<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Transaction;
use App\Services\NotificationService;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function __construct(
        private NotificationService $notificationService,
        private ChatService $chatService
    ) {}

    public function index()
    {
        $transactions = Transaction::with(['product.images', 'buyer', 'seller'])
            ->where(function ($query) {
                $query->where('buyer_id', Auth::id())
                    ->orWhere('seller_id', Auth::id());
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('transactions.index', compact('transactions'));
    }

    public function show(string $id)
    {
        $transaction = Transaction::with(['product.images', 'buyer', 'seller'])
            ->findOrFail($id);

        if ($transaction->buyer_id !== Auth::id() && $transaction->seller_id !== Auth::id()) {
            abort(403);
        }

        $canReview = false;
        $existingReview = null;
        $reviewedUser = null;

        // Review hanya bisa dilakukan setelah buyer menerima pesanan (status: paket_diterima atau selesai)
        // Dan transaksi tidak boleh dibatalkan
        if ($transaction->status !== 'dibatalkan' 
            && ($transaction->status === 'paket_diterima' || $transaction->status === 'selesai')) {
            $canReview = true;
            $existingReview = \App\Models\Review::where('transaction_id', $transaction->id)
                ->where('reviewer_id', Auth::id())
                ->first();
            
            $reviewedUser = $transaction->buyer_id === Auth::id() 
                ? $transaction->seller 
                : $transaction->buyer;
        }

        $reviews = \App\Models\Review::with(['reviewer'])
            ->where('transaction_id', $transaction->id)
            ->get();

        return view('transactions.show', compact('transaction', 'canReview', 'existingReview', 'reviewedUser', 'reviews'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'buyer_id' => ['required', 'exists:users,id'],
            'deal_method' => ['required', 'in:meetup,shipping'],
            'price' => ['required', 'numeric', 'min:0'],
            'meetup_location' => ['required_if:deal_method,meetup', 'nullable', 'string'],
            'shipping_cost' => ['required_if:deal_method,shipping', 'nullable', 'numeric', 'min:0'],
            'shipping_courier' => ['required_if:deal_method,shipping', 'nullable', 'string'],
            'shipping_service' => ['required_if:deal_method,shipping', 'nullable', 'string'],
            'origin_city_id' => ['required_if:deal_method,shipping', 'nullable', 'string'],
            'origin_city_name' => ['required_if:deal_method,shipping', 'nullable', 'string'],
            'destination_city_id' => ['required_if:deal_method,shipping', 'nullable', 'string'],
            'destination_city_name' => ['required_if:deal_method,shipping', 'nullable', 'string'],
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->user_id !== Auth::id()) {
            abort(403);
        }

        // Validasi: buyer tidak boleh sama dengan seller
        if ($request->buyer_id === Auth::id()) {
            return back()->withErrors(['error' => 'Tidak bisa membuat transaksi untuk diri sendiri.']);
        }

        // Validasi: buyer harus sudah pernah chat atau offer untuk produk ini
        $hasConversationOrOffer = Conversation::where('product_id', $product->id)
            ->where('buyer_id', $request->buyer_id)
            ->where('seller_id', Auth::id())
            ->exists();

        if (!$hasConversationOrOffer) {
            return back()->withErrors(['error' => 'Buyer harus sudah pernah chat atau offer produk ini terlebih dahulu.']);
        }

        if ($product->status !== 'active' || $product->stock <= 0) {
            return back()->withErrors(['error' => 'Produk tidak tersedia atau sudah habis.']);
        }

        DB::beginTransaction();
        try {
            // Buat transaksi baru
            // Meetup: langsung selesai, Shipping: menunggu konfirmasi pengiriman
            $transaction = Transaction::create([
                'product_id' => $product->id,
                'buyer_id' => $request->buyer_id,
                'seller_id' => Auth::id(),
                'price' => $request->price,
                'deal_method' => $request->deal_method,
                'status' => $request->deal_method === 'meetup' ? 'selesai' : 'menunggu_transaksi',
                'meetup_location' => $request->meetup_location,
                'shipping_cost' => $request->shipping_cost,
                'shipping_courier' => $request->shipping_courier,
                'shipping_service' => $request->shipping_service,
                'origin_city_id' => $request->origin_city_id,
                'origin_city_name' => $request->origin_city_name,
                'destination_city_id' => $request->destination_city_id,
                'destination_city_name' => $request->destination_city_name,
                'seller_confirmed_at' => now(),
                'completed_at' => $request->deal_method === 'meetup' ? now() : null,
            ]);

            // Kurangi stock produk
            $product->decrementStock();

            // Meetup: langsung mark as completed (tidak perlu tracking)
            if ($request->deal_method === 'meetup') {
                $transaction->markAsCompleted();
            }

            $this->notificationService->notifyTransactionCreated(
                \App\Models\User::find($transaction->buyer_id),
                $transaction
            );

            DB::commit();

            return redirect()->route('transactions.show', $transaction->id)
                ->with('success', 'Transaksi berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal membuat transaksi: ' . $e->getMessage()]);
        }
    }

    public function updateShipping(Request $request, string $id)
    {
        // Tracking number is optional - seller will send it via chat
        $request->validate([
            'tracking_number' => ['nullable', 'string', 'max:255'],
        ]);

        $transaction = Transaction::findOrFail($id);

        if ($transaction->seller_id !== Auth::id()) {
            abort(403);
        }

        if (!$transaction->canBeShipped()) {
            return back()->withErrors(['error' => 'Transaksi tidak dapat dikirim.']);
        }

        DB::beginTransaction();
        try {
            // Update status to "barang_dikirim" without requiring tracking number
            $transaction->update([
                'status' => 'barang_dikirim',
                'tracking_number' => $request->tracking_number, // Optional - can be null
                'shipping_confirmed_at' => now(),
            ]);

            // Refresh transaction to get latest data
            $transaction->refresh();

            // Send automatic receipt message to chat if tracking number is provided
            if ($request->tracking_number) {
                try {
                    $this->chatService->sendShippingReceipt($transaction);
                } catch (\Exception $e) {
                    // Log error but don't fail the transaction update
                    \Log::error('Failed to send shipping receipt message: ' . $e->getMessage());
                }
            }

            $this->notificationService->notifyTransactionShipped(
                \App\Models\User::find($transaction->buyer_id),
                $transaction
            );

            DB::commit();

            $successMessage = 'Status transaksi berhasil diperbarui menjadi "Barang dikirim".';
            if ($request->tracking_number) {
                $successMessage .= ' Informasi resi telah otomatis dikirim ke chat.';
            } else {
                $successMessage .= ' Silakan kirim nomor resi melalui chat kepada pembeli.';
            }

            return back()->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal memperbarui status: ' . $e->getMessage()]);
        }
    }

    public function markAsReceived(string $id)
    {
        $transaction = Transaction::findOrFail($id);

        if ($transaction->buyer_id !== Auth::id()) {
            abort(403);
        }

        if (!$transaction->canBeReceived()) {
            return back()->withErrors(['error' => 'Transaksi tidak dapat ditandai sebagai diterima.']);
        }

        DB::beginTransaction();
        try {
            // Buyer menerima paket, status menjadi "paket_diterima"
            // Transaksi belum selesai, buyer bisa review dulu
            $transaction->markAsReceived();

            $this->notificationService->notifyTransactionReceived(
                \App\Models\User::find($transaction->seller_id),
                $transaction
            );

            DB::commit();

            return back()->with('success', 'Pesanan berhasil diterima! Silakan berikan review untuk melanjutkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal memperbarui status: ' . $e->getMessage()]);
        }
    }

    public function cancel(string $id)
    {
        $transaction = Transaction::findOrFail($id);

        if ($transaction->buyer_id !== Auth::id() && $transaction->seller_id !== Auth::id()) {
            abort(403);
        }

        // Validasi: transaksi yang sudah selesai atau dibatalkan tidak bisa dibatalkan lagi
        if ($transaction->status === 'selesai' || $transaction->status === 'dibatalkan') {
            return back()->withErrors(['error' => 'Transaksi yang sudah selesai atau dibatalkan tidak dapat dibatalkan lagi.']);
        }

        // Bisa dibatalkan jika status masih "menunggu_transaksi" atau buyer membatalkan saat "barang_dikirim"
        if ($transaction->status === 'menunggu_transaksi') {
            // Seller atau buyer bisa cancel
        } elseif ($transaction->status === 'barang_dikirim' && $transaction->buyer_id === Auth::id()) {
            // Hanya buyer yang bisa cancel saat barang sudah dikirim
        } else {
            return back()->withErrors(['error' => 'Transaksi tidak dapat dibatalkan.']);
        }

        DB::beginTransaction();
        try {
            $transaction->update(['status' => 'dibatalkan']);

            // Restore stock produk jika transaksi dibatalkan
            $product = $transaction->product;
            $product->increment('stock');
            // Jika produk status 'sold' dan stock > 0, kembalikan ke 'active'
            if ($product->status === 'sold' && $product->stock > 0) {
                $product->update(['status' => 'active']);
            }

            // Tentukan siapa yang membatalkan
            $cancelledBy = $transaction->buyer_id === Auth::id() ? 'buyer' : 'seller';
            
            // Kirim notifikasi ke pihak yang tidak membatalkan
            if ($cancelledBy === 'buyer') {
                // Buyer membatalkan, notifikasi ke seller
                $this->notificationService->notifyTransactionCancelled(
                    \App\Models\User::find($transaction->seller_id),
                    $transaction,
                    'buyer'
                );
            } else {
                // Seller membatalkan, notifikasi ke buyer
                $this->notificationService->notifyTransactionCancelled(
                    \App\Models\User::find($transaction->buyer_id),
                    $transaction,
                    'seller'
                );
            }

            DB::commit();

            return redirect()->route('transactions.index')
                ->with('success', 'Transaksi berhasil dibatalkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal membatalkan transaksi: ' . $e->getMessage()]);
        }
    }
}
