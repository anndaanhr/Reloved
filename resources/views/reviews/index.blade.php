@extends('layouts.app')

@section('title', 'Reviews - Reloved')

@section('content')
<div class="container mx-auto px-8 py-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-text-primary mb-8">Reviews</h1>

        @if($reviews->count() > 0)
            <div class="space-y-4">
                @foreach($reviews as $review)
                    <div class="bg-white rounded-8 border border-border p-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center flex-shrink-0">
                                @if($review->reviewer->avatar)
                                    <img src="{{ $review->reviewer->avatar }}" alt="{{ $review->reviewer->name }}" class="w-full h-full object-cover rounded-full">
                                @else
                                    <span class="text-gray-600 font-semibold">{{ substr($review->reviewer->name, 0, 1) }}</span>
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <p class="font-semibold text-text-primary">{{ $review->reviewer->name }}</p>
                                    <div class="flex text-yellow-400">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                            @else
                                                <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="text-xs text-text-tertiary">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                                
                                @if($review->product)
                                    <p class="text-sm text-text-secondary mb-2">
                                        Untuk produk: <a href="{{ route('products.show', $review->product->id) }}" class="text-primary hover:underline">{{ $review->product->title }}</a>
                                    </p>
                                @endif
                                
                                @if($review->comment)
                                    <p class="text-sm text-text-secondary mb-2">{{ $review->comment }}</p>
                                @endif
                                
                                @if($review->images && count($review->images) > 0)
                                    <div class="flex gap-2 mb-2">
                                        @foreach($review->images as $image)
                                            <img src="{{ $image }}" alt="Review Image" class="w-16 h-16 object-cover rounded-8 border border-border">
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $reviews->links() }}
            </div>
        @else
            <div class="bg-white rounded-8 border border-border p-12 text-center">
                <svg class="w-24 h-24 text-gray-400 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <h2 class="text-2xl font-bold text-text-primary mb-2">Belum Ada Reviews</h2>
                <p class="text-text-secondary">Belum ada reviews untuk ditampilkan</p>
            </div>
        @endif
    </div>
</div>
@endsection

