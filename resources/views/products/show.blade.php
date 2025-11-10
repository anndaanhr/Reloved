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
        <div class="bg-white rounded-16 border border-border shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-bold text-text-primary mb-4">Produk Terjual</h3>
            <p class="text-sm text-text-secondary mb-4">Pilih pembeli yang membeli produk ini:</p>
            
            @if(count($buyers) > 0)
                <form method="POST" action="{{ route('transactions.store') }}" id="mark-as-sold-form">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    
                    <div class="space-y-2 mb-4 max-h-64 overflow-y-auto">
                        @foreach($buyers as $buyer)
                            <label class="flex items-center gap-3 p-3 border border-border rounded-10 hover:bg-gray-50 cursor-pointer transition">
                                <input 
                                    type="radio" 
                                    name="buyer_id" 
                                    value="{{ $buyer['id'] }}"
                                    class="text-primary focus:ring-primary"
                                    required
                                    onchange="enableSubmitButton()"
                                >
                                <div class="flex items-center gap-3 flex-1">
                                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0">
                                        @if($buyer['avatar'])
                                            <img src="{{ $buyer['avatar'] }}" alt="{{ $buyer['name'] }}" class="w-full h-full object-cover rounded-full">
                                        @else
                                            <span class="text-text-secondary font-semibold text-sm">{{ substr($buyer['name'], 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-text-primary">{{ $buyer['name'] }}</p>
                                        @if($buyer['has_offers'] && $buyer['latest_offer_amount'])
                                            <p class="text-xs text-text-secondary">Tawaran terakhir: Rp {{ number_format($buyer['latest_offer_amount'], 0, ',', '.') }}</p>
                                        @else
                                            <p class="text-xs text-text-secondary">Sudah chat</p>
                                        @endif
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <!-- Deal Method -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-text-primary mb-2">Metode Transaksi</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="deal_method" value="meetup" class="text-primary focus:ring-primary" checked onchange="toggleShippingFields()">
                                <span class="ml-2 text-sm text-text-primary">Meet-up (COD)</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="deal_method" value="shipping" class="text-primary focus:ring-primary" onchange="toggleShippingFields()">
                                <span class="ml-2 text-sm text-text-primary">Pengiriman (COD)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Meet-up Location -->
                    <div id="meetup-fields" class="mb-4">
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
                    <div id="shipping-fields" class="mb-4 hidden">
                        <div class="space-y-3">
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
                            <div>
                                <label for="courier" class="block text-sm font-medium text-text-primary mb-2">Kurir</label>
                                <select 
                                    id="courier" 
                                    name="courier" 
                                    class="w-full h-10 px-4 py-2 border border-border rounded-10 text-sm text-text-primary focus:ring-2 focus:ring-primary focus:border-primary transition"
                                >
                                    <option value="jne">JNE</option>
                                    <option value="tiki">TIKI</option>
                                    <option value="pos">POS Indonesia</option>
                                </select>
                            </div>
                            <button 
                                type="button" 
                                onclick="checkShippingCost()" 
                                class="w-full bg-yellow-500 text-white px-4 py-2.5 rounded-10 font-semibold text-sm hover:bg-yellow-600 transition"
                            >
                                Cek Ongkir
                            </button>
                            <div id="shipping-cost-results" class="hidden space-y-2">
                                <!-- Shipping cost results will be displayed here -->
                            </div>
                            <input type="hidden" id="shipping_cost" name="shipping_cost">
                            <input type="hidden" id="shipping_courier" name="shipping_courier">
                            <input type="hidden" id="shipping_service" name="shipping_service">
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="mb-4">
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
                        <button type="submit" id="submit-transaction-btn" class="flex-1 bg-primary text-white px-4 py-2.5 rounded-10 font-semibold text-sm hover:opacity-90 transition" disabled>
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
@endif

@push('scripts')
<script>
function changeMainImage(url) {
    document.getElementById('main-image').src = url;
}

function openMarkAsSoldModal() {
    document.getElementById('mark-as-sold-modal').style.display = 'flex';
}

function closeMarkAsSoldModal() {
    document.getElementById('mark-as-sold-modal').style.display = 'none';
}

async function toggleFavorite() {
    const btn = document.getElementById('favorite-btn');
    const text = document.getElementById('favorite-text');
    const productId = '{{ $product->id }}';
    
    // Disable button during request
    btn.disabled = true;
    
    try {
        const response = await fetch(`/wishlist/${productId}/toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
            },
        });
        
        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`HTTP error! status: ${response.status}, body: ${errorText}`);
        }
        
        const data = await response.json();
        
        if (data.success) {
            // Update button state
            if (data.is_favorite) {
                btn.classList.remove('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
                btn.classList.add('bg-red-500', 'text-white', 'hover:bg-red-600');
                text.textContent = 'Hapus dari Favorit';
            } else {
                btn.classList.remove('bg-red-500', 'text-white', 'hover:bg-red-600');
                btn.classList.add('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
                text.textContent = 'Simpan ke Favorit';
            }
        } else {
            alert('Gagal mengupdate favorit: ' + (data.error || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error toggling favorite:', error);
        alert('Gagal mengupdate favorit. Silakan coba lagi.');
    } finally {
        btn.disabled = false;
    }
}

function toggleShippingFields() {
    const dealMethod = document.querySelector('input[name="deal_method"]:checked').value;
    const meetupFields = document.getElementById('meetup-fields');
    const shippingFields = document.getElementById('shipping-fields');
    
    if (dealMethod === 'meetup') {
        meetupFields.classList.remove('hidden');
        shippingFields.classList.add('hidden');
        document.getElementById('meetup_location').required = true;
        document.getElementById('shipping_cost').required = false;
        document.getElementById('shipping_courier').required = false;
        document.getElementById('shipping_service').required = false;
    } else {
        meetupFields.classList.add('hidden');
        shippingFields.classList.remove('hidden');
        document.getElementById('meetup_location').required = false;
        document.getElementById('shipping_cost').required = true;
        document.getElementById('shipping_courier').required = true;
        document.getElementById('shipping_service').required = true;
    }
}

function enableSubmitButton() {
    const submitBtn = document.getElementById('submit-transaction-btn');
    if (submitBtn) {
        submitBtn.disabled = false;
    }
}

async function checkShippingCost() {
    const originCity = document.getElementById('origin_city').value;
    const destinationCity = document.getElementById('destination_city').value;
    const weight = document.getElementById('weight').value;
    const courier = document.getElementById('courier').value;

    if (!originCity || !destinationCity || !weight) {
        alert('Harap lengkapi semua field untuk cek ongkir.');
        return;
    }

    try {
        // Get city IDs
        const originResponse = await fetch('/api/shipping/city-id', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
            },
            body: JSON.stringify({ city_name: originCity }),
        });

        const destinationResponse = await fetch('/api/shipping/city-id', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
            },
            body: JSON.stringify({ city_name: destinationCity }),
        });

        const originData = await originResponse.json();
        const destinationData = await destinationResponse.json();

        if (!originData.success || !originData.city_id || !destinationData.success || !destinationData.city_id) {
            alert('Kota tidak ditemukan. Pastikan nama kota benar.');
            return;
        }

        // Check cost
        const costResponse = await fetch('/api/shipping/check-cost', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
            },
            body: JSON.stringify({
                origin: originData.city_id,
                destination: destinationData.city_id,
                weight: weight,
                courier: courier,
            }),
        });

        const costData = await costResponse.json();

        if (costData.success && costData.data && costData.data.length > 0) {
            displayShippingCosts(costData.data, courier);
        } else {
            alert('Gagal mendapatkan data ongkir. Silakan coba lagi.');
        }
    } catch (error) {
        console.error('Error checking shipping cost:', error);
        alert('Gagal cek ongkir. Silakan coba lagi.');
    }
}

function displayShippingCosts(costs, courier) {
    const resultsDiv = document.getElementById('shipping-cost-results');
    resultsDiv.classList.remove('hidden');
    resultsDiv.innerHTML = '<p class="font-semibold text-gray-900 mb-2">Pilih Layanan:</p>';

    costs.forEach((service) => {
        const cost = service.cost[0];
        const serviceDiv = document.createElement('div');
        serviceDiv.className = 'p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer';
        serviceDiv.onclick = function() {
            selectShippingService(this, courier, service.service, cost.value);
        };
        
        serviceDiv.innerHTML = `
            <div class="flex justify-between items-center">
                <div>
                    <p class="font-semibold text-gray-900">${service.service}</p>
                    <p class="text-sm text-gray-600">${service.description || ''}</p>
                </div>
                <p class="font-bold text-primary">Rp ${parseInt(cost.value).toLocaleString('id-ID')}</p>
            </div>
        `;
        
        resultsDiv.appendChild(serviceDiv);
    });
}

function selectShippingService(element, courier, service, cost) {
    document.getElementById('shipping_courier').value = courier.toUpperCase();
    document.getElementById('shipping_service').value = service;
    document.getElementById('shipping_cost').value = cost;
    
    // Highlight selected service
    const resultsDiv = document.getElementById('shipping-cost-results');
    resultsDiv.querySelectorAll('div').forEach(div => {
        div.classList.remove('bg-primary/10', 'border-primary');
    });
    if (element) {
        element.classList.add('bg-primary/10', 'border-primary');
    }
    
    alert(`Layanan ${service} dipilih. Ongkir: Rp ${parseInt(cost).toLocaleString('id-ID')}`);
}
</script>
@endpush
@endsection

