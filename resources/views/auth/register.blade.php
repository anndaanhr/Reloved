@extends('layouts.auth')

@section('title', 'Daftar Akun - Reloved')

@section('content')
<div class="min-h-screen flex bg-background">
    <!-- Left Side - Background Image -->
    <div class="hidden lg:block lg:w-1/2 relative overflow-hidden m-4 rounded-[25px]">
        <div class="absolute inset-0">
            <img src="{{ asset('images/login-background.png') }}" alt="Register Background" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-br from-primary-light/10 to-primary-dark/10"></div>
        </div>
    </div>

    <!-- Right Side - Register Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center px-8 py-12 bg-background overflow-y-auto">
        <div class="w-full max-w-md py-8">
            <!-- Logo -->
            <div class="flex flex-col items-center gap-5 mb-10">
                <div class="flex flex-col items-center">
                    <img src="{{ asset('images/logo/logorelovedheader (2).png') }}" alt="Reloved Logo" class="h-12 w-auto">
                </div>
                <h1 class="text-xl font-semibold text-text-primary">Daftar Akun</h1>
            </div>

            <!-- Register Form -->
            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <!-- Province -->
                <div class="space-y-2">
                    <label for="province" class="block text-lg font-medium text-text-primary">Provinsi</label>
                    <div class="relative">
                        <select 
                            id="province" 
                            name="province_id"
                            autocomplete="address-level1"
                            class="w-full h-[51px] pl-12 pr-10 py-3.5 bg-white border border-border rounded-10 text-base font-medium text-text-primary focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition appearance-none disabled:bg-gray-100 disabled:cursor-not-allowed"
                            required
                        >
                            <option value="">Pilih Provinsi</option>
                        </select>
                        <input type="hidden" id="province_name" name="province" value="">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none">
                            <svg class="w-5 h-5 text-placeholder" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none z-10">
                            <svg class="w-5 h-5 text-placeholder" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                    @error('province')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- City -->
                <div class="space-y-2">
                    <label for="city" class="block text-lg font-medium text-text-primary">Kota</label>
                    <div class="relative">
                        <select 
                            id="city" 
                            name="city_id"
                            autocomplete="address-level2"
                            class="w-full h-[51px] pl-12 pr-10 py-3.5 bg-white border border-border rounded-10 text-base font-medium text-text-primary focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition appearance-none disabled:bg-gray-100 disabled:cursor-not-allowed"
                            disabled
                            required
                        >
                            <option value="">Pilih Kota</option>
                        </select>
                        <input type="hidden" id="city_name" name="city" value="">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none">
                            <svg class="w-5 h-5 text-placeholder" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none z-10">
                            <svg class="w-5 h-5 text-placeholder" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                    @error('city')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="space-y-2">
                    <label for="email" class="block text-lg font-medium text-text-primary">Email</label>
                    <div class="relative">
                        <input 
                            type="email" 
                            id="email" 
                            name="email"
                            autocomplete="email"
                            value="{{ old('email') }}"
                            placeholder="Masukkan Email Anda" 
                            class="w-full h-[51px] px-10 py-3.5 bg-white border border-border rounded-10 text-base font-medium text-text-primary placeholder:text-placeholder focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition"
                            required
                        >
                        <div class="absolute left-4 top-1/2 -translate-y-1/2">
                            <svg class="w-5 h-5 text-placeholder" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                        </div>
                    </div>
                    @error('email')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div class="space-y-2">
                    <label for="name" class="block text-lg font-medium text-text-primary">Nama</label>
                    <div class="relative">
                        <input 
                            type="text" 
                            id="name" 
                            name="name"
                            autocomplete="name"
                            value="{{ old('name') }}"
                            placeholder="Masukkan Nama Anda" 
                            class="w-full h-[51px] px-10 py-3.5 bg-white border border-border rounded-10 text-base font-medium text-text-primary placeholder:text-placeholder focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition"
                            required
                        >
                        <div class="absolute left-4 top-1/2 -translate-y-1/2">
                            <svg class="w-5 h-5 text-placeholder" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                    </div>
                    @error('name')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="space-y-2">
                    <label for="password" class="block text-lg font-medium text-text-primary">Password</label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password"
                            autocomplete="new-password"
                            placeholder="Masukkan Password Anda" 
                            class="w-full h-[51px] px-10 py-3.5 bg-white border border-border rounded-10 text-base font-medium text-text-primary placeholder:text-placeholder focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition"
                            required
                        >
                        <div class="absolute left-4 top-1/2 -translate-y-1/2">
                            <svg class="w-5 h-5 text-placeholder" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                    </div>
                    @error('password')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Confirmation -->
                <div class="space-y-2">
                    <label for="password_confirmation" class="block text-lg font-medium text-text-primary">Konfirmasi Password</label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password_confirmation" 
                            name="password_confirmation"
                            autocomplete="new-password"
                            placeholder="Konfirmasi Password Anda" 
                            class="w-full h-[51px] px-10 py-3.5 bg-white border border-border rounded-10 text-base font-medium text-text-primary placeholder:text-placeholder focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition"
                            required
                        >
                        <div class="absolute left-4 top-1/2 -translate-y-1/2">
                            <svg class="w-5 h-5 text-placeholder" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Register Button -->
                <button 
                    type="submit"
                    id="register-submit"
                    name="register-submit"
                    class="w-full h-[51px] bg-primary text-white rounded-5 font-semibold text-base hover:opacity-90 transition"
                >
                    Daftar
                </button>

                <!-- Login Link -->
                <p class="text-center text-base font-medium">
                    <span class="text-text-primary">Sudah Memiliki Akun?</span>
                    <a href="{{ route('login') }}" class="text-primary hover:opacity-80 transition font-semibold">Masuk</a>
                </p>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const provinceSelect = document.getElementById('province');
    const citySelect = document.getElementById('city');

    // Show loading state
    provinceSelect.disabled = true;
    provinceSelect.innerHTML = '<option value="">Memuat provinsi...</option>';

    // Load provinces
    fetch('{{ route("api.shipping.provinces") }}', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin'
    })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || `HTTP error! status: ${response.status}`);
                }).catch(() => {
                    throw new Error(`HTTP error! status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            provinceSelect.disabled = false;
            provinceSelect.innerHTML = '<option value="">Pilih Provinsi</option>';
            
            if (data && data.success && data.data && Array.isArray(data.data) && data.data.length > 0) {
                data.data.forEach(province => {
                    const option = document.createElement('option');
                    // Handle different field names: province_id, id, code
                    const provinceId = province.province_id || province.id || province.code || '';
                    const provinceName = province.province || province.name || province.title || 'Unknown';
                    option.value = provinceId;
                    option.textContent = provinceName;
                    // Store province name in data attribute
                    option.setAttribute('data-name', provinceName);
                    provinceSelect.appendChild(option);
                });
            } else {
                console.error('Provinsi kosong atau format tidak valid:', data);
                provinceSelect.innerHTML = '<option value="">Gagal memuat provinsi. Cek log untuk detail.</option>';
            }
        })
        .catch(error => {
            console.error('Error loading provinces:', error);
            provinceSelect.disabled = false;
            provinceSelect.innerHTML = '<option value="">Gagal memuat provinsi. Cek log untuk detail.</option>';
        });

    // Load cities when province is selected
    provinceSelect.addEventListener('change', function() {
        const provinceId = this.value;
        const selectedOption = this.options[this.selectedIndex];
        const provinceName = selectedOption ? selectedOption.getAttribute('data-name') || selectedOption.textContent : '';
        
        // Update hidden province name field
        document.getElementById('province_name').value = provinceName;
        
        if (!provinceId) {
            citySelect.innerHTML = '<option value="">Pilih Kota</option>';
            citySelect.disabled = true;
            document.getElementById('city_name').value = '';
            return;
        }
        
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
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                citySelect.disabled = false;
                citySelect.innerHTML = '<option value="">Pilih Kota</option>';
                
                if (data && data.success && data.data && Array.isArray(data.data) && data.data.length > 0) {
                    data.data.forEach(city => {
                        const option = document.createElement('option');
                        // Handle different field names: city_id, id, code
                        const cityId = city.city_id || city.id || city.code || '';
                        const cityName = city.city_name || city.name || city.title || 'Unknown';
                        option.value = cityId;
                        option.textContent = cityName;
                        // Store city name in data attribute
                        option.setAttribute('data-name', cityName);
                        citySelect.appendChild(option);
                    });
                } else {
                    citySelect.innerHTML = '<option value="">Gagal memuat kota</option>';
                }
            })
            .catch(() => {
                citySelect.disabled = false;
                citySelect.innerHTML = '<option value="">Gagal memuat kota</option>';
            });
    });
    
    // Update city name when city is selected
    citySelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const cityName = selectedOption ? selectedOption.getAttribute('data-name') || selectedOption.textContent : '';
        document.getElementById('city_name').value = cityName;
    });
});
</script>
@endpush
@endsection

