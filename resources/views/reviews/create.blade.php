@extends('layouts.app')

@section('title', 'Beri Review - Reloved')

@section('content')
<div class="container mx-auto px-8 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Beri Review</h1>

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-8 border border-border p-8">
            <!-- Transaction Info -->
            <div class="mb-6 pb-6 border-b border-border">
                <h2 class="text-lg font-semibold text-text-primary mb-2">{{ $transaction->product->title }}</h2>
                <p class="text-sm text-text-secondary">
                    Review untuk: <span class="font-semibold">{{ $reviewedUser->name }}</span>
                </p>
            </div>

            <form method="POST" action="{{ route('reviews.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">

                <!-- Rating -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Rating <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-2" id="rating-container">
                        @for($i = 1; $i <= 5; $i++)
                            <button 
                                type="button" 
                                onclick="setRating({{ $i }})"
                                class="rating-star w-12 h-12 text-gray-300 hover:text-yellow-400 transition"
                                data-rating="{{ $i }}"
                            >
                                <svg class="w-full h-full fill-current" viewBox="0 0 20 20">
                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                </svg>
                            </button>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" id="rating" value="0" required>
                    <p class="text-xs text-gray-500 mt-2">Klik bintang untuk memberikan rating</p>
                </div>

                <!-- Comment -->
                <div>
                    <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Komentar (Opsional)</label>
                    <textarea 
                        id="comment" 
                        name="comment" 
                        rows="6"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                        placeholder="Bagikan pengalaman Anda..."
                    >{{ old('comment') }}</textarea>
                </div>

                <!-- Images -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto (Opsional, Maksimal 5)</label>
                    <div class="grid grid-cols-5 gap-2 mb-2" id="image-preview-container">
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
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maksimal 2MB per gambar.</p>
                </div>

                <!-- Submit Button -->
                <div class="flex gap-4">
                    <button 
                        type="submit" 
                        class="flex-1 bg-primary text-white px-6 py-3 rounded-8 font-semibold hover:bg-primary/90 transition"
                    >
                        Kirim Review
                    </button>
                    <a 
                        href="{{ route('transactions.show', $transaction->id) }}" 
                        class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-8 font-semibold hover:bg-gray-300 transition text-center"
                    >
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let selectedRating = 0;

function setRating(rating) {
    selectedRating = rating;
    document.getElementById('rating').value = rating;
    
    // Update star display
    const stars = document.querySelectorAll('.rating-star');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.remove('text-gray-300');
            star.classList.add('text-yellow-400');
        } else {
            star.classList.remove('text-yellow-400');
            star.classList.add('text-gray-300');
        }
    });
}

function previewImages(input) {
    const container = document.getElementById('image-preview-container');
    container.innerHTML = '';
    
    if (input.files && input.files.length > 0) {
        Array.from(input.files).forEach((file, index) => {
            if (index >= 5) return; // Max 5 images
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative';
                div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-20 object-cover rounded-lg border border-gray-300">
                    <span class="absolute top-1 right-1 bg-black/50 text-white text-xs px-1 py-0.5 rounded">${index + 1}</span>
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

