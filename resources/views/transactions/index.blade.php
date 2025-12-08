@extends('layouts.app')

@section('title', 'Transaksi Saya - Reloved')

@section('content')
<div class="container mx-auto px-8 py-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-xl font-bold text-text-primary mb-6">Transaksi Saya</h1>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-10">
                {{ session('success') }}
            </div>
        @endif

                @if($transactions->count() > 0)
                    <div class="space-y-4">
                        @foreach($transactions as $transaction)
                            <div class="bg-white rounded-16 border border-border p-6 hover:shadow-lg transition">
                                <div class="flex flex-col md:flex-row gap-6">
                                    <!-- Product Image -->
                                    <div class="w-full md:w-32 h-32 rounded-10 overflow-hidden bg-gray-100 flex-shrink-0">
                                        @if($transaction->product->images->count() > 0)
                                            <img src="{{ $transaction->product->images->first()->cloudinary_url }}" alt="{{ $transaction->product->title }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Transaction Info -->
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between mb-2">
                                            <div>
                                                <h3 class="text-sm font-semibold text-text-primary mb-1">
                                                    <a href="{{ route('products.show', $transaction->product->id) }}" class="hover:text-primary">
                                                        {{ $transaction->product->title }}
                                                    </a>
                                                </h3>
                                                <p class="text-sm text-text-secondary">
                                                    @if($transaction->buyer_id === Auth::id())
                                                        Penjual: <span class="font-semibold">{{ $transaction->seller->name }}</span>
                                                    @else
                                                        Pembeli: <span class="font-semibold">{{ $transaction->buyer->name }}</span>
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-lg font-bold text-primary">Rp {{ number_format($transaction->price, 0, ',', '.') }}</p>
                                                @if($transaction->shipping_cost)
                                                    <p class="text-sm text-text-secondary">+ Ongkir: Rp {{ number_format($transaction->shipping_cost, 0, ',', '.') }}</p>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-4 text-sm text-text-secondary mb-3">
                                            <span class="px-3 py-1 rounded-full bg-primary-50 text-primary font-semibold border border-primary-300 capitalize">
                                                @if($transaction->status === 'menunggu_transaksi')
                                                    Menunggu Transaksi
                                                @elseif($transaction->status === 'barang_dikirim')
                                                    Barang Dikirim
                                                @elseif($transaction->status === 'paket_diterima')
                                                    Paket Diterima
                                                @elseif($transaction->status === 'selesai')
                                                    Selesai
                                                @elseif($transaction->status === 'dibatalkan')
                                                    Dibatalkan
                                                @endif
                                            </span>
                                            <span>•</span>
                                            <span>{{ ucfirst($transaction->deal_method === 'meetup' ? 'Meet-up' : 'Pengiriman') }}</span>
                                            @if($transaction->tracking_number)
                                                <span>•</span>
                                                <span>Resi: {{ $transaction->tracking_number }}</span>
                                            @endif
                                        </div>

                                        <div class="flex items-center gap-3">
                                            <a href="{{ route('transactions.show', $transaction->id) }}" class="text-primary hover:underline font-semibold text-sm">
                                                Lihat Detail
                                            </a>
                                            @php
                                                $hasReview = \App\Models\Review::where('transaction_id', $transaction->id)
                                                    ->where('reviewer_id', Auth::id())
                                                    ->exists();
                                            @endphp
                                            @if($transaction->status === 'selesai' && !$hasReview)
                                                <a href="{{ route('reviews.create', $transaction->id) }}" class="text-primary hover:underline font-semibold text-sm">
                                                    Beri Review
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $transactions->links() }}
            </div>
                @else
                    <div class="bg-white rounded-8 border border-border p-12 text-center">
                        <svg class="w-24 h-24 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h2 class="text-2xl font-bold text-text-primary mb-2">Belum Ada Transaksi</h2>
                        <p class="text-text-secondary mb-6">Anda belum memiliki transaksi</p>
                        <a href="{{ route('products.index') }}" class="inline-block bg-primary text-white px-6 py-3 rounded-8 font-semibold hover:bg-primary/90 transition">
                            Jelajahi Produk
                        </a>
                    </div>
                @endif
    </div>
</div>
@endsection
