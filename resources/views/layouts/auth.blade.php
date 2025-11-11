<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Reloved - Marketplace Preloved')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body class="font-sans antialiased bg-background min-h-screen flex flex-col">
    <!-- Main Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-auto">
        <div class="container mx-auto px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- About -->
                <div>
                    <h3 class="font-bold text-lg mb-4 text-white">Tentang Kami</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-sm text-gray-400 hover:text-white transition">Tentang Marketplace</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-white transition">Karir</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-white transition">Blog</a></li>
                    </ul>
                </div>

                <!-- Help -->
                <div>
                    <h3 class="font-bold text-lg mb-4 text-white">Bantuan</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-sm text-gray-400 hover:text-white transition">Pusat Bantuan</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-white transition">Hubungi Kami</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-white transition">FAQ</a></li>
                    </ul>
                </div>

                <!-- Legal -->
                <div>
                    <h3 class="font-bold text-lg mb-4 text-white">Kebijakan</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-sm text-gray-400 hover:text-white transition">Syarat & Ketentuan</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-white transition">Kebijakan Privasi</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-white transition">Kebijakan Pengembalian</a></li>
                    </ul>
                </div>

                <!-- Social -->
                <div>
                    <h3 class="font-bold text-lg mb-4 text-white">Ikuti Kami</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-sm text-gray-400 hover:text-white transition">Facebook</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-white transition">Instagram</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-white transition">Twitter</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                <p class="text-sm text-gray-400">&copy;{{ date('Y') }} Reloved. Semua hak dilindungi.</p>
            </div>
        </div>
    </footer>

            @stack('scripts')
            @auth
            <script>
                // Load unread notification count
                async function loadNotificationCount() {
                    try {
                        const response = await fetch('{{ route('api.notifications.unread-count') }}');
                        const data = await response.json();
                        if (data.success && data.count > 0) {
                            const badge = document.getElementById('notification-badge');
                            badge.textContent = data.count > 99 ? '99+' : data.count;
                            badge.classList.remove('hidden');
                        } else {
                            document.getElementById('notification-badge').classList.add('hidden');
                        }
                    } catch (error) {
                        console.error('Error loading notification count:', error);
                    }
                }

                // Load on page load
                loadNotificationCount();

                // Refresh every 30 seconds
                setInterval(loadNotificationCount, 30000);
            </script>
            @endauth
            <script>
                // Load provinces for header search (using same API as registration form)
                document.addEventListener('DOMContentLoaded', function() {
                    const headerProvinceSelect = document.getElementById('header-province');
                    
                    if (!headerProvinceSelect) {
                        console.error('Header province select not found');
                        return;
                    }

                    // Show loading state
                    headerProvinceSelect.disabled = true;
                    headerProvinceSelect.innerHTML = '<option value="">Memuat provinsi...</option>';

                    // Load provinces from RajaOngkir API (same as registration form)
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
                        headerProvinceSelect.disabled = false;
                        headerProvinceSelect.innerHTML = '<option value="">Semua Provinsi</option>';
                        
                        if (data && data.success && data.data && Array.isArray(data.data) && data.data.length > 0) {
                            const currentProvince = '{{ request("province", "") }}';
                            
                            data.data.forEach(province => {
                                const option = document.createElement('option');
                                // Handle different field names: province_id, id, code (same as registration form)
                                const provinceId = province.province_id || province.id || province.code || '';
                                const provinceName = province.province || province.name || province.title || 'Unknown';
                                
                                option.value = provinceId;
                                option.textContent = provinceName;
                                // Store province name in data attribute (same as registration form)
                                option.setAttribute('data-name', provinceName);
                                
                                // Select current province if matches (by name, since filter uses province name)
                                if (currentProvince && provinceName === currentProvince) {
                                    option.selected = true;
                                }
                                
                                headerProvinceSelect.appendChild(option);
                            });
                        } else {
                            console.error('Provinsi kosong atau format tidak valid:', data);
                            headerProvinceSelect.innerHTML = '<option value="">Gagal memuat provinsi</option>';
                        }
                    })
                    .catch(error => {
                        console.error('Error loading provinces:', error);
                        headerProvinceSelect.disabled = false;
                        headerProvinceSelect.innerHTML = '<option value="">Gagal memuat provinsi</option>';
                    });
                });
            </script>
        </body>
        </html>


