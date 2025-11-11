@extends('layouts.auth')

@section('title', 'Masuk - Reloved')

@section('content')
<div class="min-h-screen flex bg-background">
    <!-- Left Side - Background Image -->
    <div class="hidden lg:block lg:w-1/2 relative overflow-hidden m-4 rounded-[25px]">
        <div class="absolute inset-0">
            <img src="{{ asset('images/login-background.png') }}" alt="Login Background" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-br from-primary-light/10 to-primary-dark/10"></div>
        </div>
    </div>

    <!-- Right Side - Login Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center px-8 py-12 bg-background">
        <div class="w-full max-w-md">
            <!-- Logo -->
            <div class="flex flex-col items-center gap-5 mb-8">
                <div class="flex flex-col items-center">
                    <img src="{{ asset('images/logo/logorelovedheader (2).png') }}" alt="Reloved Logo" class="h-12 w-auto">
                </div>
                <h1 class="text-xl font-semibold text-text-primary">Masuk</h1>
            </div>

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

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
                            autofocus
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

                <!-- Password -->
                <div class="space-y-2">
                    <label for="password" class="block text-lg font-medium text-text-primary">Password</label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password"
                            autocomplete="current-password"
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

                <!-- Forget Password -->
                <div>
                    <a href="#" class="text-base font-light text-primary hover:opacity-80 transition">Forget password?</a>
                </div>

                <!-- Login Button -->
                <button 
                    type="submit"
                    id="login-submit"
                    name="login-submit"
                    class="w-full h-[51px] bg-primary text-white rounded-5 font-semibold text-base hover:opacity-90 transition"
                >
                    LogIn
                </button>

                <!-- Other Login Options -->
                <p class="text-center text-base font-light text-primary-dark">Other login options</p>

                <!-- Google Login Button -->
                <a 
                    href="{{ route('auth.google') }}"
                    class="w-full h-[51px] flex items-center justify-center gap-3 bg-white border-2 border-border text-text-primary rounded-5 font-semibold text-base hover:opacity-90 hover:border-primary transition"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    <span>Masuk dengan Google</span>
                </a>

                <!-- Register Link -->
                <p class="text-center text-base font-medium">
                    <span class="text-text-primary">Belum memiliki akun?</span>
                    <a href="{{ route('register') }}" class="text-primary hover:opacity-80 transition font-semibold">Daftar Akun</a>
                </p>
            </form>
        </div>
    </div>
</div>
@endsection

