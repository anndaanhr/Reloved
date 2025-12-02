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

    /**
     * LIST CONVERSATIONS
     */
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
            $conversation = Conversation::with([
                'product.images',
                'buyer',
                'seller',
                'messages.sender'
            ])->find(request('id'));

            if ($conversation && $this->isUserInConversation($conversation)) {
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

    /**
     * SHOW SINGLE THREAD
     */
    public function show(string $id)
    {
        $conversation = Conversation::with([
            'product.images',
            'buyer',
            'seller',
            'messages.sender',
            'offers'
        ])->findOrFail($id);

        if (!$this->isUserInConversation($conversation)) {
            abort(403);
        }

        // Tandai pesan terbaca
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

    /**
     * START NEW CONVERSATION
     */
    public function create(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->user_id === Auth::id()) {
            return back()->withErrors(['error' => 'Tidak bisa chat dengan diri sendiri.']);
        }

        $conversation = Conversation::firstOrCreate(
            [
                'product_id' => $product->id,
                'buyer_id'   => Auth::id(),
                'seller_id'  => $product->user_id,
            ],
            [
                'last_message_at' => now(),
            ]
        );

        return redirect()->route('chat.show', $conversation->id);
    }

    /**
     * SEND MESSAGE
     */
    public function store(Request $request, string $id)
    {
        $request->validate([
            'message'      => ['required_without:offer_amount', 'string', 'nullable', 'max:1000'],
            'offer_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $conversation = Conversation::findOrFail($id);

        if (!$this->isUserInConversation($conversation)) {
            abort(403);
        }

        if (!$request->filled('message') && !$request->filled('offer_amount')) {
            return response()->json([
                'success' => false,
                'error'   => 'Pesan tidak boleh kosong.',
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Tentukan tipe pesanmessage
            $messageType = $request->filled('offer_amount') ? 'offer' : 'text';

            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id'       => Auth::id(),
                'message'         => $request->message,
                'message_type'    => $messageType,
                'offer_amount'    => $request->offer_amount,
                'offer_status'    => $messageType === 'offer' ? 'pending' : null,
            ]);

            // Update last message timestamp
            $conversation->updateLastMessageAt();

            // Broadcast real-time (fail-safe)
            try {
                if (config('broadcasting.default') !== 'null') {
                    broadcast(new MessageSent($message))->toOthers();
                }
            } catch (\Throwable $e) {
            }

            // Kirim notifikasi (hanya untuk text)
            if ($messageType === 'text') {
                $recipient = $conversation->buyer_id === Auth::id()
                    ? $conversation->seller_id
                    : $conversation->buyer_id;

                $this->notificationService->notifyNewMessage(
                    \App\Models\User::find($recipient),
                    $conversation
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message->load('sender'),
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'error'   => 'Gagal mengirim pesan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * MARK AS READ
     */
    public function markAsRead(string $id)
    {
        $conversation = Conversation::findOrFail($id);

        if (!$this->isUserInConversation($conversation)) {
            abort(403);
        }

        $conversation->messages()
            ->where('sender_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * GET BUYERS FOR A PRODUCT (SELLER VIEW)
     */
    public function getBuyersForProduct(string $productId)
    {
        $product = Product::findOrFail($productId);

        if ($product->user_id !== Auth::id()) {
            abort(403);
        }

        $conversations = Conversation::where('product_id', $product->id)
            ->with(['buyer', 'messages' => function ($query) {
                $query->where('message_type', 'offer')->latest();
            }])
            ->get();

        $buyers = $conversations->map(function ($c) {
            $latestOffer = $c->messages->first();

            return [
                'id'                 => $c->buyer_id,
                'name'               => $c->buyer->name,
                'avatar'             => $c->buyer->avatar,
                'conversation_id'    => $c->id,
                'has_offers'         => $c->messages->count() > 0,
                'latest_offer_amount'=> $latestOffer->offer_amount ?? null,
            ];
        })->unique('id')->values();

        return response()->json([
            'success' => true,
            'data'    => $buyers,
        ]);
    }

    /**
     * Check jika user termasuk dalam percakapan
     */
    private function isUserInConversation(Conversation $conversation): bool
    {
        return ($conversation->buyer_id === Auth::id() ||
                $conversation->seller_id === Auth::id());
    }
}
