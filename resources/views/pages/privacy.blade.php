@extends('layouts.app')

@section('title', 'Kebijakan Privasi - Reloved')

@section('content')
<div class="container mx-auto px-8 py-12">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl font-bold text-text-primary mb-8">Kebijakan Privasi</h1>
        
        <div class="bg-white rounded-8 border border-border p-8 space-y-6">
            <div>
                <h2 class="text-2xl font-bold text-text-primary mb-4">1. Informasi yang Kami Kumpulkan</h2>
                <p class="text-text-secondary leading-relaxed mb-3">
                    Kami mengumpulkan informasi yang Anda berikan secara langsung, termasuk:
                </p>
                <ul class="list-disc list-inside space-y-2 text-text-secondary">
                    <li>Informasi akun (nama, email, nomor telepon)</li>
                    <li>Informasi profil (alamat, foto profil)</li>
                    <li>Informasi transaksi</li>
                    <li>Konten yang Anda unggah (foto produk, deskripsi)</li>
                </ul>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-text-primary mb-4">2. Cara Kami Menggunakan Informasi</h2>
                <p class="text-text-secondary leading-relaxed mb-3">
                    Kami menggunakan informasi yang dikumpulkan untuk:
                </p>
                <ul class="list-disc list-inside space-y-2 text-text-secondary">
                    <li>Menyediakan dan meningkatkan layanan platform</li>
                    <li>Memproses transaksi dan komunikasi antar pengguna</li>
                    <li>Mengirim notifikasi dan update penting</li>
                    <li>Meningkatkan keamanan platform</li>
                </ul>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-text-primary mb-4">3. Perlindungan Data</h2>
                <p class="text-text-secondary leading-relaxed">
                    Kami menerapkan langkah-langkah keamanan yang wajar untuk melindungi informasi pribadi Anda dari 
                    akses, perubahan, pengungkapan, atau penghancuran yang tidak sah. Namun, tidak ada metode transmisi 
                    melalui internet yang 100% aman.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-text-primary mb-4">4. Berbagi Informasi</h2>
                <p class="text-text-secondary leading-relaxed">
                    Kami tidak menjual, menyewakan, atau memperdagangkan informasi pribadi Anda kepada pihak ketiga. 
                    Informasi hanya dibagikan dalam konteks operasional platform (misalnya, untuk memfasilitasi transaksi 
                    antara penjual dan pembeli).
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-text-primary mb-4">5. Hak Anda</h2>
                <p class="text-text-secondary leading-relaxed mb-3">
                    Anda memiliki hak untuk:
                </p>
                <ul class="list-disc list-inside space-y-2 text-text-secondary">
                    <li>Mengakses dan memperbarui informasi pribadi Anda</li>
                    <li>Menghapus akun dan data Anda</li>
                    <li>Menolak pengumpulan informasi tertentu</li>
                    <li>Mengajukan keluhan terkait privasi</li>
                </ul>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-text-primary mb-4">6. Cookie</h2>
                <p class="text-text-secondary leading-relaxed">
                    Kami menggunakan cookie untuk meningkatkan pengalaman pengguna. Anda dapat mengatur browser untuk menolak 
                    cookie, namun hal ini dapat mempengaruhi fungsionalitas platform.
                </p>
            </div>

            <div class="pt-6 border-t border-border">
                <p class="text-sm text-text-tertiary">Terakhir diperbarui: {{ date('d M Y') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

