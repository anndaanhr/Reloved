<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function __construct(
        private ImageService $imageService
    ) {}

    public function index(Request $request)
    {
        $query = Product::with(['user', 'category', 'images'])
            ->available()
            ->latest();

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // Filter by location (province)
        if ($request->filled('province')) {
            $query->byProvince($request->province);
        }
        
        // Filter by location (city) - for sidebar filter
        if ($request->filled('city')) {
            $query->byLocation($request->city, $request->province);
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by condition
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        // Filter by brand
        if ($request->filled('brand')) {
            $query->where('brand', 'ilike', "%{$request->brand}%");
        }

        $products = $query->paginate(20);

        $categories = Category::active()->root()->with('children')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::active()->root()->with('children')->get();
        
        return view('products.create', compact('categories'));
    }

    public function store(ProductRequest $request)
    {
        DB::beginTransaction();
        try {
            $product = Product::create([
                'user_id' => Auth::id(),
                'category_id' => $request->category_id,
                'title' => $request->title,
                'description' => $request->description,
                'price' => $request->price,
                'condition' => $request->condition,
                'brand' => $request->brand,
                'size' => $request->size,
                'model' => $request->model,
                'stock' => $request->stock,
                'deal_method' => $request->deal_method,
                'status' => 'active',
            ]);

            // Upload gambar produk ke Cloudinary
            // Gambar pertama otomatis menjadi primary image
            $primarySet = false;
            foreach ($request->file('images') as $index => $image) {
                $uploadResult = $this->imageService->uploadProductImage($image, $index);
                
                ProductImage::create([
                    'product_id' => $product->id,
                    'cloudinary_public_id' => $uploadResult['cloudinary_public_id'],
                    'cloudinary_url' => $uploadResult['cloudinary_url'],
                    'is_primary' => !$primarySet, // Gambar pertama = primary
                    'order' => $index,
                ]);

                if (!$primarySet) {
                    $primarySet = true;
                }
            }

            DB::commit();

            return redirect()->route('products.show', $product->id)
                ->with('success', 'Produk berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal membuat produk: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(string $id)
    {
        $product = Product::with(['user', 'category', 'images'])
            ->findOrFail($id);

        // Increment view count untuk tracking popularitas produk
        $product->incrementViewCount();

        // Cek apakah user adalah seller dan bisa mark as sold
        $canMarkAsSold = Auth::check() && Auth::id() === $product->user_id && $product->status === 'active';

        // Ambil daftar buyer yang pernah chat atau offer (untuk fitur "Mark as Sold")
        $buyers = [];
        if ($canMarkAsSold) {
            $conversations = \App\Models\Conversation::where('product_id', $product->id)
                ->with(['buyer', 'messages' => function ($query) {
                    $query->where('message_type', 'offer')
                        ->orderBy('created_at', 'desc');
                }])
                ->get();

            // Map conversations ke array buyers dengan info offer terbaru
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
            })->unique('id')->values(); // Remove duplicate buyers
        }

        $product->load(['user.products' => function ($query) use ($product) {
            $query->where('id', '!=', $product->id)
                ->available()
                ->with('images')
                ->limit(4);
        }]);

        $reviews = \App\Models\Review::with(['reviewer'])
            ->where('product_id', $product->id)
            ->latest()
            ->limit(5)
            ->get();

        $isFavorite = false;
        if (Auth::check()) {
            $isFavorite = $product->isFavoriteBy(Auth::id());
        }

        return view('products.show', compact('product', 'canMarkAsSold', 'buyers', 'reviews', 'isFavorite'));
    }

    public function edit(string $id)
    {
        $product = Product::with(['images'])->findOrFail($id);

        if (Auth::id() !== $product->user_id) {
            abort(403);
        }

        $categories = Category::active()->root()->with('children')->get();

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(ProductRequest $request, string $id)
    {
        $product = Product::findOrFail($id);

        if (Auth::id() !== $product->user_id) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            $product->update([
                'category_id' => $request->category_id,
                'title' => $request->title,
                'description' => $request->description,
                'price' => $request->price,
                'condition' => $request->condition,
                'brand' => $request->brand,
                'size' => $request->size,
                'model' => $request->model,
                'stock' => $request->stock,
                'deal_method' => $request->deal_method,
            ]);

            // Tambah gambar baru (jika ada)
            // Order dimulai dari max order yang ada + 1, agar tidak konflik dengan gambar lama
            if ($request->hasFile('images')) {
                $existingImages = $product->images()->count();
                $maxOrder = $existingImages > 0 ? $product->images()->max('order') : -1;

                foreach ($request->file('images') as $index => $image) {
                    $uploadResult = $this->imageService->uploadProductImage($image, $maxOrder + $index + 1);
                    
                    ProductImage::create([
                        'product_id' => $product->id,
                        'cloudinary_public_id' => $uploadResult['cloudinary_public_id'],
                        'cloudinary_url' => $uploadResult['cloudinary_url'],
                        'is_primary' => false, // Gambar baru tidak otomatis jadi primary
                        'order' => $maxOrder + $index + 1,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('products.show', $product->id)
                ->with('success', 'Produk berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal memperbarui produk: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);

        if (Auth::id() !== $product->user_id) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            // Hapus semua gambar dari Cloudinary sebelum soft delete produk
            foreach ($product->images as $image) {
                $this->imageService->deleteImage($image->cloudinary_public_id);
            }

            // Soft delete: produk tidak benar-benar dihapus, hanya ditandai sebagai deleted
            $product->delete();

            DB::commit();

            return redirect()->route('products.index')
                ->with('success', 'Produk berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menghapus produk: ' . $e->getMessage()]);
        }
    }

    public function deleteImage(string $id, string $imageId)
    {
        $product = Product::findOrFail($id);

        if (Auth::id() !== $product->user_id) {
            abort(403);
        }

        $image = ProductImage::where('product_id', $product->id)
            ->findOrFail($imageId);

        // Validasi: produk harus punya minimal 1 gambar
        if ($product->images()->count() <= 1) {
            return back()->withErrors(['error' => 'Produk harus memiliki minimal 1 gambar.']);
        }

        DB::beginTransaction();
        try {
            $this->imageService->deleteImage($image->cloudinary_public_id);
            $image->delete();

            // Jika gambar yang dihapus adalah primary, set gambar pertama sebagai primary baru
            if ($image->is_primary) {
                $firstImage = $product->images()->first();
                if ($firstImage) {
                    $firstImage->update(['is_primary' => true]);
                }
            }

            DB::commit();

            return back()->with('success', 'Gambar berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menghapus gambar: ' . $e->getMessage()]);
        }
    }
}
