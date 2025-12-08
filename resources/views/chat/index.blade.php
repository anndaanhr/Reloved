@extends('layouts.app')

@section('title', 'Chat - Reloved')

@section('content')
<div class="container mx-auto px-8 py-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-xl font-bold text-text-primary mb-6">Pesan Saya</h1>

        @if($conversations->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Conversations List -->
                <div class="lg:col-span-1 bg-white rounded-16 border border-border overflow-hidden">
                    <div class="p-4 border-b border-border">
                        <h2 class="text-sm font-semibold text-text-primary">Percakapan</h2>
                    </div>
                    <div class="overflow-y-auto max-h-[600px]">
                        @foreach($conversations as $conv)
                            <a href="{{ route('chat.show', $conv->id) }}" class="block p-4 border-b border-border hover:bg-gray-50 transition {{ request()->route('id') == $conv->id ? 'bg-primary-50' : '' }}">
                                <div class="flex items-start gap-3">
                                    <!-- Avatar -->
                                    <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0">
                                        @if($conv->product && $conv->product->images->count() > 0)
                                            <img src="{{ $conv->product->images->first()->cloudinary_url }}" alt="{{ $conv->product->title }}" class="w-full h-full object-cover rounded-full">
                                        @else
                                            <span class="text-text-secondary font-semibold text-sm">{{ substr($conv->product->title ?? 'P', 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <!-- Info -->
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-text-primary truncate">
                                            @if($conv->product)
                                                {{ $conv->product->title }}
                                            @else
                                                {{ $conv->buyer_id === Auth::id() ? $conv->seller->name : $conv->buyer->name }}
                                            @endif
                                        </p>
                                        @if($conv->messages->count() > 0)
                                            <p class="text-xs text-text-secondary truncate mt-1">
                                                {{ $conv->messages->first()->message ?? 'Pesan' }}
                                            </p>
                                        @endif
                                        <p class="text-xs text-text-tertiary mt-1">
                                            {{ $conv->last_message_at ? $conv->last_message_at->diffForHumans() : '' }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Chat Area -->
                <div class="lg:col-span-2 bg-white rounded-16 border border-border">
                    <div class="p-6 text-center text-text-tertiary">
                        <p class="text-sm">Pilih percakapan untuk mulai chat</p>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white rounded-16 border border-border p-12 text-center">
                <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <h3 class="text-lg font-semibold text-text-primary mb-2">Belum ada percakapan</h3>
                <p class="text-sm text-text-secondary mb-6">Mulai chat dengan penjual dari halaman produk.</p>
                <a href="{{ route('products.index') }}" class="inline-block bg-primary text-white px-6 py-2.5 rounded-10 font-semibold text-sm hover:opacity-90 transition">
                    Jelajahi Produk
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

