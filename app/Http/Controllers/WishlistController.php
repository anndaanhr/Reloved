<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = Wishlist::with(['product.images', 'product.user', 'product.category'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(12);

        return view('wishlist.index', compact('wishlists'));
    }

    public function toggle(Request $request, string $productId)
    {
        $product = Product::findOrFail($productId);

        $wishlist = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->first();

        DB::beginTransaction();
        try {
            if ($wishlist) {
                $wishlist->delete();
                $product->decrement('favorite_count');
                $isFavorite = false;
            } else {
                Wishlist::create([
                    'user_id' => Auth::id(),
                    'product_id' => $productId,
                ]);
                $product->increment('favorite_count');
                $isFavorite = true;
            }

            DB::commit();

            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'is_favorite' => $isFavorite,
                    'favorite_count' => $product->fresh()->favorite_count,
                ]);
            }

            return back()->with('success', $isFavorite ? 'Produk ditambahkan ke favorit' : 'Produk dihapus dari favorit');
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Gagal mengupdate favorit: ' . $e->getMessage(),
                ], 500);
            }

            return back()->withErrors(['error' => 'Gagal mengupdate favorit: ' . $e->getMessage()]);
        }
    }

    public function destroy(string $id)
    {
        $wishlist = Wishlist::findOrFail($id);

        if ($wishlist->user_id !== Auth::id()) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            $product = $wishlist->product;
            $wishlist->delete();
            $product->decrement('favorite_count');

            DB::commit();
            return back()->with('success', 'Produk dihapus dari favorit');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menghapus dari favorit: ' . $e->getMessage()]);
        }
    }

    public function check(string $productId)
    {
        $isFavorite = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->exists();

        return response()->json([
            'success' => true,
            'is_favorite' => $isFavorite,
        ]);
    }
}
