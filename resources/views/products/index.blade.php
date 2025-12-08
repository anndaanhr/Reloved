@extends('layouts.app')

@section('title', 'Daftar Produk - Reloved')

@section('content')
<div class="container mx-auto px-8 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar Filters -->
        <aside class="lg:w-64 flex-shrink-0">
            <div class="bg-white rounded-16 border border-border p-6 sticky top-8">
                <h2 class="text-lg font-bold text-text-primary mb-6">Filter</h2>

                <form method="GET" action="{{ route('products.index') }}" class="space-y-6">
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-text-primary mb-2">Cari</label>
                        <input 
                            type="text" 
                            id="search" 
                            name="search" 
                            value="{{ request('search') }}"
                            placeholder="Cari produk..."
                            class="w-full h-10 px-4 py-2 border border-border rounded-10 text-sm text-text-primary placeholder:text-placeholder focus:ring-2 focus:ring-primary focus:border-primary transition"
                        >
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-text-primary mb-2">Kategori</label>
                        <select 
                            id="category" 
                            name="category" 
                            class="w-full h-10 px-4 py-2 border border-border rounded-10 text-sm text-text-primary focus:ring-2 focus:ring-primary focus:border-primary transition"
                        >
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @foreach($category->children as $child)
                                    <option value="{{ $child->id }}" {{ request('category') == $child->id ? 'selected' : '' }}>
                                        &nbsp;&nbsp;{{ $child->name }}
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>

                    <!-- Condition -->
                    <div>
                        <label for="condition" class="block text-sm font-medium text-text-primary mb-2">Kondisi</label>
                        <select 
                            id="condition" 
                            name="condition" 
                            class="w-full h-10 px-4 py-2 border border-border rounded-10 text-sm text-text-primary focus:ring-2 focus:ring-primary focus:border-primary transition"
                        >
                            <option value="">Semua Kondisi</option>
                            <option value="baru" {{ request('condition') == 'baru' ? 'selected' : '' }}>Baru</option>
                            <option value="lumayan_baru" {{ request('condition') == 'lumayan_baru' ? 'selected' : '' }}>Lumayan Baru</option>
                            <option value="bekas" {{ request('condition') == 'bekas' ? 'selected' : '' }}>Bekas</option>
                            <option value="rusak" {{ request('condition') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                        </select>
                    </div>

                    <!-- Price Range -->
                    <div>
                        <label class="block text-sm font-medium text-text-primary mb-2">Harga</label>
                        <div class="grid grid-cols-2 gap-2">
                            <input 
                                type="number" 
                                name="min_price" 
                                value="{{ request('min_price') }}"
                                placeholder="Min"
                                class="h-10 px-4 py-2 border border-border rounded-10 text-sm text-text-primary placeholder:text-placeholder focus:ring-2 focus:ring-primary focus:border-primary transition"
                            >
                            <input 
                                type="number" 
                                name="max_price" 
                                value="{{ request('max_price') }}"
                                placeholder="Max"
                                class="h-10 px-4 py-2 border border-border rounded-10 text-sm text-text-primary placeholder:text-placeholder focus:ring-2 focus:ring-primary focus:border-primary transition"
                            >
                        </div>
                    </div>

                    <!-- Brand -->
                    <div>
                        <label for="brand" class="block text-sm font-medium text-text-primary mb-2">Brand</label>
                        <input 
                            type="text" 
                            id="brand" 
                            name="brand" 
                            value="{{ request('brand') }}"
                            placeholder="Cari brand..."
                            class="w-full h-10 px-4 py-2 border border-border rounded-10 text-sm text-text-primary placeholder:text-placeholder focus:ring-2 focus:ring-primary focus:border-primary transition"
                        >
                    </div>

                    <!-- Location -->
                    <div>
                        <label for="filter-province" class="block text-sm font-medium text-text-primary mb-2">Provinsi</label>
                        <select 
                            id="filter-province" 
                            name="province" 
                            class="w-full h-10 px-4 py-2 border border-border rounded-10 text-sm text-text-primary focus:ring-2 focus:ring-primary focus:border-primary transition"
                        >
                            <option value="">Semua Provinsi</option>
                        </select>
                    </div>
                    <div>
                        <label for="filter-city" class="block text-sm font-medium text-text-primary mb-2">Kota</label>
                        <select 
                            id="filter-city" 
                            name="city" 
                            class="w-full h-10 px-4 py-2 border border-border rounded-10 text-sm text-text-primary focus:ring-2 focus:ring-primary focus:border-primary transition disabled:bg-gray-100 disabled:cursor-not-allowed"
                            disabled
                        >
                            <option value="">Semua Kota</option>
                        </select>
                    </div>

                    <!-- Submit -->
                    <button 
                        type="submit" 
                        class="w-full bg-primary text-white px-4 py-2.5 rounded-10 font-semibold text-sm hover:opacity-90 transition"
                    >
                        Terapkan Filter
                    </button>
                    <a 
                        href="{{ route('products.index') }}" 
                        class="block w-full text-center bg-gray-100 text-text-secondary px-4 py-2.5 rounded-10 font-semibold text-sm hover:bg-gray-200 transition"
                    >
                        Reset
                    </a>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-xl font-bold text-text-primary">
                    Daftar Produk
                    @if(request('search'))
                        <span class="text-sm font-normal text-text-secondary">- "{{ request('search') }}"</span>
                    @endif
                </h1>
                @auth
                    <a href="{{ route('products.create') }}" class="bg-primary text-white px-5 py-2.5 rounded-10 font-semibold text-sm hover:opacity-90 transition">
                        + Tambah Produk
                    </a>
                @endauth
            </div>

            @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($products as $product)
                        <a href="{{ route('products.show', $product->id) }}" class="group bg-white rounded-16 border border-border overflow-hidden hover:shadow-lg transition-all duration-300">
                            <!-- Image -->
                            <div class="aspect-square bg-gray-100 relative overflow-hidden">
                                @if($product->images->count() > 0)
                                    <img 
                                        src="{{ $product->images->first()->cloudinary_url }}" 
                                        alt="{{ $product->title }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                        loading="lazy"
                                    >
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                                @if($product->stock <= 0)
                                    <div class="absolute inset-0 bg-black/60 flex items-center justify-center backdrop-blur-sm">
                                        <span class="text-white font-semibold text-sm px-3 py-1.5 bg-red-500 rounded-8">Habis</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Content -->
                            <div class="p-4 space-y-3">
                                <!-- Seller Info -->
                                <div class="flex items-center gap-3 pb-3 border-b border-border">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-light to-primary-dark flex items-center justify-center flex-shrink-0">
                                        @if($product->user->avatar)
                                            <img src="{{ $product->user->avatar }}" alt="{{ $product->user->name }}" class="w-full h-full object-cover rounded-full">
                                        @else
                                            <span class="text-sm font-semibold text-white">{{ substr($product->user->name, 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-text-primary truncate">{{ $product->user->name }}</p>
                                        <p class="text-xs text-text-tertiary">{{ $product->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                
                                <h3 class="text-sm font-semibold text-text-primary line-clamp-2 group-hover:text-primary transition-colors">{{ $product->title }}</h3>
                                <p class="text-lg font-bold text-primary">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                
                                <!-- Product Details -->
                                <div class="flex flex-wrap items-center gap-2">
                                    @if($product->size)
                                        <span class="px-2 py-1 rounded-8 bg-gray-100 text-xs text-text-secondary font-medium">Size: {{ $product->size }}</span>
                                    @endif
                                    @if($product->brand)
                                        <span class="px-2 py-1 rounded-8 bg-gray-100 text-xs text-text-secondary font-medium">{{ $product->brand }}</span>
                                    @endif
                                    <!-- Condition Badge -->
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
                        </a>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @else
                <div class="bg-white rounded-16 border border-border p-12 text-center">
                    <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-text-primary mb-2">Tidak ada produk ditemukan</h3>
                    <p class="text-sm text-text-secondary mb-6">Coba ubah filter atau kata kunci pencarian Anda.</p>
                    @auth
                        <a href="{{ route('products.create') }}" class="inline-block bg-primary text-white px-6 py-2.5 rounded-10 font-semibold text-sm hover:opacity-90 transition">
                            Tambah Produk Pertama
                        </a>
                    @endauth
                </div>
            @endif
        </main>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterProvinceSelect = document.getElementById('filter-province');
    const filterCitySelect = document.getElementById('filter-city');

    if (filterProvinceSelect && filterCitySelect) {
        // Load provinces
        fetch('{{ route("api.shipping.provinces") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    data.data.forEach(province => {
                        const option = document.createElement('option');
                        option.value = province.province_id;
                        option.textContent = province.province;
                        if ('{{ request("province") }}' == province.province_id) {
                            option.selected = true;
                        }
                        filterProvinceSelect.appendChild(option);
                    });
                    
                    // If province is already selected, load cities
                    if (filterProvinceSelect.value) {
                        loadCities(filterProvinceSelect.value);
                    }
                }
            })
            .catch(error => {
                console.error('Error loading provinces:', error);
            });

        // Load cities when province is selected
        filterProvinceSelect.addEventListener('change', function() {
            const provinceId = this.value;
            filterCitySelect.innerHTML = '<option value="">Semua Kota</option>';
            filterCitySelect.disabled = !provinceId;

            if (provinceId) {
                loadCities(provinceId);
            }
        });

        function loadCities(provinceId) {
            fetch(`{{ route("api.shipping.cities") }}?province_id=${provinceId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data) {
                        data.data.forEach(city => {
                            const option = document.createElement('option');
                            option.value = city.city_id;
                            option.textContent = city.city_name;
                            if ('{{ request("city") }}' == city.city_id) {
                                option.selected = true;
                            }
                            filterCitySelect.appendChild(option);
                        });
                        filterCitySelect.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error loading cities:', error);
                });
        }
    }
});
</script>
@endpush
@endsection

