@extends('layouts.app')

@section('title', 'Link Reset Terkirim - Reloved')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-background">
    <div class="w-full max-w-md mx-auto text-center p-8">
        <!-- Logo -->
        <div class="flex flex-col items-center gap-5 mb-8">
            <div class="flex flex-col items-center">
                <img src="{{ asset('images/logo/logorelovedheader (2).png') }}" alt="Reloved Logo" class="h-12 w-auto">
            </div>
            <h1 class="text-xl font-semibold text-text-primary">Periksa Email Anda</h1>
        </div>

        <div class="bg-white p-8 rounded-lg shadow-md border border-border">
            <div class="mb-4">
                <svg class="w-16 h-16 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M12 12a4 4 0 100-8 4 4 0 000 8z"></path></svg>
            </div>
            <p class="text-text-primary text-lg mb-6">
                Kami telah mengirimkan email berisi tautan untuk mereset kata sandi Anda. Silakan periksa kotak masuk Anda.
            </p>
            <a href="{{ route('login') }}" class="text-primary hover:opacity-80 transition font-semibold">
                Kembali ke Halaman Login
            </a>
        </div>
    </div>
</div>
@endsection
