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

            <!-- Category -->
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Kategori <span class="text-red-500">*</span>
                </label>
                <select 
                    id="category_id" 
                    name="category_id" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    required
                >
                    <option value="">Pilih Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @foreach($category->children as $child)
                            <option value="{{ $child->id }}" {{ old('category_id') == $child->id ? 'selected' : '' }}>
                                &nbsp;&nbsp;{{ $child->name }}
                            </option>
                        @endforeach
                    @endforeach
                </select>
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
                    <option value="lumayan_baru" {{ old('condition') == 'lumayan_baru' ? 'selected' : '' }}>Lumayan Baru</option>
                    <option value="bekas" {{ old('condition') == 'bekas' ? 'selected' : '' }}>Bekas</option>
                    <option value="rusak" {{ old('condition') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                </select>
            </div>

            <!-- Brand, Size, Model -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="brand" class="block text-sm font-medium text-gray-700 mb-2">Brand</label>
                    <input 
                        type="text" 
                        id="brand" 
                        name="brand" 
                        value="{{ old('brand') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    >
                </div>
                <div>
                    <label for="size" class="block text-sm font-medium text-gray-700 mb-2">Ukuran</label>
                    <input 
                        type="text" 
                        id="size" 
                        name="size" 
                        value="{{ old('size') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    >
                </div>
                <div>
                    <label for="model" class="block text-sm font-medium text-gray-700 mb-2">Model</label>
                    <input 
                        type="text" 
                        id="model" 
                        name="model" 
                        value="{{ old('model') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    >
                </div>
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
</script>
@endpush
@endsection

