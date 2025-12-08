<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Offer;
use App\Models\Product;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OfferController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function store(Request $request, string $conversationId)
    {
        $request->validate([
            'offer_amount' => ['required', 'numeric', 'min:0'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $conversation = Conversation::with('product')->findOrFail($conversationId);

        if ($conversation->buyer_id !== Auth::id() && $conversation->seller_id !== Auth::id()) {
            abort(403);
        }

        if ($conversation->buyer_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'error' => 'Hanya pembeli yang bisa membuat tawaran.',
            ], 403);
        }

        $product = $conversation->product;
        if (!$product) {
            return response()->json([
                'success' => false,
                'error' => 'Produk tidak ditemukan.',
            ], 404);
        }

        if ($request->offer_amount >= $product->price) {
            return response()->json([
                'success' => false,
                'error' => 'Tawaran harus lebih rendah dari harga produk.',
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Update atau create offer (jika buyer sudah pernah offer, update amount-nya)
            $offer = Offer::updateOrCreate(
                [
                    'conversation_id' => $conversation->id,
                    'product_id' => $product->id,
                    'buyer_id' => $conversation->buyer_id,
                    'seller_id' => $conversation->seller_id,
                    'status' => 'pending',
                ],
                [
                    'amount' => $request->offer_amount,
                    'counter_count' => 0,
                ]
            );

            // Buat message dengan type 'offer' untuk ditampilkan di chat
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => Auth::id(),
                'message' => $request->message ?? "Saya menawar harga Rp " . number_format($request->offer_amount, 0, ',', '.'),
                'message_type' => 'offer',
                'offer_amount' => $request->offer_amount,
                'offer_status' => 'pending',
                'offer_counter_count' => 0,
                'is_read' => false,
            ]);

            $conversation->updateLastMessageAt();

            try {
                if (config('broadcasting.default') !== 'null') {
                    broadcast(new MessageSent($message))->toOthers();
                }
            } catch (\Exception $e) {
                // Broadcasting not available
            }

            $this->notificationService->notifyNewOffer(
                \App\Models\User::find($conversation->seller_id),
                $offer
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message->load('sender'),
                'offer' => $offer,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Gagal membuat tawaran: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function accept(Request $request, string $conversationId, string $messageId)
    {
        $message = Message::with('conversation')->findOrFail($messageId);
        
        if ($message->conversation_id !== $conversationId || $message->message_type !== 'offer') {
            abort(404);
        }

        $conversation = $message->conversation;
        
        // Find offer by conversation_id (not by amount, because amount changes after counter)
        $offer = Offer::where('conversation_id', $conversationId)
            ->where('product_id', $conversation->product_id)
            ->where('buyer_id', $conversation->buyer_id)
            ->where('seller_id', $conversation->seller_id)
            ->first();

        if (!$offer) {
            return response()->json([
                'success' => false,
                'error' => 'Tawaran tidak ditemukan.',
            ], 404);
        }

        // Check authorization: 
        // - Seller can accept buyer's initial offer or buyer's counter offer
        // - Buyer can accept seller's counter offer
        $isSeller = $offer->seller_id === Auth::id();
        $isBuyer = $offer->buyer_id === Auth::id();
        $isCounterOffer = $message->offer_status === 'counter_offer';
        
        if (!$isSeller && !$isBuyer) {
            abort(403, 'Anda tidak memiliki akses untuk menerima tawaran ini.');
        }

        // If it's a counter offer from seller, only buyer can accept
        if ($isCounterOffer && $message->sender_id === $offer->seller_id && !$isBuyer) {
            abort(403, 'Hanya pembeli yang bisa menerima tawaran balik dari penjual.');
        }
        
        // If it's an offer from buyer, only seller can accept
        if ($message->offer_status === 'pending' && $message->sender_id === $offer->buyer_id && !$isSeller) {
            abort(403, 'Hanya penjual yang bisa menerima tawaran dari pembeli.');
        }

        // Update message status to accepted
        $message->update(['offer_status' => 'accepted']);

        DB::beginTransaction();
        try {
            $offer->update(['status' => 'accepted']);

            // Buat message notifikasi bahwa offer diterima
            $acceptedMessage = Message::create([
                'conversation_id' => $offer->conversation_id,
                'sender_id' => Auth::id(),
                'message' => "Saya menerima tawaran Rp " . number_format($message->offer_amount, 0, ',', '.'),
                'message_type' => 'text',
                'is_read' => false,
            ]);

            // Reject semua offer lainnya untuk conversation ini (pending, counter_offer, dll)
            // Hanya satu offer yang bisa diterima per conversation
            Offer::where('conversation_id', $offer->conversation_id)
                ->where('id', '!=', $offer->id)
                ->whereIn('status', ['pending', 'counter_offer'])
                ->update(['status' => 'rejected']);
            
            // Also update all related messages to rejected
            Message::where('conversation_id', $offer->conversation_id)
                ->where('message_type', 'offer')
                ->where('id', '!=', $messageId)
                ->whereIn('offer_status', ['pending', 'counter_offer'])
                ->update(['offer_status' => 'rejected']);

            $offer->conversation->updateLastMessageAt();

            try {
                if (config('broadcasting.default') !== 'null') {
                    broadcast(new MessageSent($acceptedMessage))->toOthers();
                }
            } catch (\Exception $e) {
                // Broadcasting not available
            }

            // Notify the other party
            $recipientId = $isSeller ? $offer->buyer_id : $offer->seller_id;
            $this->notificationService->notifyOfferAccepted(
                \App\Models\User::find($recipientId),
                $offer
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tawaran diterima!',
                'offer' => $offer->fresh(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error accepting offer: ' . $e->getMessage(), [
                'exception' => $e,
                'conversation_id' => $conversationId,
                'message_id' => $messageId,
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Gagal menerima tawaran: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function reject(Request $request, string $conversationId, string $messageId)
    {
        $message = Message::with('conversation')->findOrFail($messageId);
        
        if ($message->conversation_id !== $conversationId || $message->message_type !== 'offer') {
            abort(404);
        }

        $offer = Offer::where('conversation_id', $conversationId)
            ->where('amount', $message->offer_amount)
            ->firstOrFail();

        if ($offer->seller_id !== Auth::id()) {
            abort(403);
        }

        if ($offer->conversation_id !== $conversationId) {
            abort(404);
        }

        DB::beginTransaction();
        try {
            $offer->update(['status' => 'rejected']);

            $message = Message::create([
                'conversation_id' => $offer->conversation_id,
                'sender_id' => Auth::id(),
                'message' => "Saya menolak tawaran Rp " . number_format($offer->amount, 0, ',', '.'),
                'message_type' => 'text',
                'is_read' => false,
            ]);

            $offer->conversation->updateLastMessageAt();

            try {
                if (config('broadcasting.default') !== 'null') {
                    broadcast(new MessageSent($message))->toOthers();
                }
            } catch (\Exception $e) {
                // Broadcasting not available
            }

            $this->notificationService->notifyOfferRejected(
                \App\Models\User::find($offer->buyer_id),
                $offer
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tawaran ditolak.',
                'offer' => $offer->fresh(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Gagal menolak tawaran: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function counter(Request $request, string $conversationId, string $messageId)
    {
        $request->validate([
            'counter_amount' => ['required', 'numeric', 'min:0'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $message = Message::with('conversation')->findOrFail($messageId);
        
        if ($message->conversation_id !== $conversationId || $message->message_type !== 'offer') {
            abort(404);
        }

        $offer = Offer::with('product')->where('conversation_id', $conversationId)
            ->where('amount', $message->offer_amount)
            ->firstOrFail();

        if ($offer->seller_id !== Auth::id()) {
            abort(403);
        }

        if ($offer->conversation_id !== $conversationId) {
            abort(404);
        }

        // Validasi: cek apakah masih bisa counter (maksimal MAX_COUNTER_COUNT kali)
        if (!$offer->canCounter()) {
            return response()->json([
                'success' => false,
                'error' => 'Batas tawaran sudah tercapai (maksimal ' . Offer::MAX_COUNTER_COUNT . ' kali).',
            ], 400);
        }

        $product = $offer->product;
        // Validasi: counter amount harus < harga produk
        if ($request->counter_amount >= $product->price) {
            return response()->json([
                'success' => false,
                'error' => 'Tawaran balik harus lebih rendah dari harga produk.',
            ], 400);
        }

        // Validasi: counter amount harus > offer sebelumnya (seller harus naikkan harga)
        if ($request->counter_amount <= $offer->amount) {
            return response()->json([
                'success' => false,
                'error' => 'Tawaran balik harus lebih tinggi dari tawaran sebelumnya.',
            ], 400);
        }

        DB::beginTransaction();
        try {
            $offer->update([
                'status' => 'counter_offer',
                'amount' => $request->counter_amount,
            ]);
            $offer->incrementCounter();

            $message = Message::create([
                'conversation_id' => $offer->conversation_id,
                'sender_id' => Auth::id(),
                'message' => $request->message ?? "Saya menawar balik Rp " . number_format($request->counter_amount, 0, ',', '.') . " (Tawaran ke-" . ($offer->counter_count + 1) . " dari " . Offer::MAX_COUNTER_COUNT . ")",
                'message_type' => 'offer',
                'offer_amount' => $request->counter_amount,
                'offer_status' => 'counter_offer',
                'offer_counter_count' => $offer->counter_count,
                'is_read' => false,
            ]);

            $offer->conversation->updateLastMessageAt();

            // Broadcast message (disabled if Redis not available)
            try {
                if (config('broadcasting.default') !== 'null') {
                    broadcast(new MessageSent($message))->toOthers();
                }
            } catch (\Exception $e) {
                // Silently fail if broadcasting is not available (e.g., no Redis)
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message->load('sender'),
                'offer' => $offer->fresh(),
                'counter_count' => $offer->counter_count,
                'max_counter' => Offer::MAX_COUNTER_COUNT,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Gagal membuat tawaran balik: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Buyer counter offer after seller counter
     */
    public function buyerCounter(Request $request, string $conversationId, string $messageId)
    {
        $request->validate([
            'offer_amount' => ['required', 'numeric', 'min:0'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $message = Message::with('conversation')->findOrFail($messageId);
        
        if ($message->conversation_id !== $conversationId || $message->message_type !== 'offer') {
            abort(404);
        }

        $conversation = $message->conversation;
        if ($conversation->buyer_id !== Auth::id()) {
            abort(403, 'Hanya pembeli yang bisa membuat tawaran.');
        }

        $product = $conversation->product;
        if (!$product) {
            return response()->json([
                'success' => false,
                'error' => 'Produk tidak ditemukan.',
            ], 404);
        }

        if ($request->offer_amount >= $product->price) {
            return response()->json([
                'success' => false,
                'error' => 'Tawaran harus lebih rendah dari harga produk.',
            ], 400);
        }

        // Find existing offer
        $offer = Offer::where('conversation_id', $conversationId)
            ->where('product_id', $product->id)
            ->where('buyer_id', $conversation->buyer_id)
            ->where('seller_id', $conversation->seller_id)
            ->first();

        if (!$offer) {
            return response()->json([
                'success' => false,
                'error' => 'Tawaran tidak ditemukan.',
            ], 404);
        }

        // Check if can counter
        if (!$offer->canCounter()) {
            return response()->json([
                'success' => false,
                'error' => 'Batas tawaran sudah tercapai (maksimal ' . Offer::MAX_COUNTER_COUNT . ' kali).',
            ], 400);
        }

        DB::beginTransaction();
        try {
            $offer->update([
                'status' => 'pending',
                'amount' => $request->offer_amount,
            ]);
            $offer->incrementCounter();

            $newMessage = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => Auth::id(),
                'message' => $request->message ?? "Saya menawar harga Rp " . number_format($request->offer_amount, 0, ',', '.') . " (Tawaran ke-" . ($offer->counter_count + 1) . " dari " . Offer::MAX_COUNTER_COUNT . ")",
                'message_type' => 'offer',
                'offer_amount' => $request->offer_amount,
                'offer_status' => 'pending',
                'offer_counter_count' => $offer->counter_count,
                'is_read' => false,
            ]);

            $conversation->updateLastMessageAt();

            try {
                if (config('broadcasting.default') !== 'null') {
                    broadcast(new MessageSent($newMessage))->toOthers();
                }
            } catch (\Exception $e) {
                // Broadcasting not available
            }

            $this->notificationService->notifyNewOffer(
                \App\Models\User::find($conversation->seller_id),
                $offer
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $newMessage->load('sender'),
                'offer' => $offer->fresh(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Gagal membuat tawaran: ' . $e->getMessage(),
            ], 500);
        }
    }
}
