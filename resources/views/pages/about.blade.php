@extends('layouts.app')

@section('title', 'Tentang Kami - Reloved')

@section('content')
<div class="container mx-auto px-8 py-12">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl font-bold text-text-primary mb-8">Tentang Reloved</h1>
        
        <div class="bg-white rounded-8 border border-border p-8 space-y-6">
            <div>
                <h2 class="text-2xl font-bold text-text-primary mb-4">Visi Kami</h2>
                <p class="text-text-secondary leading-relaxed">
                    Menjadi marketplace preloved terdepan di Indonesia yang menghubungkan penjual dan pembeli dengan cara yang mudah, aman, dan ramah lingkungan.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-text-primary mb-4">Misi Kami</h2>
                <ul class="list-disc list-inside space-y-2 text-text-secondary">
                    <li>Menyediakan platform yang mudah digunakan untuk jual beli barang preloved</li>
                    <li>Meningkatkan kesadaran masyarakat tentang pentingnya mengurangi limbah melalui penggunaan kembali barang</li>
                    <li>Membantu masyarakat mendapatkan barang berkualitas dengan harga terjangkau</li>
                    <li>Menyediakan sistem transaksi yang aman dan terpercaya</li>
                </ul>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-text-primary mb-4">Mengapa Reloved?</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 rounded-8 p-6">
                        <h3 class="font-bold text-lg text-text-primary mb-2">Ramah Lingkungan</h3>
                        <p class="text-text-secondary text-sm">Mengurangi limbah dengan memberikan kehidupan kedua pada barang yang masih layak pakai.</p>
                    </div>
                    <div class="bg-gray-50 rounded-8 p-6">
                        <h3 class="font-bold text-lg text-text-primary mb-2">Harga Terjangkau</h3>
                        <p class="text-text-secondary text-sm">Dapatkan barang berkualitas dengan harga yang lebih terjangkau dibandingkan barang baru.</p>
                    </div>
                    <div class="bg-gray-50 rounded-8 p-6">
                        <h3 class="font-bold text-lg text-text-primary mb-2">Aman & Terpercaya</h3>
                        <p class="text-text-secondary text-sm">Sistem transaksi yang aman dengan fitur chat dan negosiasi langsung dengan penjual.</p>
                    </div>
                    <div class="bg-gray-50 rounded-8 p-6">
                        <h3 class="font-bold text-lg text-text-primary mb-2">Mudah Digunakan</h3>
                        <p class="text-text-secondary text-sm">Platform yang user-friendly dengan fitur pencarian dan filter yang lengkap.</p>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-text-primary mb-4">Tim Kami</h2>
                <p class="text-text-secondary leading-relaxed">
                    Reloved dikembangkan oleh tim yang berkomitmen untuk menciptakan solusi berkelanjutan dalam dunia e-commerce. 
                    Kami percaya bahwa setiap barang memiliki nilai dan dapat memberikan manfaat bagi orang lain.
                </p>
            </div>

            <div class="pt-6 border-t border-border">
                <a href="{{ route('pages.contact') }}" class="inline-block bg-primary text-white px-6 py-3 rounded-8 font-semibold hover:bg-primary/90 transition">
                    Hubungi Kami
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

