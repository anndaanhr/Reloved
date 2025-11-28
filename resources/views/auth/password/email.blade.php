@extends('layouts.app')

@section('title', 'Lupa Kata Sandi - Reloved')

@section('content')
<div class="min-h-screen flex bg-background">
    <!-- Left Side - Background Image -->
    <div class="hidden lg:block lg:w-1/2 relative overflow-hidden m-4 rounded-[25px]">
        <div class="absolute inset-0">
            <img src="{{ asset('images/login-background.png') }}" alt="Background" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-br from-primary-light/10 to-primary-dark/10"></div>
        </div>
    </div>

    <!-- Right Side - Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center px-8 py-12 bg-background">
        <div class="w-full max-w-md">
            <!-- Logo -->
            <div class="flex flex-col items-center gap-5 mb-8">
                <div class="flex flex-col items-center">
                    <img src="{{ asset('images/logo/logorelovedheader (2).png') }}" alt="Reloved Logo" class="h-12 w-auto">
                </div>
                <h1 class="text-xl font-semibold text-text-primary">Lupa Kata Sandi</h1>
            </div>

            @if (session('status'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('status') }}</span>
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf

                <!-- Email -->
                <div class="space-y-2">
                    <label for="email" class="block text-lg font-medium text-text-primary">Alamat Email</label>
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

                <!-- Submit Button -->
                <button 
                    type="submit"
                    class="w-full h-[51px] bg-primary text-white rounded-5 font-semibold text-base hover:opacity-90 transition"
                >
                    Kirim Link Reset Kata Sandi
                </button>

                <!-- Login Link -->
                <p class="text-center text-base font-medium">
                    <a href="{{ route('login') }}" class="text-primary hover:opacity-80 transition font-semibold">Kembali ke Halaman Login</a>
                </p>
            </form>
        </div>
    </div>
</div>
@endsection
