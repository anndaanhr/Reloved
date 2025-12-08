@extends('layouts.app')

@section('title', 'Tambah Produk - Reloved')

@section('content')
<div class="container mx-auto px-8 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-text-primary mb-8">Tambah Produk</h1>

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-8">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="bg-white rounded-8 border border-border p-8 space-y-6">
            @csrf

            <!-- Images -->
            <div>
                <label class="block text-sm font-medium text-text-secondary mb-2">
                    Foto Produk <span class="text-red-500">*</span> (Maksimal 10 gambar)
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
                    required
                    onchange="previewImages(this)"
                >
                <p class="mt-1 text-xs text-gray-500">Maksimal 10 gambar. Format: JPG, PNG. Maksimal 2MB per gambar.</p>
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
                    value="{{ old('title') }}"
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
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('parent_category_id') == $category->id ? 'selected' : '' }}>
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
                        disabled
                    >
                        <option value="">Pilih Kategori Utama terlebih dahulu</option>
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
                >{{ old('description') }}</textarea>
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
                    value="{{ old('price') }}"
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
                    <option value="baru" {{ old('condition') == 'baru' ? 'selected' : '' }}>Baru</option>
                    <option value="seperti_baru" {{ old('condition') == 'seperti_baru' ? 'selected' : '' }}>Seperti Baru</option>
                    <option value="bekas_bagus" {{ old('condition') == 'bekas_bagus' ? 'selected' : '' }}>Bekas (Bagus)</option>
                    <option value="bekas_cukup" {{ old('condition') == 'bekas_cukup' ? 'selected' : '' }}>Bekas (Cukup)</option>
                </select>
            </div>

            <!-- Dynamic Attributes based on Category -->
            <div id="product-attributes" class="space-y-4">
                <!-- Attributes will be loaded dynamically via JavaScript based on selected category -->
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
                    value="{{ old('stock', 1) }}"
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
                            {{ in_array('meetup', old('deal_method', [])) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-primary focus:ring-primary"
                        >
                        <span class="ml-2 text-gray-700">Meet-up (COD)</span>
                    </label>
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            name="deal_method[]" 
                            value="shipping"
                            {{ in_array('shipping', old('deal_method', [])) ? 'checked' : '' }}
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
                    Tambah Produk
                </button>
                <a 
                    href="{{ route('products.index') }}" 
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
// Category configuration for dynamic attributes
const categoryAttributes = {
    // Fashion categories
    'fashion': ['brand', 'size', 'model'],
    'pakaian-pria': ['brand', 'size', 'model'],
    'pakaian-wanita': ['brand', 'size', 'model'],
    'sepatu': ['brand', 'size', 'model'],
    'tas': ['brand', 'size', 'model'],
    'aksesoris-fashion': ['brand', 'model'],
    'jam-tangan': ['brand', 'model'],
    'perhiasan': ['brand', 'model'],
    
    // Electronics
    'electronic': ['brand', 'model'],
    'smartphone': ['brand', 'model'],
    'laptop': ['brand', 'model'],
    'tablet': ['brand', 'model'],
    'kamera': ['brand', 'model'],
    'audio': ['brand', 'model'],
    'tv-monitor': ['brand', 'model'],
    'gaming': ['brand', 'model'],
    
    // Food & Beverage
    'makananminuman': ['expired_date', 'weight'],
    'makanan': ['expired_date', 'weight'],
    'minuman': ['expired_date', 'weight'],
    'snack': ['expired_date', 'weight'],
    
    // Books
    'buku': ['author', 'publisher', 'year'],
    'buku-fiksi': ['author', 'publisher', 'year'],
    'buku-non-fiksi': ['author', 'publisher', 'year'],
    'buku-pelajaran': ['author', 'publisher', 'year'],
    'komik': ['author', 'publisher', 'year'],
    
    // Vehicles
    'kendaraan': ['brand', 'model', 'year'],
    'motor': ['brand', 'model', 'year'],
    'mobil': ['brand', 'model', 'year'],
    'sepeda': ['brand', 'model', 'year'],
    
    // Sports
    'olahraga': ['brand', 'size'],
    'sepatu-olahraga': ['brand', 'size'],
    'pakaian-olahraga': ['brand', 'size'],
    'alat-olahraga': ['brand', 'size'],
    
    // Default (for categories without specific attributes)
    'default': ['brand']
};

// Get category slug from category name or ID
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

function previewImages(input) {
    const container = document.getElementById('image-preview-container');
    container.innerHTML = '';
    
    if (input.files && input.files.length > 0) {
        Array.from(input.files).forEach((file, index) => {
            if (index >= 10) return; // Max 10 images
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative';
                div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg border border-gray-300">
                    <span class="absolute top-1 right-1 bg-black/50 text-white text-xs px-2 py-1 rounded">${index + 1}</span>
                `;
                container.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }
}

// Load sub-categories when parent category is selected
document.getElementById('parent_category_id')?.addEventListener('change', function() {
    const parentId = this.value;
    const subCategorySelect = document.getElementById('category_id');
    const attributesContainer = document.getElementById('product-attributes');
    
    // Reset sub-category
    subCategorySelect.innerHTML = '<option value="">Pilih Kategori Utama terlebih dahulu</option>';
    subCategorySelect.disabled = true;
    
    // Clear attributes
    attributesContainer.innerHTML = '';
    
    if (!parentId) return;
    
    // Fetch sub-categories
    fetch(`/api/products/sub-categories?parent_id=${parentId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                subCategorySelect.innerHTML = '<option value="">Pilih Sub Kategori</option>';
                data.data.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.id;
                    option.textContent = category.name;
                    subCategorySelect.appendChild(option);
                });
                subCategorySelect.disabled = false;
            } else {
                // No sub-categories, use parent category directly
                subCategorySelect.innerHTML = '<option value="">Kategori ini tidak memiliki sub-kategori</option>';
                // Allow using parent category as final category
                const option = document.createElement('option');
                option.value = parentId;
                option.textContent = 'Gunakan kategori utama';
                subCategorySelect.appendChild(option);
                subCategorySelect.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error loading sub-categories:', error);
            subCategorySelect.innerHTML = '<option value="">Error memuat sub-kategori</option>';
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
    
    // Try to get attributes by sub-category slug first, then parent
    const categorySlug = categoryName.toLowerCase().replace(/\s+/g, '-');
    const parentSlug = getCategorySlug(parentName);
    
    // Get attributes for this category (check sub-category first, then parent, then default)
    const attributes = categoryAttributes[categorySlug] || 
                      categoryAttributes[parentSlug] || 
                      categoryAttributes['default'];
    
    // Render attributes dynamically
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
                
                return `
                    <div>
                        <label for="${attr}" class="block text-sm font-medium text-gray-700 mb-2">
                            ${labels[attr] || attr}
                        </label>
                        <input 
                            type="${inputType}" 
                            id="${attr}" 
                            name="${attr}" 
                            value="{{ old('${attr}') }}"
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
</script>
@endpush
@endsection

