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
    <!-- Header -->
    <header class="bg-white border-b border-gray-200" x-data="{ mobileMenuOpen: false }">
        <div class="container mx-auto px-8">
            <!-- Top Navigation -->
            <div class="flex items-center justify-between gap-4 py-4">
                <!-- Logo and Categories -->
                <div class="flex items-center gap-6 flex-1">
                    <!-- Logo -->
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        <img src="{{ asset('images/logo/logorelovedheader (1).png') }}" alt="Reloved Logo" class="h-10 w-auto">
                    </a>

                    <!-- Navigation Menu -->
                    <nav class="hidden lg:flex items-center gap-6 overflow-x-auto">
                        @php
                            $headerCategories = \App\Models\Category::active()->root()->limit(5)->get();
                        @endphp
                        @foreach($headerCategories as $category)
                            <a href="{{ route('products.index', ['category' => $category->id]) }}" class="text-sm text-text-secondary hover:text-primary whitespace-nowrap transition">
                                {{ $category->name }}
                            </a>
                        @endforeach
                        <a href="{{ route('products.index') }}" class="text-sm text-text-secondary hover:text-primary whitespace-nowrap transition">
                            Semua Kategori
                        </a>
                    </nav>
                </div>

                <!-- Auth Buttons / User Menu -->
                <div class="flex items-center gap-4">
                    @auth
                        <!-- User Menu -->
                        <div class="flex items-center gap-4" x-data="{ open: false }">
                            <a href="{{ route('products.create') }}" class="bg-primary-light text-white px-5 py-2.5 rounded-10 font-semibold text-sm hover:bg-primary-light/90 transition">
                                Jual
                            </a>
                            <!-- Chat -->
                            <a href="{{ route('chat.index') }}" class="relative p-2" title="Pesan">
                                <svg class="w-6 h-6 text-text-secondary hover:text-primary transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                <span id="chat-badge" class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden">0</span>
                            </a>
                            <!-- Notifications -->
                            <a href="{{ route('notifications.index') }}" class="relative p-2" title="Notifikasi">
                                <svg class="w-6 h-6 text-text-secondary hover:text-primary transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <span id="notification-badge" class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden">0</span>
                            </a>
                            <!-- User avatar -->
                            <div class="relative">
                                @php
                                    $currentUser = auth()->user()->fresh();
                                @endphp
                                <button @click="open = !open" class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300 transition overflow-hidden">
                                    @if($currentUser && $currentUser->avatar)
                                        <img src="{{ $currentUser->avatar }}" alt="{{ $currentUser->name }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <span class="text-text-secondary font-semibold text-sm hidden">{{ substr($currentUser->name, 0, 1) }}</span>
                                    @else
                                        <span class="text-text-secondary font-semibold text-sm">{{ substr($currentUser->name ?? 'U', 0, 1) }}</span>
                                    @endif
                                </button>
                                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-10 shadow-lg border border-border py-2 z-50" style="display: none;">
                                    <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-text-primary hover:bg-gray-50 transition">Profile</a>
                                    <a href="{{ route('chat.index') }}" class="block px-4 py-2 text-sm text-text-primary hover:bg-gray-50 transition">Pesan</a>
                                    <a href="{{ route('notifications.index') }}" class="block px-4 py-2 text-sm text-text-primary hover:bg-gray-50 transition">Notifikasi</a>
                                    <a href="{{ route('wishlist.index') }}" class="block px-4 py-2 text-sm text-text-primary hover:bg-gray-50 transition">Favorit</a>
                                    <a href="{{ route('transactions.index') }}" class="block px-4 py-2 text-sm text-text-primary hover:bg-gray-50 transition">Transaksi</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-text-primary hover:bg-gray-50 transition">Logout</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('register') }}" class="text-sm text-text-secondary font-semibold hover:text-primary transition">Daftar Akun</a>
                        <a href="{{ route('login') }}" class="text-sm text-text-secondary font-semibold hover:text-primary transition">Masuk</a>
                        <a href="{{ route('products.create') }}" class="bg-primary-light text-white px-5 py-2.5 rounded-10 font-semibold text-sm hover:bg-primary-light/90 transition">
                            Jual
                        </a>
                    @endauth
                    <!-- Burger Menu -->
                    <button class="lg:hidden w-7 h-7 text-gray-700" @click="mobileMenuOpen = !mobileMenuOpen">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div x-show="mobileMenuOpen" class="lg:hidden border-t border-gray-200 py-4" style="display: none;">
                <nav class="flex flex-col gap-4">
                    @php
                        $mobileCategories = \App\Models\Category::active()->root()->limit(5)->get();
                    @endphp
                    @foreach($mobileCategories as $category)
                        <a href="{{ route('products.index', ['category' => $category->id]) }}" class="text-sm text-text-secondary hover:text-primary transition">
                            {{ $category->name }}
                        </a>
                    @endforeach
                    <a href="{{ route('products.index') }}" class="text-sm text-text-secondary hover:text-primary transition">
                        Semua Kategori
                    </a>
                </nav>
            </div>

            <!-- Search Bar -->
            <div class="flex items-center gap-3 pb-4" x-data="{ 
                searchQuery: '{{ request('search', '') }}', 
                selectedProvince: '',
                handleSearch() {
                    const provinceSelect = document.getElementById('header-province');
                    const selectedProvinceName = provinceSelect ? provinceSelect.options[provinceSelect.selectedIndex]?.text : '';
                    
                    let url = '{{ route('products.index') }}';
                    const params = [];
                    
                    if (this.searchQuery) {
                        params.push('search=' + encodeURIComponent(this.searchQuery));
                    }
                    
                    if (selectedProvinceName && selectedProvinceName !== 'Semua Provinsi' && selectedProvinceName !== 'Memuat provinsi...') {
                        params.push('province=' + encodeURIComponent(selectedProvinceName));
                    }
                    
                    if (params.length > 0) {
                        url += '?' + params.join('&');
                    }
                    
                    window.location.href = url;
                }
            }">
                <div class="flex-1 flex items-center border border-border rounded-10 bg-white px-4 py-2.5">
                    <input 
                        type="text" 
                        placeholder="Cari barang..." 
                        class="flex-1 outline-none text-sm text-text-primary placeholder:text-placeholder"
                        x-model="searchQuery"
                        @keyup.enter="handleSearch()"
                    >
                </div>
                <div class="relative">
                    <select 
                        id="header-province" 
                        class="appearance-none bg-gray-100 border border-border rounded-10 px-4 py-2.5 pr-10 text-sm text-text-primary focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition min-w-[180px] cursor-pointer"
                        style="-webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: none;"
                    >
                        <option value="">Semua Provinsi</option>
                    </select>
                    <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none z-10">
                        <svg class="w-5 h-5 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
                <button 
                    @click="handleSearch()"
                    class="bg-primary text-white px-6 py-2.5 rounded-10 font-semibold text-sm hover:opacity-90 transition"
                >
                    Cari
                </button>
            </div>
        </div>
    </header>

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
                        <li><a href="{{ route('pages.about') }}" class="text-sm text-gray-400 hover:text-white transition">Tentang Marketplace</a></li>
                        <li><a href="{{ route('pages.career') }}" class="text-sm text-gray-400 hover:text-white transition">Karir</a></li>
                        <li><a href="{{ route('pages.blog') }}" class="text-sm text-gray-400 hover:text-white transition">Blog</a></li>
                    </ul>
                </div>

                <!-- Help -->
                <div>
                    <h3 class="font-bold text-lg mb-4 text-white">Bantuan</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('pages.help') }}" class="text-sm text-gray-400 hover:text-white transition">Pusat Bantuan</a></li>
                        <li><a href="{{ route('pages.contact') }}" class="text-sm text-gray-400 hover:text-white transition">Hubungi Kami</a></li>
                        <li><a href="{{ route('pages.faq') }}" class="text-sm text-gray-400 hover:text-white transition">FAQ</a></li>
                    </ul>
                </div>

                <!-- Legal -->
                <div>
                    <h3 class="font-bold text-lg mb-4 text-white">Kebijakan</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('pages.terms') }}" class="text-sm text-gray-400 hover:text-white transition">Syarat & Ketentuan</a></li>
                        <li><a href="{{ route('pages.privacy') }}" class="text-sm text-gray-400 hover:text-white transition">Kebijakan Privasi</a></li>
                        <li><a href="{{ route('pages.return-policy') }}" class="text-sm text-gray-400 hover:text-white transition">Kebijakan Pengembalian</a></li>
                    </ul>
                </div>

                <!-- Social -->
                <div>
                    <h3 class="font-bold text-lg mb-4 text-white">Ikuti Kami</h3>
                    <div class="flex gap-4 mb-4">
                        <a href="https://www.tiktok.com/@preloved.co.id" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white transition">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                        </a>
                        <a href="https://www.instagram.com/preloved.app/" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white transition">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                        <a href="https://x.com/preloved_app" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white transition">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                    </div>
                    <p class="text-sm text-gray-400 mt-4">Dapatkan update terbaru dari kami</p>
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
                // Load unread notification count (only for offer/transaction notifications, not chat)
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

                // Load unread chat message count
                async function loadChatUnreadCount() {
                    try {
                        const response = await fetch('{{ route('api.chat.unread-count') }}');
                        const data = await response.json();
                        if (data.success && data.count > 0) {
                            const badge = document.getElementById('chat-badge');
                            badge.textContent = data.count > 99 ? '99+' : data.count;
                            badge.classList.remove('hidden');
                        } else {
                            document.getElementById('chat-badge').classList.add('hidden');
                        }
                    } catch (error) {
                        console.error('Error loading chat unread count:', error);
                    }
                }

                // Load on page load
                loadNotificationCount();
                loadChatUnreadCount();

                // Refresh every 30 seconds
                setInterval(loadNotificationCount, 30000);
                setInterval(loadChatUnreadCount, 30000);
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


