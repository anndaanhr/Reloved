<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewRequest;
use App\Models\Review;
use App\Models\Transaction;
use App\Services\ImageService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function __construct(
        private ImageService $imageService,
        private NotificationService $notificationService
    ) {}

    public function index(Request $request)
    {
        $query = Review::with(['reviewer', 'product', 'transaction']);

        // Filter by user
        if ($request->filled('user_id')) {
            $query->forUser($request->user_id);
        }

        // Filter by product
        if ($request->filled('product_id')) {
            $query->forProduct($request->product_id);
        }

        $reviews = $query->latest()->paginate(20);

        return view('reviews.index', compact('reviews'));
    }

    public function create(string $transactionId)
    {
        $transaction = Transaction::with(['product', 'buyer', 'seller'])
            ->findOrFail($transactionId);

        if ($transaction->buyer_id !== Auth::id() && $transaction->seller_id !== Auth::id()) {
            abort(403);
        }

        // Review hanya bisa dilakukan setelah transaksi selesai (status: paket_diterima atau selesai)
        // Dan transaksi tidak boleh dibatalkan
        if ($transaction->status === 'dibatalkan') {
            return back()->withErrors(['error' => 'Review tidak dapat dibuat untuk transaksi yang sudah dibatalkan.']);
        }

        if ($transaction->status !== 'paket_diterima' && $transaction->status !== 'selesai') {
            return back()->withErrors(['error' => 'Review hanya bisa dilakukan setelah buyer menerima pesanan (paket diterima atau transaksi selesai).']);
        }

        $existingReview = Review::where('transaction_id', $transaction->id)
            ->where('reviewer_id', Auth::id())
            ->first();

        if ($existingReview) {
            return redirect()->route('reviews.edit', $existingReview->id);
        }

        $reviewedUser = $transaction->buyer_id === Auth::id() 
            ? $transaction->seller 
            : $transaction->buyer;

        return view('reviews.create', compact('transaction', 'reviewedUser'));
    }

    public function store(ReviewRequest $request)
    {
        $transaction = Transaction::with(['product', 'buyer', 'seller'])
            ->findOrFail($request->transaction_id);

        if ($transaction->buyer_id !== Auth::id() && $transaction->seller_id !== Auth::id()) {
            abort(403);
        }

        // Review hanya bisa dilakukan setelah transaksi selesai (status: paket_diterima atau selesai)
        // Dan transaksi tidak boleh dibatalkan
        if ($transaction->status === 'dibatalkan') {
            return back()->withErrors(['error' => 'Review tidak dapat dibuat untuk transaksi yang sudah dibatalkan.']);
        }

        if ($transaction->status !== 'paket_diterima' && $transaction->status !== 'selesai') {
            return back()->withErrors(['error' => 'Review hanya bisa dilakukan setelah buyer menerima pesanan (paket diterima atau transaksi selesai).']);
        }

        $existingReview = Review::where('transaction_id', $transaction->id)
            ->where('reviewer_id', Auth::id())
            ->first();

        if ($existingReview) {
            return back()->withErrors(['error' => 'Anda sudah memberikan review untuk transaksi ini.']);
        }

        DB::beginTransaction();
        try {
            $reviewedUserId = $transaction->buyer_id === Auth::id() 
                ? $transaction->seller_id 
                : $transaction->buyer_id;

            $imageUrls = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $uploadResult = $this->imageService->uploadImage($image, 'reviews');
                    $imageUrls[] = $uploadResult['secure_url'];
                }
            }

            $review = Review::create([
                'transaction_id' => $transaction->id,
                'reviewer_id' => Auth::id(),
                'reviewed_id' => $reviewedUserId,
                'product_id' => $transaction->product_id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'images' => $imageUrls,
            ]);

            $this->updateUserRating($reviewedUserId);

            $this->notificationService->notifyNewReview(
                \App\Models\User::find($reviewedUserId),
                $review
            );

            DB::commit();

            return redirect()->route('transactions.show', $transaction->id)
                ->with('success', 'Review berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal membuat review: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(string $id)
    {
        $review = Review::with(['reviewer', 'reviewed', 'product', 'transaction'])
            ->findOrFail($id);

        return view('reviews.show', compact('review'));
    }

    public function edit(string $id)
    {
        $review = Review::with(['transaction', 'reviewed'])
            ->findOrFail($id);

        if ($review->reviewer_id !== Auth::id()) {
            abort(403);
        }

        $transaction = $review->transaction;
        $reviewedUser = $review->reviewed;

        return view('reviews.edit', compact('review', 'transaction', 'reviewedUser'));
    }

    public function update(ReviewRequest $request, string $id)
    {
        $review = Review::with('transaction')->findOrFail($id);

        if ($review->reviewer_id !== Auth::id()) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            $imageUrls = $review->images ?? [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $uploadResult = $this->imageService->uploadImage($image, 'reviews');
                    $imageUrls[] = $uploadResult['secure_url'];
                }
            }

            $review->update([
                'rating' => $request->rating,
                'comment' => $request->comment,
                'images' => $imageUrls,
            ]);

            $this->updateUserRating($review->reviewed_id);

            DB::commit();

            return redirect()->route('transactions.show', $review->transaction_id)
                ->with('success', 'Review berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal memperbarui review: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(string $id)
    {
        $review = Review::findOrFail($id);

        if ($review->reviewer_id !== Auth::id()) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            $reviewedUserId = $review->reviewed_id;
            $transactionId = $review->transaction_id;

            $review->delete();

            $this->updateUserRating($reviewedUserId);

            DB::commit();

            return redirect()->route('transactions.show', $transactionId)
                ->with('success', 'Review berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menghapus review: ' . $e->getMessage()]);
        }
    }

    private function updateUserRating(string $userId): void
    {
        $reviews = Review::where('reviewed_id', $userId)->get();
        
        if ($reviews->count() > 0) {
            $avgRating = $reviews->avg('rating');
            $reviewCount = $reviews->count();

            \App\Models\User::where('id', $userId)->update([
                'rating_avg' => round($avgRating, 2),
                'review_count' => $reviewCount,
            ]);
        } else {
            \App\Models\User::where('id', $userId)->update([
                'rating_avg' => 0,
                'review_count' => 0,
            ]);
        }
    }
}
