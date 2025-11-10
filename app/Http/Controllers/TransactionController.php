<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Transaction;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
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

        if ($transaction->status === 'selesai' || $transaction->status === 'paket_diterima') {
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
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->user_id !== Auth::id()) {
            abort(403);
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
        $request->validate([
            'tracking_number' => ['required', 'string', 'max:255'],
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
            $transaction->markAsShipped($request->tracking_number);

            $this->notificationService->notifyTransactionShipped(
                \App\Models\User::find($transaction->buyer_id),
                $transaction
            );

            DB::commit();

            return back()->with('success', 'Status transaksi berhasil diperbarui menjadi "Barang dikirim".');
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
            // Buyer menerima paket, otomatis complete transaksi
            // Alur: paket_diterima -> selesai (untuk shipping method)
            $transaction->markAsReceived();
            $transaction->markAsCompleted();

            $this->notificationService->notifyTransactionReceived(
                \App\Models\User::find($transaction->seller_id),
                $transaction
            );

            DB::commit();

            return back()->with('success', 'Paket berhasil ditandai sebagai diterima. Transaksi selesai!');
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

        if ($transaction->status !== 'menunggu_transaksi') {
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

            DB::commit();

            return redirect()->route('transactions.index')
                ->with('success', 'Transaksi berhasil dibatalkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal membatalkan transaksi: ' . $e->getMessage()]);
        }
    }
}
