@extends('layouts.app')

@section('title', 'Detail Transaksi - Reloved')

@section('content')
<div class="container mx-auto px-8 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Detail Transaksi</h1>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-8 border border-border p-8 space-y-6">
            <!-- Product Info -->
            <div class="flex gap-6 border-b border-border pb-6">
                <div class="w-32 h-32 rounded-8 overflow-hidden bg-gray-200 flex-shrink-0">
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
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-text-primary mb-2">{{ $transaction->product->title }}</h2>
                    <p class="text-lg text-primary font-semibold">Rp {{ number_format($transaction->price, 0, ',', '.') }}</p>
                </div>
            </div>

            <!-- Transaction Details -->
            <div class="space-y-4">
                <div>
                    <h3 class="text-lg font-semibold text-text-primary mb-4">Detail Transaksi</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-text-secondary">Status:</span>
                            <span class="font-semibold 
                                @if($transaction->status === 'selesai') text-green-600
                                @elseif($transaction->status === 'dibatalkan') text-red-600
                                @else text-yellow-600
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $transaction->status)) }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-text-secondary">Metode:</span>
                            <span class="font-semibold text-text-primary">{{ $transaction->deal_method === 'meetup' ? 'Meet-up (COD)' : 'Pengiriman (COD)' }}</span>
                        </div>
                        @if($transaction->deal_method === 'meetup' && $transaction->meetup_location)
                            <div class="flex justify-between">
                                <span class="text-text-secondary">Lokasi Meet-up:</span>
                                <span class="font-semibold text-text-primary">{{ $transaction->meetup_location }}</span>
                            </div>
                        @endif
                        @if($transaction->deal_method === 'shipping')
                            @if($transaction->shipping_cost)
                                <div class="flex justify-between">
                                    <span class="text-text-secondary">Ongkir:</span>
                                    <span class="font-semibold text-text-primary">Rp {{ number_format($transaction->shipping_cost, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if($transaction->shipping_courier)
                                <div class="flex justify-between">
                                    <span class="text-text-secondary">Kurir:</span>
                                    <span class="font-semibold text-text-primary">{{ $transaction->shipping_courier }}</span>
                                </div>
                            @endif
                            @if($transaction->shipping_service)
                                <div class="flex justify-between">
                                    <span class="text-text-secondary">Layanan:</span>
                                    <span class="font-semibold text-text-primary">{{ $transaction->shipping_service }}</span>
                                </div>
                            @endif
                            @if($transaction->tracking_number)
                                <div class="flex justify-between">
                                    <span class="text-text-secondary">No. Resi:</span>
                                    <span class="font-semibold text-text-primary">{{ $transaction->tracking_number }}</span>
                                </div>
                            @endif
                        @endif
                        <div class="flex justify-between">
                            <span class="text-text-secondary">Total:</span>
                            <span class="font-semibold text-lg text-primary">
                                Rp {{ number_format($transaction->price + ($transaction->shipping_cost ?? 0), 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- User Info -->
                <div class="border-t border-border pt-4">
                    <h3 class="text-lg font-semibold text-text-primary mb-4">
                        @if($transaction->buyer_id === Auth::id())
                            Informasi Penjual
                        @else
                            Informasi Pembeli
                        @endif
                    </h3>
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-full bg-gray-300 flex items-center justify-center">
                            @if(($transaction->buyer_id === Auth::id() ? $transaction->seller : $transaction->buyer)->avatar)
                                <img src="{{ ($transaction->buyer_id === Auth::id() ? $transaction->seller : $transaction->buyer)->avatar }}" alt="{{ ($transaction->buyer_id === Auth::id() ? $transaction->seller : $transaction->buyer)->name }}" class="w-full h-full object-cover rounded-full">
                            @else
                                <span class="text-gray-600 font-semibold">{{ substr(($transaction->buyer_id === Auth::id() ? $transaction->seller : $transaction->buyer)->name, 0, 1) }}</span>
                            @endif
                        </div>
                        <div>
                            <p class="font-semibold text-text-primary">{{ ($transaction->buyer_id === Auth::id() ? $transaction->seller : $transaction->buyer)->name }}</p>
                            <p class="text-sm text-text-secondary">
                                {{ ($transaction->buyer_id === Auth::id() ? $transaction->seller : $transaction->buyer)->city ?? 'Lokasi tidak tersedia' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Reviews Section -->
                @if($reviews && $reviews->count() > 0)
                <div class="border-t border-border pt-6">
                    <h3 class="text-lg font-semibold text-text-primary mb-4">Reviews</h3>
                    <div class="space-y-4">
                        @foreach($reviews as $reviewItem)
                            <div class="bg-white rounded-8 border border-border p-4">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center flex-shrink-0">
                                        @if($reviewItem->reviewer->avatar)
                                            <img src="{{ $reviewItem->reviewer->avatar }}" alt="{{ $reviewItem->reviewer->name }}" class="w-full h-full object-cover rounded-full">
                                        @else
                                            <span class="text-gray-600 font-semibold">{{ substr($reviewItem->reviewer->name, 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <p class="font-semibold text-text-primary">{{ $reviewItem->reviewer->name }}</p>
                                            <div class="flex text-yellow-400">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $reviewItem->rating)
                                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                                    @else
                                                        <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                                    @endif
                                                @endfor
                                            </div>
                                        </div>
                                        @if($reviewItem->comment)
                                            <p class="text-sm text-text-secondary mb-2">{{ $reviewItem->comment }}</p>
                                        @endif
                                        @if($reviewItem->images && count($reviewItem->images) > 0)
                                            <div class="flex gap-2 mb-2">
                                                @foreach($reviewItem->images as $image)
                                                    <img src="{{ $image }}" alt="Review Image" class="w-16 h-16 object-cover rounded-8 border border-border">
                                                @endforeach
                                            </div>
                                        @endif
                                        <p class="text-xs text-text-tertiary">{{ $reviewItem->created_at->format('d M Y, H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Actions -->
                <div class="border-t border-border pt-4">
                    <div class="flex flex-wrap gap-3">
                        @if($transaction->status === 'menunggu_transaksi' && $transaction->deal_method === 'shipping' && $transaction->seller_id === Auth::id())
                            <button onclick="openShippingModal('{{ $transaction->id }}')" class="flex-1 bg-yellow-500 text-white px-6 py-3 rounded-8 font-semibold hover:bg-yellow-600 transition">
                                Barang Dikirim
                            </button>
                        @endif
                        @if($transaction->status === 'barang_dikirim' && $transaction->buyer_id === Auth::id())
                            <form method="POST" action="{{ route('transactions.received', $transaction->id) }}" class="flex-1" onsubmit="return confirm('Konfirmasi paket sudah diterima?')">
                                @csrf
                                <button type="submit" class="w-full bg-primary text-white px-6 py-3 rounded-8 font-semibold hover:opacity-90 transition">
                                    Paket Diterima
                                </button>
                            </form>
                        @endif
                        @if($canReview)
                            @if($existingReview)
                                <a href="{{ route('reviews.edit', $existingReview->id) }}" class="flex-1 bg-primary text-white px-6 py-3 rounded-8 font-semibold hover:bg-primary/90 transition text-center">
                                    Edit Review
                                </a>
                            @else
                                <a href="{{ route('reviews.create', $transaction->id) }}" class="flex-1 bg-primary text-white px-6 py-3 rounded-8 font-semibold hover:bg-primary/90 transition text-center">
                                    Beri Review
                                </a>
                            @endif
                        @endif
                        @if($transaction->status === 'menunggu_transaksi')
                            <form method="POST" action="{{ route('transactions.cancel', $transaction->id) }}" class="flex-1" onsubmit="return confirm('Batalkan transaksi ini?')">
                                @csrf
                                <button type="submit" class="w-full bg-red-500 text-white px-6 py-3 rounded-8 font-semibold hover:bg-red-600 transition">
                                    Batal Transaksi
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('transactions.index') }}" class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-8 font-semibold hover:bg-gray-300 transition text-center">
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Shipping Modal -->
<div id="shipping-modal" class="fixed inset-0 bg-black/50 z-50 hidden" style="display: none;">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-8 border border-border shadow-xl max-w-md w-full p-6">
            <h3 class="text-xl font-bold text-text-primary mb-4">Barang Dikirim</h3>
            <form method="POST" id="shipping-form">
                @csrf
                <div class="mb-4">
                    <label for="tracking_number" class="block text-sm font-medium text-gray-700 mb-2">No. Resi / Tracking Number</label>
                    <input 
                        type="text" 
                        id="tracking_number" 
                        name="tracking_number" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                        placeholder="Masukkan nomor resi"
                    >
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeShippingModal()" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-semibold hover:bg-gray-300 transition">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 bg-primary text-white px-4 py-2 rounded-lg font-semibold hover:bg-primary/90 transition">
                        Konfirmasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openShippingModal(transactionId) {
    const modal = document.getElementById('shipping-modal');
    const form = document.getElementById('shipping-form');
    form.action = `/transactions/${transactionId}/shipping`;
    modal.style.display = 'flex';
}

function closeShippingModal() {
    document.getElementById('shipping-modal').style.display = 'none';
}
</script>
@endpush
@endsection

