@extends('layouts.app')

@section('title', $category->name . ' - Reloved')

@section('content')
<!-- Category Hero Section -->
<section class="bg-gradient-to-br from-primary-50 via-background to-primary-50 py-12 lg:py-16">
    <div class="container mx-auto px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16 items-center">
            <!-- Category Info -->
            <div class="space-y-6">
                <h1 class="text-2xl lg:text-3xl font-bold text-text-primary leading-tight">
                    Kategori {{ $category->name }}
                </h1>
                <p class="text-base lg:text-md text-text-secondary leading-relaxed">
                    {{ $category->description ?? 'Jelajahi berbagai produk ' . strtolower($category->name) . ' preloved berkualitas dengan harga terjangkau.' }}
                </p>
            </div>
            <!-- Category Image -->
            <div class="relative">
                @if($categoryImage && file_exists(public_path("images/kategorisection/{$categoryImage}")))
                    <div class="bg-white rounded-16 shadow-xl overflow-hidden">
                        <img src="{{ asset("images/kategorisection/{$categoryImage}") }}" alt="{{ $category->name }}" class="w-full h-auto">
                    </div>
                @else
                    <div class="bg-white rounded-16 shadow-xl p-12 flex items-center justify-center">
                        <svg class="w-32 h-32 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Sub Categories (if any) -->
@if($category->children->count() > 0)
<section class="bg-background py-8 border-b border-border">
    <div class="container mx-auto px-8">
        <div class="flex flex-wrap gap-3">
            <span class="text-sm font-semibold text-text-primary">Sub Kategori:</span>
            @foreach($category->children as $child)
                <a href="{{ route('products.index', ['category' => $child->id]) }}" class="px-4 py-2 rounded-8 bg-white border border-border text-sm text-text-secondary hover:bg-primary-50 hover:text-primary hover:border-primary transition">
                    {{ $child->name }}
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Products Section -->
<section class="bg-background py-12">
    <div class="container mx-auto px-8">
        <!-- Filters and Sort -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
            <div>
                <h2 class="text-xl font-bold text-text-primary mb-2">Produk {{ $category->name }}</h2>
                <p class="text-sm text-text-secondary">{{ $products->total() }} produk ditemukan</p>
            </div>
            
            <!-- Sort Options -->
            <div class="flex items-center gap-3">
                <label class="text-sm text-text-secondary">Urutkan:</label>
                <select 
                    id="sort-select"
                    class="border border-border rounded-8 px-4 py-2 text-sm text-text-primary bg-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                    onchange="window.location.href = updateQueryParam('sort', this.value)"
                >
                    <option value="latest" {{ request('sort') == 'latest' || !request('sort') ? 'selected' : '' }}>Terbaru</option>
                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga: Rendah ke Tinggi</option>
                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga: Tinggi ke Rendah</option>
                    <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Paling Populer</option>
                </select>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4 mb-8">
            @forelse($products as $product)
                <a href="{{ route('products.show', $product->id) }}" class="group bg-white rounded-8 border border-border overflow-hidden hover:shadow-lg transition-all duration-300">
                    <!-- Product Image -->
                    <div class="relative aspect-square bg-gray-100 overflow-hidden">
                        @if($product->images->count() > 0)
                            <img src="{{ $product->images->first()->cloudinary_url }}" alt="{{ $product->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Product Info -->
                    <div class="p-4 space-y-2">
                        <!-- Seller Info -->
                        <div class="flex items-center gap-2 pb-2 border-b border-border">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-light to-primary-dark flex items-center justify-center flex-shrink-0">
                                @if($product->user->avatar)
                                    <img src="{{ $product->user->avatar }}" alt="{{ $product->user->name }}" class="w-full h-full object-cover rounded-full">
                                @else
                                    <span class="text-xs font-semibold text-white">{{ substr($product->user->name, 0, 1) }}</span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-text-primary truncate">{{ $product->user->name }}</p>
                                <p class="text-xs text-text-tertiary">{{ $product->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        
                        <!-- Product Title -->
                        <h3 class="text-sm font-semibold text-text-primary line-clamp-2 group-hover:text-primary transition-colors min-h-[2.5rem]">{{ $product->title }}</h3>
                        
                        <!-- Price -->
                        <p class="text-base font-bold text-primary">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        
                        <!-- Product Details -->
                        <div class="flex flex-wrap items-center gap-2">
                            @if($product->size)
                                <span class="px-2 py-1 rounded-8 bg-gray-100 text-xs text-text-secondary font-medium">Size: {{ $product->size }}</span>
                            @endif
                            @if($product->brand)
                                <span class="px-2 py-1 rounded-8 bg-gray-100 text-xs text-text-secondary font-medium">{{ $product->brand }}</span>
                            @endif
                            <!-- Condition Badge -->
                            <span class="px-2 py-1 rounded-full bg-primary-50 text-primary text-xs font-semibold border border-primary-300">
                                @if($product->condition === 'baru')
                                    Baru
                                @elseif($product->condition === 'lumayan_baru')
                                    Lumayan Baru
                                @elseif($product->condition === 'bekas')
                                    Jarang Digunakan
                                @else
                                    Rusak
                                @endif
                            </span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-5 text-center py-12">
                    <p class="text-text-secondary">Belum ada produk di kategori ini</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $products->links() }}
        </div>
    </div>
</section>

@push('scripts')
<script>
    function updateQueryParam(key, value) {
        const url = new URL(window.location.href);
        if (value) {
            url.searchParams.set(key, value);
        } else {
            url.searchParams.delete(key);
        }
        return url.toString();
    }
</script>
@endpush
@endsection

