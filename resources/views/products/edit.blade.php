@extends('layouts.app')

@section('title', 'Edit Produk - Reloved')

@section('content')
<div class="container mx-auto px-8 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-text-primary mb-8">Edit Produk</h1>

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-8">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('products.update', $product->id) }}" enctype="multipart/form-data" class="bg-white rounded-8 border border-border p-8 space-y-6">
            @csrf
            @method('PUT')

            <!-- Existing Images -->
            @if($product->images->count() > 0)
            <div>
                <label class="block text-sm font-medium text-text-secondary mb-2">Foto Produk Saat Ini</label>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-4">
                    @foreach($product->images as $image)
                        <div class="relative">
                            <img src="{{ $image->cloudinary_url }}" alt="Product Image" class="w-full h-32 object-cover rounded-8 border border-border">
                            @if($product->images->count() > 1)
                                <form method="POST" action="{{ route('products.images.destroy', [$product->id, $image->id]) }}" class="absolute top-1 right-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 text-white text-xs px-2 py-1 rounded hover:bg-red-600" onclick="return confirm('Hapus gambar ini?')">
                                        ×
                                    </button>
                                </form>
                            @endif
                            @if($image->is_primary)
                                <span class="absolute bottom-1 left-1 bg-primary text-white text-xs px-2 py-1 rounded">Utama</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- New Images -->
            <div>
                <label class="block text-sm font-medium text-text-secondary mb-2">
                    Tambah Foto Produk (Opsional, Maksimal 10 gambar total)
                </label>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-4" id="image-preview-container">
                    <!-- Preview akan ditambahkan via JavaScript -->
                </div>
                <input 
                    type="file" 
                    name="images[]" 
                    id="images" 
                    accept="image/*"
                    multiple
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90"
                    onchange="previewImages(this)"
                >
                <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG. Maksimal 2MB per gambar.</p>
            </div>

            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Judul Produk <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    value="{{ old('title', $product->title) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required
                >
            </div>

            <!-- Category - 2 Step Selection -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Parent Category -->
                <div>
                    <label for="parent_category_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Kategori Utama <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="parent_category_id" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                        required
                    >
                        <option value="">Pilih Kategori Utama</option>
                        @php
                            $productParentCategory = $product->category && $product->category->parent_id 
                                ? $product->category->parent 
                                : ($product->category ?? null);
                        @endphp
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('parent_category_id', $productParentCategory->id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Sub Category -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Sub Kategori <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="category_id" 
                        name="category_id" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                        required
                    >
                        @if($product->category)
                            <option value="{{ $product->category_id }}" selected>{{ $product->category->name }}</option>
                        @else
                            <option value="">Pilih Sub Kategori</option>
                        @endif
                    </select>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi <span class="text-red-500">*</span>
                </label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="6"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required
                >{{ old('description', $product->description) }}</textarea>
            </div>

            <!-- Price -->
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                    Harga (Rp) <span class="text-red-500">*</span>
                </label>
                <input 
                    type="number" 
                    id="price" 
                    name="price" 
                    value="{{ old('price', $product->price) }}"
                    min="0"
                    step="1000"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required
                >
            </div>

            <!-- Condition -->
            <div>
                <label for="condition" class="block text-sm font-medium text-gray-700 mb-2">
                    Kondisi <span class="text-red-500">*</span>
                </label>
                <select 
                    id="condition" 
                    name="condition" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required
                >
                    <option value="">Pilih Kondisi</option>
                    <option value="baru" {{ old('condition', $product->condition) == 'baru' ? 'selected' : '' }}>Baru</option>
                    <option value="seperti_baru" {{ old('condition', $product->condition) == 'seperti_baru' ? 'selected' : '' }}>Seperti Baru</option>
                    <option value="bekas_bagus" {{ old('condition', $product->condition) == 'bekas_bagus' ? 'selected' : '' }}>Bekas (Bagus)</option>
                    <option value="bekas_cukup" {{ old('condition', $product->condition) == 'bekas_cukup' ? 'selected' : '' }}>Bekas (Cukup)</option>
                </select>
            </div>

            <!-- Dynamic Attributes based on Category -->
            <div id="product-attributes" class="space-y-4">
                <!-- Attributes will be loaded dynamically via JavaScript based on selected category -->
                <!-- Show existing values for edit -->
                @if($product->brand || $product->size || $product->model || $product->expired_date || $product->weight || $product->author || $product->publisher || $product->year)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @if($product->brand)
                    <div>
                        <label for="brand" class="block text-sm font-medium text-gray-700 mb-2">Brand/Merek</label>
                        <input type="text" id="brand" name="brand" value="{{ old('brand', $product->brand) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    @endif
                    @if($product->size)
                    <div>
                        <label for="size" class="block text-sm font-medium text-gray-700 mb-2">Ukuran</label>
                        <input type="text" id="size" name="size" value="{{ old('size', $product->size) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    @endif
                    @if($product->model)
                    <div>
                        <label for="model" class="block text-sm font-medium text-gray-700 mb-2">Model</label>
                        <input type="text" id="model" name="model" value="{{ old('model', $product->model) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    @endif
                    @if($product->expired_date)
                    <div>
                        <label for="expired_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Kadaluarsa</label>
                        <input type="date" id="expired_date" name="expired_date" value="{{ old('expired_date', $product->expired_date) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    @endif
                    @if($product->weight)
                    <div>
                        <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">Berat (gram)</label>
                        <input type="number" id="weight" name="weight" value="{{ old('weight', $product->weight) }}" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    @endif
                    @if($product->author)
                    <div>
                        <label for="author" class="block text-sm font-medium text-gray-700 mb-2">Penulis</label>
                        <input type="text" id="author" name="author" value="{{ old('author', $product->author) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    @endif
                    @if($product->publisher)
                    <div>
                        <label for="publisher" class="block text-sm font-medium text-gray-700 mb-2">Penerbit</label>
                        <input type="text" id="publisher" name="publisher" value="{{ old('publisher', $product->publisher) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    @endif
                    @if($product->year)
                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700 mb-2">Tahun Terbit</label>
                        <input type="number" id="year" name="year" value="{{ old('year', $product->year) }}" min="1900" max="{{ date('Y') + 1 }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    @endif
                </div>
                @endif
            </div>

            <!-- Stock -->
            <div>
                <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">
                    Stok <span class="text-red-500">*</span>
                </label>
                <input 
                    type="number" 
                    id="stock" 
                    name="stock" 
                    value="{{ old('stock', $product->stock) }}"
                    min="1"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required
                >
            </div>

            <!-- Deal Method -->
            <div>
                <label class="block text-sm font-medium text-text-secondary mb-2">
                    Metode Transaksi <span class="text-red-500">*</span>
                </label>
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            name="deal_method[]" 
                            value="meetup"
                            {{ in_array('meetup', old('deal_method', $product->deal_method ?? [])) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-primary focus:ring-primary"
                        >
                        <span class="ml-2 text-gray-700">Meet-up (COD)</span>
                    </label>
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            name="deal_method[]" 
                            value="shipping"
                            {{ in_array('shipping', old('deal_method', $product->deal_method ?? [])) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-primary focus:ring-primary"
                        >
                        <span class="ml-2 text-gray-700">Pengiriman (COD)</span>
                    </label>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex gap-4">
                <button 
                    type="submit" 
                    class="bg-primary text-white px-6 py-2 rounded-lg font-semibold hover:bg-primary/90 transition"
                >
                    Simpan Perubahan
                </button>
                <a 
                    href="{{ route('products.show', $product->id) }}" 
                    class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-semibold hover:bg-gray-300 transition"
                >
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
@php
    $productDataArray = [
        'brand' => old('brand', $product->brand ?? null),
        'size' => old('size', $product->size ?? null),
        'model' => old('model', $product->model ?? null),
        'expired_date' => old('expired_date', $product->expired_date ?? null),
        'weight' => old('weight', $product->weight ?? null),
        'author' => old('author', $product->author ?? null),
        'publisher' => old('publisher', $product->publisher ?? null),
        'year' => old('year', $product->year ?? null),
    ];
    
    $oldValuesArray = [
        'brand' => old('brand'),
        'size' => old('size'),
        'model' => old('model'),
        'expired_date' => old('expired_date'),
        'weight' => old('weight'),
        'author' => old('author'),
        'publisher' => old('publisher'),
        'year' => old('year'),
    ];
@endphp

// Product data for JavaScript access
const productData = @json($productDataArray);

// Old values from form validation
const oldValues = @json($oldValuesArray);

// Include the same category attributes configuration from create form
const categoryAttributes = {
    'fashion': ['brand', 'size', 'model'],
    'pakaian-pria': ['brand', 'size', 'model'],
    'pakaian-wanita': ['brand', 'size', 'model'],
    'sepatu': ['brand', 'size', 'model'],
    'tas': ['brand', 'size', 'model'],
    'aksesoris-fashion': ['brand', 'model'],
    'jam-tangan': ['brand', 'model'],
    'perhiasan': ['brand', 'model'],
    'electronic': ['brand', 'model'],
    'smartphone': ['brand', 'model'],
    'laptop': ['brand', 'model'],
    'tablet': ['brand', 'model'],
    'kamera': ['brand', 'model'],
    'audio': ['brand', 'model'],
    'tv-monitor': ['brand', 'model'],
    'gaming': ['brand', 'model'],
    'makananminuman': ['expired_date', 'weight'],
    'makanan': ['expired_date', 'weight'],
    'minuman': ['expired_date', 'weight'],
    'snack': ['expired_date', 'weight'],
    'buku': ['author', 'publisher', 'year'],
    'buku-fiksi': ['author', 'publisher', 'year'],
    'buku-non-fiksi': ['author', 'publisher', 'year'],
    'buku-pelajaran': ['author', 'publisher', 'year'],
    'komik': ['author', 'publisher', 'year'],
    'kendaraan': ['brand', 'model', 'year'],
    'motor': ['brand', 'model', 'year'],
    'mobil': ['brand', 'model', 'year'],
    'sepeda': ['brand', 'model', 'year'],
    'olahraga': ['brand', 'size'],
    'sepatu-olahraga': ['brand', 'size'],
    'pakaian-olahraga': ['brand', 'size'],
    'alat-olahraga': ['brand', 'size'],
    'default': ['brand']
};

function getCategorySlug(categoryName) {
    const slugMap = {
        'Elektronik': 'electronic',
        'Fashion': 'fashion',
        'Makanan & Minuman': 'makananminuman',
        'Buku': 'buku',
        'Kendaraan': 'kendaraan',
        'Olahraga': 'olahraga',
    };
    return slugMap[categoryName] || categoryName.toLowerCase().replace(/\s+/g, '-');
}

// Load sub-categories when parent category is selected
document.getElementById('parent_category_id')?.addEventListener('change', function() {
    const parentId = this.value;
    const subCategorySelect = document.getElementById('category_id');
    const attributesContainer = document.getElementById('product-attributes');
    
    subCategorySelect.innerHTML = '<option value="">Memuat sub-kategori...</option>';
    subCategorySelect.disabled = true;
    attributesContainer.innerHTML = '';
    
    if (!parentId) return;
    
    fetch(`/api/products/sub-categories?parent_id=${parentId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                subCategorySelect.innerHTML = '<option value="">Pilih Sub Kategori</option>';
                data.data.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.id;
                    option.textContent = category.name;
                    const currentCategoryId = '{{ $product->category_id }}';
                    if (category.id === currentCategoryId) {
                        option.selected = true;
                    }
                    subCategorySelect.appendChild(option);
                });
                subCategorySelect.disabled = false;
                // Trigger change to load attributes
                subCategorySelect.dispatchEvent(new Event('change'));
            } else {
                subCategorySelect.innerHTML = '<option value="{{ $product->category_id }}" selected>{{ $product->category->name }}</option>';
                subCategorySelect.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error loading sub-categories:', error);
        });
});

// Load dynamic attributes when sub-category is selected
document.getElementById('category_id')?.addEventListener('change', function() {
    const categoryId = this.value;
    const categoryName = this.options[this.selectedIndex].textContent.trim();
    const parentSelect = document.getElementById('parent_category_id');
    const parentOption = parentSelect.options[parentSelect.selectedIndex];
    const parentName = parentOption.textContent;
    
    const attributesContainer = document.getElementById('product-attributes');
    attributesContainer.innerHTML = '';
    
    if (!categoryId) return;
    
    const categorySlug = categoryName.toLowerCase().replace(/\s+/g, '-');
    const parentSlug = getCategorySlug(parentName);
    const attributes = categoryAttributes[categorySlug] || categoryAttributes[parentSlug] || categoryAttributes['default'];
    
    const gridCols = attributes.length > 2 ? 'md:grid-cols-3' : attributes.length === 2 ? 'md:grid-cols-2' : '';
    const attributesHTML = `
        <div class="grid grid-cols-1 ${gridCols} gap-4">
            ${attributes.map(attr => {
                const labels = {
                    'brand': 'Brand/Merek',
                    'size': 'Ukuran',
                    'model': 'Model',
                    'expired_date': 'Tanggal Kadaluarsa',
                    'weight': 'Berat (gram)',
                    'author': 'Penulis',
                    'publisher': 'Penerbit',
                    'year': 'Tahun Terbit',
                };
                
                const inputType = attr === 'expired_date' ? 'date' : 
                                 attr === 'weight' || attr === 'year' ? 'number' : 'text';
                
                // Get old value from form validation or product data
                const oldValue = oldValues[attr] !== undefined ? oldValues[attr] : (productData[attr] || '');
                
                return `
                    <div>
                        <label for="${attr}" class="block text-sm font-medium text-gray-700 mb-2">
                            ${labels[attr] || attr}
                        </label>
                        <input 
                            type="${inputType}" 
                            id="${attr}" 
                            name="${attr}" 
                            value="${oldValue || ''}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                            ${attr === 'weight' ? 'min="0" step="1"' : ''}
                            ${attr === 'year' ? 'min="1900" max="' + (new Date().getFullYear() + 1) + '"' : ''}
                        >
                    </div>
                `;
            }).join('')}
        </div>
    `;
    
    attributesContainer.innerHTML = attributesHTML;
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const parentSelect = document.getElementById('parent_category_id');
    if (parentSelect && parentSelect.value) {
        parentSelect.dispatchEvent(new Event('change'));
    }
});

function previewImages(input) {
    const container = document.getElementById('image-preview-container');
    container.innerHTML = '';
    
    if (input.files && input.files.length > 0) {
        Array.from(input.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative';
                div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg border border-gray-300">
                    <span class="absolute top-1 right-1 bg-black/50 text-white text-xs px-2 py-1 rounded">Baru</span>
                `;
                container.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }
}
</script>
@endpush
@endsection

