@extends('layouts.app')

@section('title', 'Blog - Reloved')

@section('content')
<div class="container mx-auto px-8 py-12">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-4xl font-bold text-text-primary mb-8">Blog Reloved</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($posts as $post)
            <div class="bg-white rounded-8 border border-border overflow-hidden hover:shadow-lg transition">
                <div class="aspect-video bg-gray-200 overflow-hidden">
                    @if(isset($post['image']) && $post['image'])
                        <img src="{{ $post['image'] }}" alt="{{ $post['title'] }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                </div>
                <div class="p-6">
                    <p class="text-xs text-text-tertiary mb-2">{{ \Carbon\Carbon::parse($post['date'])->format('d M Y') }} • {{ $post['author'] }}</p>
                    <h3 class="font-bold text-lg text-text-primary mb-2 line-clamp-2">
                        <a href="{{ route('pages.blog.detail', $post['id']) }}" class="hover:text-primary transition">
                            {{ $post['title'] }}
                        </a>
                    </h3>
                    <p class="text-sm text-text-secondary mb-4 line-clamp-3">{{ $post['excerpt'] }}</p>
                    <a href="{{ route('pages.blog.detail', $post['id']) }}" class="text-primary hover:underline font-semibold text-sm">
                        Baca Selengkapnya →
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

