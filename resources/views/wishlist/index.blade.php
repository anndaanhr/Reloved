@extends('layouts.app')

@section('title', 'Favorit Saya - Reloved')

@section('content')
<div class="container mx-auto px-8 py-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Favorit Saya</h1>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if($wishlists->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($wishlists as $wishlist)
                    <div class="bg-white rounded-8 border border-border overflow-hidden hover:shadow-lg transition relative">
                        <!-- Remove from favorites button -->
                        <form method="POST" action="{{ route('wishlist.destroy', $wishlist->id) }}" class="absolute top-2 right-2 z-10" onsubmit="return confirm('Hapus dari favorit?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 text-white p-2 rounded-full hover:bg-red-600 transition">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </form>
                        
                        <a href="{{ route('products.show', $wishlist->product->id) }}">
                            <div class="aspect-square bg-gray-200 relative overflow-hidden">
                                @if($wishlist->product->images->count() > 0)
                                    <img src="{{ $wishlist->product->images->first()->cloudinary_url }}" alt="{{ $wishlist->product->title }}" class="w-full h-full object-cover" loading="lazy">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="p-3">
                                <h3 class="font-semibold text-text-primary text-sm mb-1 line-clamp-2">{{ $wishlist->product->title }}</h3>
                                <p class="text-lg font-bold text-primary">Rp {{ number_format($wishlist->product->price, 0, ',', '.') }}</p>
                                <p class="text-xs text-text-secondary mt-1">
                                    {{ $wishlist->product->user->city ?? 'Lokasi tidak tersedia' }}
                                </p>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $wishlists->links() }}
            </div>
        @else
            <div class="bg-white rounded-8 border border-border p-12 text-center">
                <svg class="w-24 h-24 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                <h2 class="text-2xl font-bold text-text-primary mb-2">Belum Ada Favorit</h2>
                <p class="text-text-secondary mb-6">Simpan produk favorit Anda untuk melihatnya lagi nanti</p>
                <a href="{{ route('products.index') }}" class="inline-block bg-primary text-white px-6 py-3 rounded-8 font-semibold hover:bg-primary/90 transition">
                    Jelajahi Produk
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

