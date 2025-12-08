@extends('layouts.app')

@section('title', 'Syarat & Ketentuan - Reloved')

@section('content')
<div class="container mx-auto px-8 py-12">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl font-bold text-text-primary mb-8">Syarat & Ketentuan</h1>
        
        <div class="bg-white rounded-8 border border-border p-8 space-y-6">
            <div>
                <h2 class="text-2xl font-bold text-text-primary mb-4">1. Penerimaan Syarat</h2>
                <p class="text-text-secondary leading-relaxed">
                    Dengan mengakses dan menggunakan platform Reloved, Anda menyetujui untuk terikat oleh syarat dan ketentuan ini. 
                    Jika Anda tidak setuju dengan syarat dan ketentuan ini, mohon untuk tidak menggunakan platform kami.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-text-primary mb-4">2. Penggunaan Platform</h2>
                <p class="text-text-secondary leading-relaxed mb-3">
                    Platform Reloved disediakan untuk memfasilitasi jual beli barang preloved. Pengguna diharapkan untuk:
                </p>
                <ul class="list-disc list-inside space-y-2 text-text-secondary">
                    <li>Menyediakan informasi yang akurat dan terkini</li>
                    <li>Menggunakan platform dengan cara yang legal dan sesuai dengan tujuan</li>
                    <li>Menghormati hak pengguna lain</li>
                    <li>Tidak melakukan aktivitas yang merugikan platform atau pengguna lain</li>
                </ul>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-text-primary mb-4">3. Akun Pengguna</h2>
                <p class="text-text-secondary leading-relaxed">
                    Anda bertanggung jawab untuk menjaga kerahasiaan informasi akun Anda. Semua aktivitas yang terjadi 
                    di bawah akun Anda adalah tanggung jawab Anda. Jika Anda mengetahui adanya penggunaan yang tidak sah, 
                    segera laporkan kepada kami.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-text-primary mb-4">4. Transaksi</h2>
                <p class="text-text-secondary leading-relaxed">
                    Semua transaksi dilakukan antara penjual dan pembeli. Reloved hanya menyediakan platform untuk memfasilitasi 
                    transaksi. Kami tidak bertanggung jawab atas kualitas, kondisi, atau keaslian produk yang dijual oleh penjual.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-text-primary mb-4">5. Pembayaran</h2>
                <p class="text-text-secondary leading-relaxed">
                    Saat ini, kami hanya mendukung metode pembayaran COD (Cash on Delivery) baik untuk meet-up maupun pengiriman. 
                    Pembayaran dilakukan langsung antara penjual dan pembeli.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-text-primary mb-4">6. Konten Pengguna</h2>
                <p class="text-text-secondary leading-relaxed">
                    Dengan mengunggah konten ke platform, Anda memberikan izin kepada Reloved untuk menggunakan, menampilkan, 
                    dan mendistribusikan konten tersebut dalam konteks operasional platform.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-text-primary mb-4">7. Perubahan Syarat</h2>
                <p class="text-text-secondary leading-relaxed">
                    Kami berhak mengubah syarat dan ketentuan ini kapan saja. Perubahan akan diberitahukan melalui platform. 
                    Penggunaan berkelanjutan setelah perubahan berarti Anda menerima syarat dan ketentuan yang baru.
                </p>
            </div>

            <div class="pt-6 border-t border-border">
                <p class="text-sm text-text-tertiary">Terakhir diperbarui: {{ date('d M Y') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

