@extends('layouts.app')

@section('title', $product->title . ' - Reloved')

@section('content')
<div class="container mx-auto px-8 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Breadcrumb -->
        <nav class="mb-6 text-sm text-text-secondary">
            <a href="{{ route('home') }}" class="hover:text-primary transition">Home</a>
            <span class="mx-2">/</span>
            <a href="{{ route('products.index') }}" class="hover:text-primary transition">Produk</a>
            <span class="mx-2">/</span>
            <span class="text-text-primary">{{ $product->title }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Image Gallery -->
            <div>
                @if($product->images->count() > 0)
                    <div class="mb-4">
                        <img 
                            id="main-image"
                            src="{{ $product->images->first()->cloudinary_url }}" 
                            alt="{{ $product->title }}"
                            class="w-full h-96 object-cover rounded-16 border border-border"
                        >
                    </div>
                    @if($product->images->count() > 1)
                        <div class="grid grid-cols-5 gap-2">
                            @foreach($product->images as $image)
                                <img 
                                    src="{{ $image->cloudinary_url }}" 
                                    alt="{{ $product->title }}"
                                    class="w-full h-20 object-cover rounded-10 border border-border cursor-pointer hover:border-primary transition"
                                    onclick="changeMainImage('{{ $image->cloudinary_url }}')"
                                >
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="w-full h-96 bg-gray-100 rounded-16 border border-border flex items-center justify-center">
                        <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif
            </div>

            <!-- Product Info -->
            <div>
                <h1 class="text-xl font-bold text-text-primary mb-4">{{ $product->title }}</h1>
                
                <div class="mb-6">
                    <p class="text-2xl font-bold text-primary mb-3">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    <div class="flex items-center gap-4 text-sm text-text-secondary mb-3">
                        @if($product->size)
                            <span>Size: {{ $product->size }}</span>
                        @endif
                        @if($product->brand)
                            <span>{{ $product->brand }}</span>
                        @endif
                    </div>
                    <!-- Condition Badge -->
                    <div class="mb-4">
                        <span class="px-3 py-1 rounded-full bg-primary-50 text-primary text-xs font-semibold border border-primary-300">
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

                <!-- Seller Info -->
                <div class="bg-white rounded-16 border border-border p-4 mb-6">
                    <div class="flex items-center gap-3 pb-3 border-b border-border">
                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0">
                            @if($product->user->avatar)
                                <img src="{{ $product->user->avatar }}" alt="{{ $product->user->name }}" class="w-full h-full object-cover rounded-full">
                            @else
                                <span class="text-sm font-semibold text-text-secondary">{{ substr($product->user->name, 0, 1) }}</span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-text-primary truncate">{{ $product->user->name }}</p>
                            <p class="text-xs text-text-tertiary">
                                {{ $product->user->city ?? 'Lokasi tidak tersedia' }}
                                @if($product->user->province)
                                    , {{ $product->user->province }}
                                @endif
                            </p>
                        </div>
                        @if($product->user->rating_avg > 0)
                            <div class="flex items-center gap-1">
                                <span class="text-sm font-semibold text-text-primary">{{ number_format($product->user->rating_avg, 1) }}</span>
                                <div class="flex text-yellow-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($product->user->rating_avg))
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                        @else
                                            <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                        @endif
                                    @endfor
                                </div>
                                <span class="text-xs text-text-tertiary">({{ $product->user->review_count }})</span>
                            </div>
                        @endif
                    </div>
                    <p class="text-xs text-text-tertiary mt-2">{{ $product->created_at->diffForHumans() }}</p>
                </div>

                <!-- Actions -->
                <div class="space-y-3">
                    @auth
                        @if(Auth::id() === $product->user_id)
                            <!-- Seller Actions -->
                            <div class="space-y-3">
                                @if($canMarkAsSold)
                                    <button 
                                        onclick="openMarkAsSoldModal()"
                                        class="w-full bg-primary text-white px-6 py-3 rounded-10 font-semibold text-sm hover:opacity-90 transition"
                                    >
                                        Produk Terjual
                                    </button>
                                @endif
                                <div class="flex gap-3">
                                    <a href="{{ route('products.edit', $product->id) }}" class="flex-1 bg-gray-100 text-text-secondary px-6 py-3 rounded-10 font-semibold text-sm hover:bg-gray-200 transition text-center">
                                        Edit Produk
                                    </a>
                                    <form method="POST" action="{{ route('products.destroy', $product->id) }}" class="flex-1" onsubmit="return confirm('Hapus produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full bg-red-500 text-white px-6 py-3 rounded-10 font-semibold text-sm hover:bg-red-600 transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <!-- Buyer Actions -->
                            <form method="GET" action="{{ route('chat.create') }}" class="w-full">
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <button type="submit" class="w-full bg-primary text-white px-6 py-3 rounded-10 font-semibold text-sm hover:opacity-90 transition">
                                    Chat Penjual
                                </button>
                            </form>
                            <div class="flex gap-3">
                                <button 
                                    id="favorite-btn"
                                    class="flex-1 {{ $isFavorite ? 'bg-red-500 text-white hover:bg-red-600' : 'bg-gray-100 text-text-secondary hover:bg-gray-200' }} px-6 py-3 rounded-10 font-semibold text-sm transition" 
                                    onclick="toggleFavorite()"
                                >
                                    <span id="favorite-text">{{ $isFavorite ? 'Hapus dari Favorit' : 'Simpan ke Favorit' }}</span>
                                </button>
                                <button class="flex-1 bg-gray-100 text-text-secondary px-6 py-3 rounded-10 font-semibold text-sm hover:bg-gray-200 transition">
                                    Bagikan
                                </button>
                            </div>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="block w-full bg-primary text-white px-6 py-3 rounded-10 font-semibold text-sm hover:opacity-90 transition text-center">
                            Chat Penjual
                        </a>
                    @endauth
                </div>

                <!-- Product Details -->
                <div class="mt-6 space-y-3">
                    <div>
                        <h3 class="text-sm font-semibold text-text-primary mb-3">Detail Produk</h3>
                        <div class="space-y-2 text-sm">
                            @if($product->brand)
                                <div class="flex">
                                    <span class="w-24 text-text-secondary">Brand:</span>
                                    <span class="text-text-primary">{{ $product->brand }}</span>
                                </div>
                            @endif
                            @if($product->size)
                                <div class="flex">
                                    <span class="w-24 text-text-secondary">Ukuran:</span>
                                    <span class="text-text-primary">{{ $product->size }}</span>
                                </div>
                            @endif
                            @if($product->model)
                                <div class="flex">
                                    <span class="w-24 text-text-secondary">Model:</span>
                                    <span class="text-text-primary">{{ $product->model }}</span>
                                </div>
                            @endif
                            <div class="flex">
                                <span class="w-24 text-text-secondary">Metode:</span>
                                <span class="text-text-primary">
                                    @if(is_array($product->deal_method))
                                        {{ implode(', ', array_map(function($method) {
                                            return $method === 'meetup' ? 'Meet-up' : 'Pengiriman';
                                        }, $product->deal_method)) }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="bg-white rounded-16 border border-border p-6 mb-8">
            <h2 class="text-lg font-bold text-text-primary mb-4">Deskripsi</h2>
            <div class="prose max-w-none">
                <p class="text-sm text-text-secondary whitespace-pre-line leading-relaxed">{{ $product->description }}</p>
            </div>
        </div>

        <!-- Reviews Section -->
        @if($reviews && $reviews->count() > 0)
        <div class="bg-white rounded-16 border border-border p-6 mb-8">
            <h2 class="text-lg font-bold text-text-primary mb-6">Reviews</h2>
            <div class="space-y-4">
                @foreach($reviews as $review)
                    <div class="bg-gray-50 rounded-16 border border-border p-4">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0">
                                @if($review->reviewer->avatar)
                                    <img src="{{ $review->reviewer->avatar }}" alt="{{ $review->reviewer->name }}" class="w-full h-full object-cover rounded-full">
                                @else
                                    <span class="text-text-secondary font-semibold text-sm">{{ substr($review->reviewer->name, 0, 1) }}</span>
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <p class="text-sm font-semibold text-text-primary">{{ $review->reviewer->name }}</p>
                                    <div class="flex text-yellow-400">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                            @else
                                                <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                                @if($review->comment)
                                    <p class="text-sm text-text-secondary mb-2">{{ $review->comment }}</p>
                                @endif
                                @if($review->images && count($review->images) > 0)
                                    <div class="flex gap-2 mb-2">
                                        @foreach($review->images as $image)
                                            <img src="{{ $image }}" alt="Review Image" class="w-16 h-16 object-cover rounded-10 border border-border">
                                        @endforeach
                                    </div>
                                @endif
                                <p class="text-xs text-text-tertiary">{{ $review->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Other Products from Seller -->
        @if($product->user->products()->where('id', '!=', $product->id)->available()->count() > 0)
        <div class="bg-white rounded-16 border border-border p-6">
            <h2 class="text-lg font-bold text-text-primary mb-6">Produk Lain dari Penjual</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($product->user->products()->where('id', '!=', $product->id)->available()->limit(4)->get() as $otherProduct)
                    <a href="{{ route('products.show', $otherProduct->id) }}" class="bg-white rounded-16 border border-border overflow-hidden hover:shadow-lg transition">
                        <div class="aspect-square bg-gray-100 relative overflow-hidden">
                            @if($otherProduct->images->count() > 0)
                                <img src="{{ $otherProduct->images->first()->cloudinary_url }}" alt="{{ $otherProduct->title }}" class="w-full h-full object-cover" loading="lazy">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="p-3">
                            <h3 class="text-sm font-semibold text-text-primary mb-1 line-clamp-2">{{ $otherProduct->title }}</h3>
                            <p class="text-base font-bold text-primary">Rp {{ number_format($otherProduct->price, 0, ',', '.') }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Mark as Sold Modal -->
@if($canMarkAsSold)
<div id="mark-as-sold-modal" class="fixed inset-0 bg-black/50 z-50 hidden" style="display: none;">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-16 border border-border shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-border p-6 z-10">
                <h3 class="text-lg font-bold text-text-primary">Produk Terjual</h3>
                <p class="text-sm text-text-secondary mt-1">Pilih pembeli yang membeli produk ini</p>
            </div>
            
            <div class="p-6">
                @if(count($buyers) > 0)
                    <form method="POST" action="{{ route('transactions.store') }}" id="mark-as-sold-form">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        
                        <!-- Buyer Selection -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-text-primary mb-3">Pilih Pembeli</label>
                            <div class="space-y-2 max-h-48 overflow-y-auto">
                                @foreach($buyers as $buyer)
                                    <label class="flex items-center gap-3 p-3 border border-border rounded-10 hover:bg-gray-50 cursor-pointer transition">
                                        <input 
                                            type="radio" 
                                            name="buyer_id" 
                                            value="{{ $buyer['id'] }}"
                                            class="text-primary focus:ring-primary"
                                            required
                                            onchange="validateForm()"
                                        >
                                        <div class="flex items-center gap-3 flex-1 min-w-0">
                                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0">
                                                @if($buyer['avatar'])
                                                    <img src="{{ $buyer['avatar'] }}" alt="{{ $buyer['name'] }}" class="w-full h-full object-cover rounded-full">
                                                @else
                                                    <span class="text-text-secondary font-semibold text-sm">{{ substr($buyer['name'], 0, 1) }}</span>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-semibold text-text-primary truncate">{{ $buyer['name'] }}</p>
                                                @if($buyer['has_offers'] && $buyer['latest_offer_amount'])
                                                    <p class="text-xs text-text-secondary truncate">Tawaran: Rp {{ number_format($buyer['latest_offer_amount'], 0, ',', '.') }}</p>
                                                @else
                                                    <p class="text-xs text-text-secondary">Sudah chat</p>
                                                @endif
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Deal Method -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-text-primary mb-3">Metode Transaksi</label>
                            <div class="space-y-2">
                                <label class="flex items-center p-3 border border-border rounded-10 cursor-pointer hover:bg-gray-50 transition">
                                    <input type="radio" name="deal_method" value="meetup" class="text-primary focus:ring-primary" checked onchange="toggleShippingFields()">
                                    <span class="ml-3 text-sm text-text-primary font-medium">Meet-up (COD)</span>
                                </label>
                                <label class="flex items-center p-3 border border-border rounded-10 cursor-pointer hover:bg-gray-50 transition">
                                    <input type="radio" name="deal_method" value="shipping" class="text-primary focus:ring-primary" onchange="toggleShippingFields()">
                                    <span class="ml-3 text-sm text-text-primary font-medium">Pengiriman (COD)</span>
                                </label>
                            </div>
                        </div>

                        <!-- Meet-up Location -->
                        <div id="meetup-fields" class="mb-6">
                            <label for="meetup_location" class="block text-sm font-medium text-text-primary mb-2">Lokasi Meet-up</label>
                            <input 
                                type="text" 
                                id="meetup_location" 
                                name="meetup_location" 
                                placeholder="Contoh: Mall Grand Indonesia, Jakarta"
                                class="w-full h-10 px-4 py-2 border border-border rounded-10 text-sm text-text-primary placeholder:text-placeholder focus:ring-2 focus:ring-primary focus:border-primary transition"
                            >
                        </div>

                        <!-- Shipping Fields -->
                        <div id="shipping-fields" class="mb-6 hidden">
                            <div class="space-y-4">
                                <div>
                                    <label for="origin_city" class="block text-sm font-medium text-text-primary mb-2">Kota Asal</label>
                                    <input 
                                        type="text" 
                                        id="origin_city" 
                                        name="origin_city" 
                                        value="{{ Auth::user()->city ?? '' }}"
                                        placeholder="Kota asal pengiriman"
                                        class="w-full h-10 px-4 py-2 border border-border rounded-10 text-sm text-text-primary placeholder:text-placeholder focus:ring-2 focus:ring-primary focus:border-primary transition"
                                    >
                                </div>
                                <div>
                                    <label for="destination_city" class="block text-sm font-medium text-text-primary mb-2">Kota Tujuan</label>
                                    <input 
                                        type="text" 
                                        id="destination_city" 
                                        name="destination_city" 
                                        placeholder="Kota tujuan pengiriman"
                                        class="w-full h-10 px-4 py-2 border border-border rounded-10 text-sm text-text-primary placeholder:text-placeholder focus:ring-2 focus:ring-primary focus:border-primary transition"
                                    >
                                </div>
                                <div>
                                    <label for="weight" class="block text-sm font-medium text-text-primary mb-2">Berat (gram)</label>
                                    <input 
                                        type="number" 
                                        id="weight" 
                                        name="weight" 
                                        min="1"
                                        placeholder="1000"
                                        class="w-full h-10 px-4 py-2 border border-border rounded-10 text-sm text-text-primary placeholder:text-placeholder focus:ring-2 focus:ring-primary focus:border-primary transition"
                                    >
                                </div>
                                <button 
                                    type="button" 
                                    onclick="checkShippingCost()" 
                                    class="w-full bg-blue-500 text-white px-4 py-2.5 rounded-10 font-semibold text-sm hover:bg-blue-600 transition"
                                >
                                    Cek Ongkir
                                </button>
                                
                                <!-- Shipping Results -->
                                <div id="shipping-cost-results" class="hidden">
                                    <p class="font-semibold text-gray-900 mb-3 text-sm">Pilih Layanan:</p>
                                    <div class="space-y-2 max-h-64 overflow-y-auto border border-gray-200 rounded-10 p-2">
                                        <!-- Results will be inserted here -->
                                    </div>
                                </div>
                                
                                <input type="hidden" id="shipping_cost" name="shipping_cost">
                                <input type="hidden" id="shipping_courier" name="shipping_courier">
                                <input type="hidden" id="shipping_service" name="shipping_service">
                            </div>
                        </div>

                        <!-- Price -->
                        <div class="mb-6">
                            <label for="price" class="block text-sm font-medium text-text-primary mb-2">Harga Kesepakatan (Rp)</label>
                            <input 
                                type="number" 
                                id="price" 
                                name="price" 
                                value="{{ $product->price }}"
                                min="0"
                                step="1000"
                                required
                                class="w-full h-10 px-4 py-2 border border-border rounded-10 text-sm text-text-primary placeholder:text-placeholder focus:ring-2 focus:ring-primary focus:border-primary transition"
                            >
                        </div>

                        <div class="flex gap-3">
                            <button type="button" onclick="closeMarkAsSoldModal()" class="flex-1 bg-gray-100 text-text-secondary px-4 py-2.5 rounded-10 font-semibold text-sm hover:bg-gray-200 transition">
                                Batal
                            </button>
                            <button type="submit" id="submit-transaction-btn" class="flex-1 bg-primary text-white px-4 py-2.5 rounded-10 font-semibold text-sm hover:opacity-90 transition disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                Konfirmasi
                            </button>
                        </div>
                    </form>
                @else
                    <div class="text-center py-8">
                        <p class="text-sm text-text-secondary mb-4">Belum ada pembeli yang chat/tawar</p>
                        <button onclick="closeMarkAsSoldModal()" class="bg-gray-100 text-text-secondary px-6 py-2.5 rounded-10 font-semibold text-sm hover:bg-gray-200 transition">
                            Tutup
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
function toggleShippingFields() {
    const dealMethod = document.querySelector('input[name="deal_method"]:checked').value;
    const meetupFields = document.getElementById('meetup-fields');
    const shippingFields = document.getElementById('shipping-fields');
    const shippingResults = document.getElementById('shipping-cost-results');
    
    if (dealMethod === 'meetup') {
        meetupFields.classList.remove('hidden');
        shippingFields.classList.add('hidden');
        shippingResults.classList.add('hidden');
        document.getElementById('meetup_location').required = true;
        // Clear shipping requirements
        document.getElementById('shipping_cost').value = '';
        document.getElementById('shipping_courier').value = '';
        document.getElementById('shipping_service').value = '';
    } else {
        meetupFields.classList.add('hidden');
        shippingFields.classList.remove('hidden');
        document.getElementById('meetup_location').required = false;
    }
    
    validateForm();
}

function validateForm() {
    const submitBtn = document.getElementById('submit-transaction-btn');
    const buyerSelected = document.querySelector('input[name="buyer_id"]:checked');
    const dealMethod = document.querySelector('input[name="deal_method"]:checked').value;
    
    let isValid = buyerSelected !== null;
    
    if (dealMethod === 'shipping') {
        const shippingCost = document.getElementById('shipping_cost').value;
        const shippingCourier = document.getElementById('shipping_courier').value;
        const shippingService = document.getElementById('shipping_service').value;
        isValid = isValid && shippingCost && shippingCourier && shippingService;
    }
    
    submitBtn.disabled = !isValid;
}

async function checkShippingCost() {
    const originCity = document.getElementById('origin_city').value.trim();
    const destinationCity = document.getElementById('destination_city').value.trim();
    const weight = document.getElementById('weight').value;

    if (!originCity || !destinationCity || !weight) {
        alert('Harap lengkapi semua field untuk cek ongkir.');
        return;
    }

    const button = event.target;
    const originalText = button.textContent;
    button.textContent = 'Memproses...';
    button.disabled = true;

    try {
        // Get origin subdistrict ID
        const originResponse = await fetch('/api/shipping/subdistrict-id', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            },
            body: JSON.stringify({ city_name: originCity }),
        });

        const originData = await originResponse.json();

        if (!originData.success || !originData.subdistrict_id) {
            let message = `Kota asal "${originCity}" tidak ditemukan.`;
            if (originData.suggestions && originData.suggestions.length > 0) {
                message += '\n\nMungkin maksud Anda:\n';
                message += originData.suggestions.map(s => `- ${s.city_name || s.name}`).join('\n');
            }
            alert(message);
            button.textContent = originalText;
            button.disabled = false;
            return;
        }

        // Get destination subdistrict ID
        const destinationResponse = await fetch('/api/shipping/subdistrict-id', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            },
            body: JSON.stringify({ city_name: destinationCity }),
        });

        const destinationData = await destinationResponse.json();

        if (!destinationData.success || !destinationData.subdistrict_id) {
            let message = `Kota tujuan "${destinationCity}" tidak ditemukan.`;
            if (destinationData.suggestions && destinationData.suggestions.length > 0) {
                message += '\n\nMungkin maksud Anda:\n';
                message += destinationData.suggestions.map(s => `- ${s.city_name || s.name}`).join('\n');
            }
            alert(message);
            button.textContent = originalText;
            button.disabled = false;
            return;
        }

        // Calculate shipping cost
        const costResponse = await fetch('/api/shipping/calculate-cost', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            },
            body: JSON.stringify({
                origin: originData.subdistrict_id,
                destination: destinationData.subdistrict_id,
                weight: parseInt(weight),
                couriers: 'jne:jnt:tiki:pos',
            }),
        });

        const costData = await costResponse.json();

        if (costData.success && costData.data && costData.data.length > 0) {
            displayShippingCosts(costData.data);
        } else {
            alert(costData.message || 'Tidak dapat menghitung ongkir. Silakan coba lagi.');
        }
    } catch (error) {
        console.error('Error checking shipping cost:', error);
        alert('Gagal cek ongkir. Silakan coba lagi.\n\nError: ' + error.message);
    } finally {
        button.textContent = originalText;
        button.disabled = false;
    }
}

function displayShippingCosts(services) {
    const resultsDiv = document.getElementById('shipping-cost-results');
    resultsDiv.classList.remove('hidden');
    
    // Get the container for results
    const container = resultsDiv.querySelector('div');
    container.innerHTML = '';

    // Group services by courier
    const groupedByCourier = {};
    services.forEach(service => {
        const courierCode = service.code.toUpperCase();
        if (!groupedByCourier[courierCode]) {
            groupedByCourier[courierCode] = {
                name: service.name,
                services: []
            };
        }
        groupedByCourier[courierCode].services.push(service);
    });

    // Display services grouped by courier
    Object.keys(groupedByCourier).forEach(courierCode => {
        const courier = groupedByCourier[courierCode];
        
        const courierHeader = document.createElement('div');
        courierHeader.className = 'font-bold text-xs text-gray-700 uppercase mt-3 first:mt-0 mb-2 px-2 sticky top-0 bg-white py-1';
        courierHeader.textContent = `${courierCode}`;
        container.appendChild(courierHeader);

        courier.services.forEach(service => {
            const serviceDiv = document.createElement('label');
            serviceDiv.className = 'shipping-service-item flex items-start gap-3 p-3 mb-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition';
            
            const etdText = service.etd ? service.etd.replace(' day', ' hari').replace('HARI', 'hari') : 'Hubungi kurir';
            
            serviceDiv.innerHTML = `
                <input type="radio" name="shipping_option" class="mt-1 text-primary focus:ring-primary" onchange="selectShippingService('${service.code}', '${service.service}', ${service.cost}, '${etdText}')">
                <div class="flex-1">
                    <div class="flex justify-between items-start gap-2 mb-1">
                        <span class="font-semibold text-gray-900 text-sm">${service.service}</span>
                        <span class="font-bold text-primary text-sm whitespace-nowrap">Rp ${parseInt(service.cost).toLocaleString('id-ID')}</span>
                    </div>
                    ${service.description ? `<p class="text-xs text-gray-600 mb-1">${service.description}</p>` : ''}
                    <p class="text-xs text-gray-500">⏱ ${etdText}</p>
                </div>
            `;
            
            container.appendChild(serviceDiv);
        });
    });
}

function selectShippingService(courier, service, cost, etd) {
    document.getElementById('shipping_courier').value = courier.toUpperCase();
    document.getElementById('shipping_service').value = service;
    document.getElementById('shipping_cost').value = cost;
    
    validateForm();
    showNotification(`✓ ${courier.toUpperCase()} ${service} dipilih (Rp ${parseInt(cost).toLocaleString('id-ID')})`);
}

function showNotification(message) {
    const existingNotif = document.querySelector('.shipping-notification');
    if (existingNotif) existingNotif.remove();

    const notification = document.createElement('div');
    notification.className = 'shipping-notification fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    notification.style.animation = 'slideIn 0.3s ease-out';
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

function openMarkAsSoldModal() {
    document.getElementById('mark-as-sold-modal').style.display = 'flex';
}

function closeMarkAsSoldModal() {
    document.getElementById('mark-as-sold-modal').style.display = 'none';
    // Reset form
    document.getElementById('mark-as-sold-form')?.reset();
    document.getElementById('shipping-cost-results').classList.add('hidden');
    document.getElementById('shipping_cost').value = '';
    document.getElementById('shipping_courier').value = '';
    document.getElementById('shipping_service').value = '';
    validateForm();
}
</script>
@endpush
@endsection

