@extends('layouts.app')

@section('title', 'Home - Reloved')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-background via-background to-primary-50 py-12 lg:py-16" style="background: linear-gradient(178deg, rgba(13, 148, 136, 0.1) 0%, rgba(84, 213, 192, 0.05) 50%, rgba(13, 148, 136, 0.1) 100%);">
    <div class="container mx-auto px-8 lg:px-28">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16 items-center">
            <!-- Hero Content -->
            <div class="space-y-6 animate-fade-in">
                <h1 class="text-2xl lg:text-3xl font-bold text-text-primary leading-tight">
                    Beli & Jual Barang<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-primary-dark">Preloved</span><br>
                    Lebih Mudah
                </h1>
                <p class="text-base lg:text-md text-text-secondary leading-relaxed">
                    Platform C2C untuk jual beli barang preloved.<br>
                    Chat langsung dengan penjual, negosiasi harga,<br>
                    dan deal sesuai kesepakatan.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('products.index') }}" class="bg-primary text-white px-6 py-3 rounded-8 font-semibold text-sm hover:opacity-90 transition shadow-md hover:shadow-lg">
                        Cari Barang
                    </a>
                    <a href="{{ route('products.create') }}" class="bg-white text-primary border border-primary px-6 py-3 rounded-8 font-semibold text-sm hover:bg-primary-50 transition shadow-md hover:shadow-lg">
                        Jual Sekarang
                    </a>
                </div>
            </div>
            <!-- Hero Image -->
            <div class="relative animate-fade-in-right">
                <div class="bg-white rounded-16 shadow-xl overflow-hidden">
                    <img src="{{ asset('images/herosection.png') }}" alt="Hero Section" class="w-full h-auto">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Spotlight Products Section -->
<section class="bg-background py-12">
    <div class="container mx-auto px-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-xl font-bold text-text-primary mb-2">Produk Spotlight</h2>
                <p class="text-sm text-text-secondary">Barang preloved pilihan dari seller terpercaya</p>
            </div>
            <a href="{{ route('products.index') }}" class="flex items-center gap-1 text-sm text-primary font-semibold hover:opacity-80 transition group">
                <span>Lihat Semua</span>
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
        
        <!-- Products Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            @forelse($spotlightProducts as $product)
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
                        <!-- Spotlight Badge -->
                        <div class="absolute top-2 right-2 bg-yellow-bg text-yellow-text px-2 py-1 rounded-full text-xs font-semibold">
                            Spotlight
                        </div>
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
                    <p class="text-text-secondary">Belum ada produk spotlight</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Barang Terbaru Section -->
<section class="bg-background py-12">
    <div class="container mx-auto px-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-xl font-bold text-text-primary mb-2">Barang Terbaru</h2>
                <p class="text-sm text-text-secondary">Listing terbaru dari seller - langsung chat untuk nego harga</p>
            </div>
            <a href="{{ route('products.index') }}" class="flex items-center gap-1 text-sm text-primary font-semibold hover:opacity-80 transition group">
                <span>Lihat Semua</span>
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
        
        <!-- Products Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            @forelse($latestProducts as $product)
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
                    <p class="text-text-secondary">Belum ada produk terbaru</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Mungkin kamu suka ini Section -->
<section class="bg-background py-12">
    <div class="container mx-auto px-8">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-xl font-bold text-text-primary">Mungkin kamu suka ini</h2>
        </div>
        
        <!-- Products Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            @forelse($recommendedProducts as $product)
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
                    <p class="text-text-secondary">Belum ada rekomendasi produk</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="bg-background py-12">
    <div class="container mx-auto px-8">
        <h2 class="text-xl font-bold text-text-primary mb-8">Kategori Product</h2>
        
        <!-- Categories Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            @php
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
                
                $categories = \App\Models\Category::active()->root()->limit(6)->get();
            @endphp
            
            @foreach($categories as $category)
                @php
                    $slug = strtolower(str_replace(' ', '', $category->slug));
                    $imageName = $categoryImages[$slug] ?? null;
                @endphp
                <a href="{{ route('categories.show', $category->slug) }}" class="group relative h-48 rounded-8 overflow-hidden hover:shadow-lg transition-all duration-300">
                    @if($imageName && file_exists(public_path("images/kategorisection/{$imageName}")))
                        <img src="{{ asset("images/kategorisection/{$imageName}") }}" alt="{{ $category->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-primary-50 to-primary-light/30 flex items-center justify-center">
                            <svg class="w-16 h-16 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                    <!-- Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-primary/80 via-primary/40 to-transparent"></div>
                    <!-- Category Name -->
                    <div class="absolute bottom-0 left-0 right-0 p-4">
                        <p class="text-white font-semibold text-sm">{{ $category->name }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="bg-primary py-16">
    <div class="container mx-auto px-8">
        <div class="text-center space-y-6 max-w-2xl mx-auto">
            <h2 class="text-2xl lg:text-3xl font-bold text-white">Siap Memulai?</h2>
            <p class="text-base lg:text-md text-white/90 leading-relaxed">
                Bergabunglah dengan ribuan pengguna yang sudah merasakan kemudahan jual beli barang preloved di Reloved.
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('register') }}" class="bg-white text-primary border-2 border-white px-6 py-3 rounded-8 font-semibold text-sm hover:bg-white/90 hover:border-white/90 transition shadow-md hover:shadow-lg">
                    Daftar Sekarang
                </a>
                <a href="{{ route('products.index') }}" class="bg-white text-primary border-2 border-white px-6 py-3 rounded-8 font-semibold text-sm hover:bg-white/90 hover:border-white/90 transition shadow-md hover:shadow-lg">
                    Jelajahi Barang
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
