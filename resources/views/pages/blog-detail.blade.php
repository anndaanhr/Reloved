@extends('layouts.app')

@section('title', $post['title'] . ' - Blog Reloved')

@section('content')
<div class="container mx-auto px-8 py-12">
    <div class="max-w-4xl mx-auto">
        <a href="{{ route('pages.blog') }}" class="text-primary hover:underline mb-4 inline-block">
            ← Kembali ke Blog
        </a>
        
        <article class="bg-white rounded-8 border border-border p-8">
            <div class="mb-6">
                <p class="text-sm text-text-tertiary mb-2">{{ \Carbon\Carbon::parse($post['date'])->format('d M Y') }} • {{ $post['author'] }}</p>
                <h1 class="text-4xl font-bold text-text-primary mb-4">{{ $post['title'] }}</h1>
            </div>
            
            @if(isset($post['image']) && $post['image'])
                <div class="aspect-video bg-gray-200 rounded-8 mb-8 overflow-hidden">
                    <img src="{{ $post['image'] }}" alt="{{ $post['title'] }}" class="w-full h-full object-cover">
                </div>
            @else
                <div class="aspect-video bg-gray-200 rounded-8 mb-8 flex items-center justify-center">
                    <div class="text-gray-400">
                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            @endif
            
            <div class="prose max-w-none text-text-secondary leading-relaxed">
                {!! $post['content'] !!}
            </div>
            
            <div class="mt-8 pt-6 border-t border-border">
                <a href="{{ route('pages.blog') }}" class="text-primary hover:underline font-semibold">
                    ← Kembali ke Blog
                </a>
            </div>
        </article>
    </div>
</div>
@endsection

