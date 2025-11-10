<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Menampilkan halaman kategori khusus dengan layout berbeda
     */
    public function show(string $slug, Request $request)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->with('children')
            ->firstOrFail();

        // Query produk berdasarkan kategori (termasuk sub-kategori)
        $categoryIds = [$category->id];
        if ($category->children->count() > 0) {
            $categoryIds = array_merge($categoryIds, $category->children->pluck('id')->toArray());
        }

        $query = Product::with(['user', 'images'])
            ->available()
            ->whereIn('category_id', $categoryIds);

        // Filter tambahan
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        if ($request->filled('brand')) {
            $query->where('brand', 'ilike', "%{$request->brand}%");
        }

        // Sorting
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->orderByDesc('view_count')
                    ->orderByDesc('favorite_count');
                break;
            case 'latest':
            default:
                $query->latest('created_at');
                break;
        }

        $products = $query->paginate(20);

        $categoryImages = [
            'electronic' => 'kategorisection_electronic.png',
            'fashion' => 'kategorisection_fashion.png',
            'perawatan' => 'kategorisection_perawatan.png',
            'anakanak' => 'kategorisection_anakanak.png',
            'rumah' => 'kategorisection_rumah.png',
            'hobi' => 'kategorisection_hobi.png',
            'kendaraan' => 'kategorisection_kendaraan.png',
            'olahraga' => 'kategorisection_olahraga.png',
            'buku' => 'kategorisectionbuku.png',
            'makananminuman' => 'kategorisection_makananminuman.png',
        ];

        $slugKey = strtolower(str_replace(' ', '', $category->slug));
        $categoryImage = $categoryImages[$slugKey] ?? null;

        return view('categories.show', compact('category', 'products', 'categoryImage'));
    }
}

