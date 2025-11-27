@extends('layouts.app')

@section('title', 'Reset Kata Sandi - Reloved')

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
                <h1 class="text-xl font-semibold text-text-primary">Reset Kata Sandi</h1>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <!-- Email -->
                <div class="space-y-2">
                    <label for="email" class="block text-lg font-medium text-text-primary">Alamat Email</label>
                    <div class="relative">
                        <input 
                            type="email" 
                            id="email" 
                            name="email"
                            autocomplete="email"
                            value="{{ $email ?? old('email') }}"
                            placeholder="Masukkan Email Anda" 
                            class="w-full h-[51px] px-10 py-3.5 bg-white border border-border rounded-10 text-base font-medium text-text-primary placeholder:text-placeholder focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition"
                            required
                            readonly
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
                    <label for="password" class="block text-lg font-medium text-text-primary">Password Baru</label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password"
                            autocomplete="new-password"
                            placeholder="Masukkan Password Baru Anda" 
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

                <!-- Confirm Password -->
                <div class="space-y-2">
                    <label for="password-confirm" class="block text-lg font-medium text-text-primary">Konfirmasi Password Baru</label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password-confirm" 
                            name="password_confirmation"
                            autocomplete="new-password"
                            placeholder="Konfirmasi Password Baru Anda" 
                            class="w-full h-[51px] px-10 py-3.5 bg-white border border-border rounded-10 text-base font-medium text-text-primary placeholder:text-placeholder focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition"
                            required
                        >
                        <div class="absolute left-4 top-1/2 -translate-y-1/2">
                            <svg class="w-5 h-5 text-placeholder" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit"
                    class="w-full h-[51px] bg-primary text-white rounded-5 font-semibold text-base hover:opacity-90 transition"
                >
                    Reset Kata Sandi
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
