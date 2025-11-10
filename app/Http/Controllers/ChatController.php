<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Product;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function index()
    {
        $conversations = Conversation::with(['product.images', 'buyer', 'seller'])
            ->forUser(Auth::id())
            ->orderBy('last_message_at', 'desc')
            ->get();
        
        $conversations->load(['messages' => function ($query) {
            $query->latest()->limit(1);
        }]);

        $conversation = null;
        if (request()->has('id')) {
            $conversation = Conversation::with(['product.images', 'buyer', 'seller', 'messages.sender'])
                ->find(request('id'));
            
            if ($conversation && ($conversation->buyer_id === Auth::id() || $conversation->seller_id === Auth::id())) {
                $conversation->messages()
                    ->where('sender_id', '!=', Auth::id())
                    ->where('is_read', false)
                    ->update(['is_read' => true]);
            } else {
                $conversation = null;
            }
        }

        return view('chat.index', compact('conversations', 'conversation'));
    }

    public function show(string $id)
    {
        $conversation = Conversation::with(['product.images', 'buyer', 'seller', 'messages.sender', 'offers'])
            ->findOrFail($id);

        if ($conversation->buyer_id !== Auth::id() && $conversation->seller_id !== Auth::id()) {
            abort(403);
        }

        $conversation->messages()
            ->where('sender_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $conversations = Conversation::with(['product.images', 'buyer', 'seller'])
            ->forUser(Auth::id())
            ->orderBy('last_message_at', 'desc')
            ->get();
        
        $conversations->load(['messages' => function ($query) {
            $query->latest()->limit(1);
        }]);

        return view('chat.show', compact('conversation', 'conversations'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->user_id === Auth::id()) {
            return back()->withErrors(['error' => 'Anda tidak bisa chat dengan diri sendiri.']);
        }

        $conversation = Conversation::firstOrCreate(
            [
                'product_id' => $product->id,
                'buyer_id' => Auth::id(),
                'seller_id' => $product->user_id,
            ],
            [
                'last_message_at' => now(),
            ]
        );

        return redirect()->route('chat.show', $conversation->id);
    }

    public function store(Request $request, string $id)
    {
        $request->validate([
            'message' => ['required_without:offer_amount', 'string', 'max:1000'],
            'offer_amount' => ['sometimes', 'nullable', 'numeric', 'min:0'],
        ]);

        $conversation = Conversation::findOrFail($id);

        if ($conversation->buyer_id !== Auth::id() && $conversation->seller_id !== Auth::id()) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            // Tentukan message type: 'offer' jika ada offer_amount, 'text' jika tidak
            $messageType = $request->filled('offer_amount') ? 'offer' : 'text';
            
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => Auth::id(),
                'message' => $request->message,
                'message_type' => $messageType,
                'offer_amount' => $request->offer_amount,
                'offer_status' => $messageType === 'offer' ? 'pending' : null,
            ]);

            // Update last_message_at untuk sorting di conversation list
            $conversation->updateLastMessageAt();

            // Broadcast message untuk real-time (jika Redis tersedia)
            try {
                if (config('broadcasting.default') !== 'null') {
                    broadcast(new MessageSent($message))->toOthers();
                }
            } catch (\Exception $e) {
                // Broadcasting not available (no Redis)
            }

            // Kirim notifikasi hanya untuk text message, bukan offer
            // Offer akan di-handle oleh OfferController
            if ($messageType === 'text') {
                $recipientId = $conversation->buyer_id === Auth::id() 
                    ? $conversation->seller_id 
                    : $conversation->buyer_id;
                $this->notificationService->notifyNewMessage(
                    \App\Models\User::find($recipientId),
                    $conversation
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message->load('sender'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Gagal mengirim pesan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function markAsRead(string $id)
    {
        $conversation = Conversation::findOrFail($id);

        if ($conversation->buyer_id !== Auth::id() && $conversation->seller_id !== Auth::id()) {
            abort(403);
        }

        $conversation->messages()
            ->where('sender_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function getBuyersForProduct(string $productId)
    {
        $product = Product::findOrFail($productId);

        if ($product->user_id !== Auth::id()) {
            abort(403);
        }

        $conversations = Conversation::where('product_id', $product->id)
            ->with(['buyer', 'messages' => function ($query) {
                $query->where('message_type', 'offer')
                    ->orderBy('created_at', 'desc');
            }])
            ->get();

        $buyers = $conversations->map(function ($conversation) {
            $latestOffer = $conversation->messages->first();
            return [
                'id' => $conversation->buyer_id,
                'name' => $conversation->buyer->name,
                'avatar' => $conversation->buyer->avatar,
                'conversation_id' => $conversation->id,
                'has_offers' => $conversation->messages->where('message_type', 'offer')->count() > 0,
                'latest_offer_amount' => $latestOffer ? $latestOffer->offer_amount : null,
            ];
        })->unique('id')->values();

        return response()->json(['success' => true, 'data' => $buyers]);
    }
}
