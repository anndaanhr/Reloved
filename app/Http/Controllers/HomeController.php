<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman home dengan berbagai section produk
     */
    public function index()
    {
        // 1. Produk Spotlight - Algoritma scoring berdasarkan performa
        $spotlightProducts = Product::spotlight(5)->get();

        // 2. Barang Terbaru - Produk terbaru dalam 7 hari
        $latestProducts = Product::latestProducts(5)->get();

        // 3. Mungkin Kamu Suka Ini - Rekomendasi personalisasi
        $recommendedProducts = $this->getRecommendedProducts();

        return view('home', compact('spotlightProducts', 'latestProducts', 'recommendedProducts'));
    }

    /**
     * Mendapatkan produk rekomendasi berdasarkan preferensi user
     * 
     * Logika:
     * 1. Jika user login:
     *    - Prioritas 1: Kategori dari wishlist/favorit user
     *    - Prioritas 2: Kategori dari produk yang pernah dilihat (view_count)
     *    - Prioritas 3: Lokasi user (province/city)
     *    - Fallback: Rekomendasi umum
     * 
     * 2. Jika guest:
     *    - Rekomendasi umum (produk dari seller terpercaya)
     */
    private function getRecommendedProducts(int $limit = 5)
    {
        if (!Auth::check()) {
            // Guest: rekomendasi umum
            return Product::recommendedGeneral($limit)->get();
        }

        $user = Auth::user();

        try {
            // Prioritas 1: Kategori dari wishlist user
            // Note: Tabel yang digunakan adalah 'favorites', bukan 'wishlists'
            $wishlistCategoryIds = DB::table('favorites')
                ->join('products', 'favorites.product_id', '=', 'products.id')
                ->where('favorites.user_id', $user->id)
                ->whereNotNull('products.category_id')
                ->whereNull('products.deleted_at')
                ->distinct()
                ->pluck('products.category_id')
                ->toArray();

            if (!empty($wishlistCategoryIds)) {
                $products = Product::recommendedByCategory($wishlistCategoryIds, $limit)->get();
                if ($products->count() >= $limit) {
                    return $products;
                }
            }
        } catch (\Exception $e) {
            // Jika ada error (misalnya tabel wishlists belum ada), lanjut ke prioritas berikutnya
            \Log::warning('Error getting wishlist categories: ' . $e->getMessage());
        }

        // Prioritas 2: Kategori dari produk populer (untuk user baru yang belum punya wishlist)
        try {
            $viewedCategoryIds = DB::table('products')
                ->where('view_count', '>', 0)
                ->whereNotNull('category_id')
                ->whereNull('deleted_at')
                ->distinct()
                ->pluck('category_id')
                ->take(3)
                ->toArray();

            if (!empty($viewedCategoryIds)) {
                $products = Product::recommendedByCategory($viewedCategoryIds, $limit)->get();
                if ($products->count() >= $limit) {
                    return $products;
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Error getting viewed categories: ' . $e->getMessage());
        }

        // Prioritas 3: Berdasarkan lokasi user
        if ($user->province || $user->city) {
            try {
                $products = Product::recommendedByLocation($user->province, $user->city, $limit)->get();
                if ($products->count() >= $limit) {
                    return $products;
                }
            } catch (\Exception $e) {
                \Log::warning('Error getting location-based recommendations: ' . $e->getMessage());
            }
        }

        // Fallback: Rekomendasi umum
        return Product::recommendedGeneral($limit)->get();
    }
}

