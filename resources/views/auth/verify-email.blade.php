@extends('layouts.app')

@section('title', 'Verifikasi Email - Reloved')

@section('content')
<div class="min-h-screen flex items-center justify-center px-8 py-12">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="flex flex-col items-center gap-5 mb-8">
            <div class="flex flex-col items-center gap-[-10px]">
                <img src="{{ asset('images/logo/logorelovedheader (2).png') }}" alt="Reloved Logo" class="h-12 w-auto">
            </div>
            <h1 class="text-2xl font-semibold text-black">Verifikasi Email</h1>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Error Message -->
        @if($errors->has('email'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ $errors->first('email') }}
            </div>
        @endif

        <!-- Verify Form -->
        <form method="POST" action="{{ route('verify.email') }}" class="space-y-4">
            @csrf

            @php
                $email = session('email', old('email', ''));
                // Jika email masih kosong dan user sudah login, ambil email dari user
                if (empty($email) && Auth::check()) {
                    $email = Auth::user()->email;
                }
                // Pastikan email tidak kosong
                if (empty($email)) {
                    $email = request()->query('email', '');
                }
            @endphp

            <div class="text-center mb-6">
                <p class="text-gray-700 mb-2">Kami telah mengirimkan kode OTP ke email:</p>
                <p class="font-semibold text-black">{{ $email }}</p>
            </div>

            <!-- Email (hidden) -->
            @if(!empty($email))
                <input type="hidden" name="email" value="{{ $email }}" required>
            @else
                <!-- Jika email kosong, tampilkan input visible -->
                <div class="space-y-1.5">
                    <label for="email" class="block text-lg font-medium text-black">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="Masukkan email Anda" 
                        class="w-full h-[51px] px-5 py-3.5 bg-white border border-black rounded-[10px] text-[15px] font-medium text-black placeholder:text-placeholder focus:outline-none focus:ring-2 focus:ring-primary"
                        value="{{ $email }}"
                        required
                    >
                    @error('email')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <!-- OTP -->
            <div class="space-y-1.5">
                <label for="otp" class="block text-lg font-medium text-black">Kode OTP</label>
                <div class="relative">
                    <input 
                        type="text" 
                        id="otp" 
                        name="otp" 
                        placeholder="Masukkan 6 digit kode OTP" 
                        class="w-full h-[51px] px-5 py-3.5 bg-white border border-black rounded-[10px] text-[15px] font-medium text-black placeholder:text-placeholder focus:outline-none focus:ring-2 focus:ring-primary text-center tracking-widest"
                        maxlength="6"
                        required
                        autofocus
                    >
                </div>
                @error('otp')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Verify Button -->
            <button 
                type="submit" 
                class="w-full h-[51px] bg-primary text-white rounded-[5px] font-semibold text-[15px] hover:bg-primary/90 transition"
            >
                Verifikasi
            </button>

        </form>

        <!-- Resend OTP -->
        <form method="POST" action="{{ route('resend.otp') }}" class="mt-4">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}" required>
            <button 
                type="submit" 
                class="w-full text-center text-[15px] font-medium text-primary hover:underline"
            >
                Kirim ulang kode OTP
            </button>
        </form>
    </div>
</div>
@endsection

