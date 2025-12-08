@extends('layouts.app')

@section('title', 'Hubungi Kami - Reloved')

@section('content')
<div class="container mx-auto px-8 py-12">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl font-bold text-text-primary mb-8">Hubungi Kami</h1>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white rounded-8 border border-border p-8">
                <h2 class="text-2xl font-bold text-text-primary mb-6">Informasi Kontak</h2>
                <div class="space-y-4">
                    <div>
                        <h3 class="font-semibold text-text-primary mb-1">Email</h3>
                        <a href="mailto:{{ env('CONTACT_EMAIL', env('MAIL_FROM_ADDRESS', 'info@reloved.com')) }}" class="text-primary hover:underline">
                            {{ env('CONTACT_EMAIL', env('MAIL_FROM_ADDRESS', 'info@reloved.com')) }}
                        </a>
                    </div>
                    <div>
                        <h3 class="font-semibold text-text-primary mb-1">Customer Service</h3>
                        <a href="mailto:{{ env('CONTACT_EMAIL', env('MAIL_FROM_ADDRESS', 'info@reloved.com')) }}" class="text-primary hover:underline">
                            {{ env('CONTACT_EMAIL', env('MAIL_FROM_ADDRESS', 'info@reloved.com')) }}
                        </a>
                    </div>
                    <div>
                        <h3 class="font-semibold text-text-primary mb-1">Karir</h3>
                        <a href="mailto:{{ env('CONTACT_EMAIL', env('MAIL_FROM_ADDRESS', 'info@reloved.com')) }}" class="text-primary hover:underline">
                            {{ env('CONTACT_EMAIL', env('MAIL_FROM_ADDRESS', 'info@reloved.com')) }}
                        </a>
                    </div>
                    <div>
                        <h3 class="font-semibold text-text-primary mb-1">Alamat</h3>
                        <p class="text-text-secondary">Gedong Meneng, Kec Rajabasa No 18<br>Kota Bandar Lampung, Lampung 35141</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-8 border border-border p-8">
                <h2 class="text-2xl font-bold text-text-primary mb-6">Kirim Pesan</h2>
                <form method="POST" action="{{ route('pages.contact.submit') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>
                    </div>
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Subjek</label>
                        <input type="text" id="subject" name="subject" value="{{ old('subject') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>
                    </div>
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Pesan</label>
                        <textarea id="message" name="message" rows="5" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>{{ old('message') }}</textarea>
                    </div>
                    <button type="submit" class="w-full bg-primary text-white px-6 py-3 rounded-8 font-semibold hover:bg-primary/90 transition">
                        Kirim Pesan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

