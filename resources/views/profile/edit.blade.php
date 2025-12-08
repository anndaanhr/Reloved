@extends('layouts.app')

@section('title', 'Edit Profile - Reloved')

@section('content')
<div class="container mx-auto px-8 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold text-text-primary mb-8">Edit Profile</h1>

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-8">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-8 border border-border p-8">
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Avatar -->
                <div>
                    <label class="block text-sm font-medium text-text-secondary mb-2">Foto Profil</label>
                    <div class="flex items-center gap-4">
                        <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-200 flex items-center justify-center">
                            @if($user->avatar)
                                <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-full h-full object-cover" id="avatar-preview">
                            @else
                                <span class="text-2xl font-semibold text-gray-600" id="avatar-placeholder">{{ substr($user->name, 0, 1) }}</span>
                            @endif
                        </div>
                        <div>
                            <input 
                                type="file" 
                                name="avatar" 
                                id="avatar" 
                                accept="image/*"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90"
                                onchange="previewAvatar(this)"
                            >
                            <p class="mt-1 text-xs text-gray-500">Maksimal 2MB. Format: JPG, PNG</p>
                        </div>
                    </div>
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name', $user->name) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                        required
                    >
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">No. Telepon</label>
                    <input 
                        type="text" 
                        id="phone" 
                        name="phone" 
                        value="{{ old('phone', $user->phone) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                        placeholder="08xxxxxxxxxx"
                    >
                </div>

                <!-- Province -->
                <div>
                    <label for="province" class="block text-sm font-medium text-gray-700 mb-2">Provinsi</label>
                    <select 
                        id="province" 
                        name="province_id" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    >
                        <option value="">Pilih Provinsi</option>
                        <!-- Will be populated via API -->
                    </select>
                    <input type="hidden" id="province_name" name="province" value="{{ old('province', $user->province) }}">
                </div>

                <!-- City -->
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-2">Kota</label>
                    <select 
                        id="city" 
                        name="city_id" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                        disabled
                    >
                        <option value="">Pilih Kota</option>
                    </select>
                    <input type="hidden" id="city_name" name="city" value="{{ old('city', $user->city) }}">
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
                        href="{{ route('profile.show') }}" 
                        class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-semibold hover:bg-gray-300 transition"
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
function previewAvatar(input) {
    const preview = document.getElementById('avatar-preview');
    const placeholder = document.getElementById('avatar-placeholder');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            if (preview) {
                preview.src = e.target.result;
            } else {
                const img = document.createElement('img');
                img.id = 'avatar-preview';
                img.src = e.target.result;
                img.className = 'w-full h-full object-cover';
                placeholder.parentElement.replaceChild(img, placeholder);
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Load provinces and cities for edit form
document.addEventListener('DOMContentLoaded', function() {
    const provinceSelect = document.getElementById('province');
    const citySelect = document.getElementById('city');
    const currentProvince = '{{ $user->province }}';
    const currentCity = '{{ $user->city }}';
    
    // Load provinces
    provinceSelect.disabled = true;
    provinceSelect.innerHTML = '<option value="">Memuat provinsi...</option>';
    
    fetch('{{ route("api.shipping.provinces") }}', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin'
    })
        .then(response => response.json())
        .then(data => {
            provinceSelect.disabled = false;
            provinceSelect.innerHTML = '<option value="">Pilih Provinsi</option>';
            
            if (data && data.success && data.data && Array.isArray(data.data)) {
                let currentProvinceId = null;
                data.data.forEach(province => {
                    const provinceId = province.province_id || province.id || province.code || '';
                    const provinceName = province.province || province.name || province.title || 'Unknown';
                    const option = document.createElement('option');
                    option.value = provinceId;
                    option.textContent = provinceName;
                    option.setAttribute('data-name', provinceName);
                    
                    // Select current province if matches
                    if (provinceName === currentProvince || provinceId === currentProvince) {
                        option.selected = true;
                        currentProvinceId = provinceId;
                    }
                    
                    provinceSelect.appendChild(option);
                });
                
                // If province is selected, load cities
                if (currentProvinceId) {
                    loadCities(currentProvinceId);
                }
            }
        })
        .catch(error => {
            console.error('Error loading provinces:', error);
            provinceSelect.disabled = false;
            provinceSelect.innerHTML = '<option value="">Gagal memuat provinsi</option>';
        });
    
    // Load cities when province is selected
    provinceSelect.addEventListener('change', function() {
        const provinceId = this.value;
        const selectedOption = this.options[this.selectedIndex];
        const provinceName = selectedOption ? selectedOption.getAttribute('data-name') || selectedOption.textContent : '';
        
        document.getElementById('province_name').value = provinceName;
        
        if (!provinceId) {
            citySelect.innerHTML = '<option value="">Pilih Kota</option>';
            citySelect.disabled = true;
            document.getElementById('city_name').value = '';
            return;
        }
        
        loadCities(provinceId);
    });
    
    // Update city name when city is selected
    citySelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const cityName = selectedOption ? selectedOption.getAttribute('data-name') || selectedOption.textContent : '';
        document.getElementById('city_name').value = cityName;
    });
    
    function loadCities(provinceId) {
        citySelect.disabled = true;
        citySelect.innerHTML = '<option value="">Memuat kota...</option>';
        
        fetch(`{{ route("api.shipping.cities") }}?province_id=${provinceId}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin'
        })
            .then(response => response.json())
            .then(data => {
                citySelect.disabled = false;
                citySelect.innerHTML = '<option value="">Pilih Kota</option>';
                
                if (data && data.success && data.data && Array.isArray(data.data)) {
                    data.data.forEach(city => {
                        const cityId = city.city_id || city.id || city.code || '';
                        const cityName = city.city_name || city.name || city.title || 'Unknown';
                        const option = document.createElement('option');
                        option.value = cityId;
                        option.textContent = cityName;
                        option.setAttribute('data-name', cityName);
                        
                        // Select current city if matches
                        if (cityName === currentCity || cityId === currentCity) {
                            option.selected = true;
                            document.getElementById('city_name').value = cityName;
                        }
                        
                        citySelect.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.error('Error loading cities:', error);
                citySelect.disabled = false;
                citySelect.innerHTML = '<option value="">Gagal memuat kota</option>';
            });
    }
});
</script>
@endpush
@endsection

