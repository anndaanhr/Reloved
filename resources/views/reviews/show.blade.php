@extends('layouts.app')

@section('title', 'Detail Review - Reloved')

@section('content')
<div class="container mx-auto px-8 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-text-primary mb-8">Detail Review</h1>

        <div class="bg-white rounded-8 border border-border p-8">
            <div class="flex items-start gap-6 mb-6">
                <div class="w-16 h-16 rounded-full bg-gray-300 flex items-center justify-center flex-shrink-0">
                    @if($review->reviewer->avatar)
                        <img src="{{ $review->reviewer->avatar }}" alt="{{ $review->reviewer->name }}" class="w-full h-full object-cover rounded-full">
                    @else
                        <span class="text-gray-600 font-semibold text-xl">{{ substr($review->reviewer->name, 0, 1) }}</span>
                    @endif
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <h2 class="text-2xl font-bold text-text-primary">{{ $review->reviewer->name }}</h2>
                        <div class="flex text-yellow-400">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $review->rating)
                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                @endif
                            @endfor
                        </div>
                    </div>
                    <p class="text-sm text-text-secondary">{{ $review->created_at->format('d M Y, H:i') }}</p>
                </div>
            </div>

            @if($review->product)
            <div class="mb-6 pb-6 border-b border-border">
                <h3 class="text-lg font-semibold text-text-primary mb-2">Produk yang Direview</h3>
                <div class="flex items-center gap-4">
                    <div class="w-20 h-20 rounded-8 overflow-hidden bg-gray-200 flex-shrink-0">
                        @if($review->product->images->count() > 0)
                            <img src="{{ $review->product->images->first()->cloudinary_url }}" alt="{{ $review->product->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div>
                        <a href="{{ route('products.show', $review->product->id) }}" class="text-lg font-semibold text-primary hover:underline">
                            {{ $review->product->title }}
                        </a>
                        <p class="text-sm text-text-secondary">Rp {{ number_format($review->product->price, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            @endif

            @if($review->comment)
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-text-primary mb-2">Komentar</h3>
                <p class="text-base text-text-secondary whitespace-pre-line leading-relaxed">{{ $review->comment }}</p>
            </div>
            @endif

            @if($review->images && count($review->images) > 0)
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-text-primary mb-2">Foto</h3>
                <div class="grid grid-cols-4 gap-4">
                    @foreach($review->images as $image)
                        <img src="{{ $image }}" alt="Review Image" class="w-full h-32 object-cover rounded-8 border border-border">
                    @endforeach
                </div>
            </div>
            @endif

            <div class="flex gap-4">
                <a href="{{ route('reviews.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-8 font-semibold hover:bg-gray-300 transition">
                    Kembali
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

