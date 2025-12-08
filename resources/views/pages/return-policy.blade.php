@extends('layouts.app')

@section('title', 'Kebijakan Pengembalian - Reloved')

@section('content')
<div class="container mx-auto px-8 py-12">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl font-bold text-text-primary mb-8">Kebijakan Pengembalian</h1>
        
        <div class="bg-white rounded-8 border border-border p-8 space-y-6">
            <div>
                <h2 class="text-2xl font-bold text-text-primary mb-4">1. Ketentuan Umum</h2>
                <p class="text-text-secondary leading-relaxed">
                    Karena Reloved adalah marketplace untuk barang preloved, kebijakan pengembalian mengikuti kesepakatan 
                    antara penjual dan pembeli. Kami mendorong komunikasi yang jelas sebelum transaksi untuk menghindari 
                    masalah pengembalian.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-text-primary mb-4">2. Alasan Pengembalian yang Diterima</h2>
                <p class="text-text-secondary leading-relaxed mb-3">
                    Pengembalian dapat dilakukan jika:
                </p>
                <ul class="list-disc list-inside space-y-2 text-text-secondary">
                    <li>Produk yang diterima tidak sesuai dengan deskripsi yang diberikan penjual</li>
                    <li>Produk memiliki kerusakan yang tidak disebutkan dalam deskripsi</li>
                    <li>Produk yang diterima berbeda dengan yang dipesan</li>
                    <li>Kesepakatan pengembalian antara penjual dan pembeli</li>
                </ul>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-text-primary mb-4">3. Prosedur Pengembalian</h2>
                <ol class="list-decimal list-inside space-y-2 text-text-secondary">
                    <li>Hubungi penjual melalui fitur chat di platform</li>
                    <li>Jelaskan alasan pengembalian dengan jelas</li>
                    <li>Lampirkan foto produk jika diperlukan</li>
                    <li>Negosiasikan solusi dengan penjual (pengembalian dana atau penggantian produk)</li>
                    <li>Jika disetujui, kirim kembali produk ke alamat penjual</li>
                </ol>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-text-primary mb-4">4. Batas Waktu</h2>
                <p class="text-text-secondary leading-relaxed">
                    Pengembalian harus diajukan dalam waktu maksimal 3 hari setelah produk diterima. Setelah batas waktu 
                    tersebut, pengembalian akan mengikuti kebijakan penjual.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-text-primary mb-4">5. Biaya Pengembalian</h2>
                <p class="text-text-secondary leading-relaxed">
                    Biaya pengiriman kembali produk ditanggung oleh pembeli, kecuali jika produk yang diterima tidak sesuai 
                    dengan deskripsi atau ada kesalahan dari penjual.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-text-primary mb-4">6. Kondisi Produk yang Dikembalikan</h2>
                <p class="text-text-secondary leading-relaxed">
                    Produk yang dikembalikan harus dalam kondisi yang sama seperti saat diterima. Produk yang sudah digunakan 
                    atau rusak karena kesalahan pembeli tidak dapat dikembalikan.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-text-primary mb-4">7. Penyelesaian Sengketa</h2>
                <p class="text-text-secondary leading-relaxed">
                    Jika terjadi sengketa yang tidak dapat diselesaikan antara penjual dan pembeli, tim Reloved akan membantu 
                    mediasi. Keputusan akhir akan dibuat berdasarkan bukti dan komunikasi yang ada di platform.
                </p>
            </div>

            <div class="pt-6 border-t border-border">
                <p class="text-sm text-text-tertiary">Terakhir diperbarui: {{ date('d M Y') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

