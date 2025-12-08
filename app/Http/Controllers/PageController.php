<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\ContactMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class PageController extends Controller
{
    public function about()
    {
        return view('pages.about');
    }

    public function career()
    {
        return view('pages.career');
    }

    public function blog()
    {
        // Simulasi data blog (bisa diganti dengan model Blog nanti)
        $posts = [
            [
                'id' => 1,
                'title' => 'Tips Membeli Barang Preloved yang Berkualitas',
                'excerpt' => 'Panduan lengkap untuk memilih barang preloved yang masih bagus dan layak pakai.',
                'date' => '2024-12-01',
                'author' => 'Tim Reloved',
                'image' => asset('images/blog1.png'),
            ],
            [
                'id' => 2,
                'title' => 'Cara Merawat Barang Preloved agar Awet',
                'excerpt' => 'Tips dan trik merawat barang preloved agar tetap terlihat seperti baru.',
                'date' => '2024-11-25',
                'author' => 'Tim Reloved',
                'image' => asset('images/blog2.png'),
            ],
            [
                'id' => 3,
                'title' => 'Keuntungan Berbelanja di Marketplace Preloved',
                'excerpt' => 'Mengapa berbelanja barang preloved lebih menguntungkan dan ramah lingkungan.',
                'date' => '2024-11-15',
                'author' => 'Tim Reloved',
                'image' => asset('images/blog3.png'),
            ],
        ];

        return view('pages.blog', compact('posts'));
    }

    public function blogDetail($id)
    {
        // Simulasi data blog detail (bisa diganti dengan model Blog nanti)
        $posts = [
            1 => [
                'id' => 1,
                'title' => 'Tips Membeli Barang Preloved yang Berkualitas',
                'content' => '<p>Berbelanja barang preloved adalah pilihan yang cerdas dan ramah lingkungan. Namun, untuk mendapatkan barang yang berkualitas, ada beberapa hal yang perlu diperhatikan.</p>
                <h2>1. Periksa Kondisi Barang</h2>
                <p>Selalu periksa foto produk dengan teliti. Perhatikan detail seperti warna, ukuran, dan kondisi fisik barang. Jangan ragu untuk bertanya kepada penjual jika ada yang kurang jelas.</p>
                <h2>2. Cek Reputasi Penjual</h2>
                <p>Lihat rating dan review dari pembeli sebelumnya. Penjual dengan rating tinggi dan banyak review positif biasanya lebih terpercaya.</p>
                <h2>3. Bandingkan Harga</h2>
                <p>Bandingkan harga dengan produk serupa di marketplace lain. Harga yang terlalu murah bisa jadi tanda peringatan.</p>
                <h2>4. Baca Deskripsi dengan Teliti</h2>
                <p>Deskripsi produk biasanya mencakup informasi penting tentang kondisi, ukuran, dan detail lainnya. Pastikan Anda membaca dengan seksama sebelum membeli.</p>
                <h2>5. Gunakan Fitur Chat</h2>
                <p>Jangan ragu untuk bertanya kepada penjual melalui fitur chat. Tanyakan hal-hal yang belum jelas dari foto atau deskripsi produk.</p>',
                'date' => '2024-12-01',
                'author' => 'Tim Reloved',
                'image' => asset('images/blog1.png'),
            ],
            2 => [
                'id' => 2,
                'title' => 'Cara Merawat Barang Preloved agar Awet',
                'content' => '<p>Merawat barang preloved dengan benar akan membuatnya tetap awet dan terlihat seperti baru. Berikut tips untuk merawat berbagai jenis barang preloved.</p>
                <h2>1. Pakaian</h2>
                <p>Cuci dengan air dingin dan gunakan detergen yang lembut. Hindari pemutih dan pengering dengan suhu tinggi. Simpan di tempat yang kering dan sejuk.</p>
                <h2>2. Elektronik</h2>
                <p>Bersihkan secara berkala dengan kain lembut dan hindari terkena air. Pastikan sirkulasi udara yang baik saat digunakan untuk mencegah overheating.</p>
                <h2>3. Buku</h2>
                <p>Simpan di tempat yang kering dan hindari sinar matahari langsung. Gunakan bookmark, bukan melipat halaman. Bersihkan debu secara berkala.</p>
                <h2>4. Sepatu</h2>
                <p>Bersihkan setelah digunakan dan simpan dengan shoe tree untuk menjaga bentuk. Beri waktu istirahat antar penggunaan untuk mengeringkan kelembaban.</p>
                <h2>5. Peralatan Rumah Tangga</h2>
                <p>Bersihkan setelah digunakan dan simpan di tempat yang kering. Periksa secara berkala untuk memastikan semua bagian masih berfungsi dengan baik.</p>',
                'date' => '2024-11-25',
                'author' => 'Tim Reloved',
                'image' => asset('images/blog2.png'),
            ],
            3 => [
                'id' => 3,
                'title' => 'Keuntungan Berbelanja di Marketplace Preloved',
                'content' => '<p>Berbelanja di marketplace preloved memiliki banyak keuntungan, baik dari segi ekonomi maupun lingkungan. Berikut beberapa alasan mengapa Anda harus mencoba.</p>
                <h2>1. Lebih Hemat</h2>
                <p>Barang preloved biasanya dijual dengan harga yang jauh lebih murah dibandingkan barang baru. Anda bisa mendapatkan barang berkualitas dengan harga terjangkau.</p>
                <h2>2. Ramah Lingkungan</h2>
                <p>Dengan membeli barang bekas, Anda membantu mengurangi limbah dan penggunaan sumber daya. Ini adalah bentuk dukungan terhadap lingkungan.</p>
                <h2>3. Kualitas Teruji</h2>
                <p>Barang preloved yang masih dijual biasanya dalam kondisi baik karena sudah teruji kualitasnya. Penjual juga cenderung jujur tentang kondisi barang.</p>
                <h2>4. Unik dan Vintage</h2>
                <p>Banyak barang preloved yang tidak lagi diproduksi, sehingga memiliki nilai unik. Anda bisa menemukan barang vintage yang tidak tersedia di toko biasa.</p>
                <h2>5. Mendukung Ekonomi Sirkuler</h2>
                <p>Dengan membeli preloved, Anda mendukung ekonomi sirkuler yang berkelanjutan. Barang digunakan kembali daripada dibuang ke tempat pembuangan akhir.</p>',
                'date' => '2024-11-15',
                'author' => 'Tim Reloved',
                'image' => asset('images/blog3.png'),
            ],
        ];

        $post = $posts[$id] ?? $posts[1];

        return view('pages.blog-detail', compact('post'));
    }

    public function help()
    {
        $faqs = [
            [
                'question' => 'Bagaimana cara membeli produk di Reloved?',
                'answer' => 'Anda bisa mencari produk yang diinginkan melalui halaman beranda atau menggunakan fitur pencarian. Setelah menemukan produk, klik "Chat Penjual" untuk berkomunikasi dengan penjual dan melakukan transaksi.',
            ],
            [
                'question' => 'Bagaimana cara menjual produk di Reloved?',
                'answer' => 'Untuk menjual produk, Anda perlu membuat akun terlebih dahulu. Setelah login, klik tombol "Jual Barang" di header, lalu isi form dengan detail produk yang ingin dijual.',
            ],
            [
                'question' => 'Apakah transaksi di Reloved aman?',
                'answer' => 'Ya, kami menyediakan sistem chat dan negosiasi yang aman. Semua transaksi dilakukan melalui platform kami dengan sistem COD (Cash on Delivery) untuk memastikan keamanan pembeli dan penjual.',
            ],
            [
                'question' => 'Bagaimana jika produk yang diterima tidak sesuai?',
                'answer' => 'Jika produk yang diterima tidak sesuai dengan deskripsi, Anda bisa menghubungi penjual melalui fitur chat atau membatalkan transaksi sebelum konfirmasi penerimaan.',
            ],
            [
                'question' => 'Apakah ada biaya untuk menggunakan Reloved?',
                'answer' => 'Tidak ada biaya pendaftaran atau biaya bulanan. Kami hanya mengambil komisi kecil dari setiap transaksi yang berhasil.',
            ],
        ];

        return view('pages.help', compact('faqs'));
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function submitContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $contactEmail = env('CONTACT_EMAIL', env('MAIL_FROM_ADDRESS', 'info@reloved.com'));
            
            // Remove quotes from email if present
            $contactEmail = trim($contactEmail, '"\'');
            
            \Log::info('Attempting to send contact email', [
                'to' => $contactEmail,
                'from' => $request->email,
                'subject' => $request->subject,
            ]);
            
            Mail::to($contactEmail)->send(
                new ContactMail(
                    $request->name,
                    $request->email,
                    $request->subject,
                    $request->message
                )
            );

            \Log::info('Contact email sent successfully', ['to' => $contactEmail]);
            
            return back()->with('success', 'Pesan Anda berhasil dikirim! Kami akan membalas secepatnya.');
        } catch (\Exception $e) {
            \Log::error('Failed to send contact email', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'to' => $contactEmail ?? 'unknown',
            ]);
            
            return back()
                ->withErrors(['error' => 'Gagal mengirim pesan: ' . $e->getMessage() . '. Silakan coba lagi atau hubungi kami langsung melalui email.'])
                ->withInput();
        }
    }

    public function faq()
    {
        $faqs = [
            [
                'category' => 'Umum',
                'questions' => [
                    [
                        'question' => 'Apa itu Reloved?',
                        'answer' => 'Reloved adalah marketplace khusus untuk barang preloved (bekas pakai) yang masih layak pakai. Kami memudahkan penjual dan pembeli untuk bertemu dan melakukan transaksi dengan aman.',
                    ],
                    [
                        'question' => 'Bagaimana cara mendaftar?',
                        'answer' => 'Anda bisa mendaftar dengan klik tombol "Daftar" di halaman beranda, lalu isi data diri Anda. Setelah itu, verifikasi email Anda dengan kode OTP yang dikirim.',
                    ],
                ],
            ],
            [
                'category' => 'Pembelian',
                'questions' => [
                    [
                        'question' => 'Bagaimana cara membeli produk?',
                        'answer' => 'Cari produk yang diinginkan, lalu klik "Chat Penjual" untuk berkomunikasi dengan penjual. Setelah deal, penjual akan membuat transaksi dan Anda bisa melakukan pembayaran.',
                    ],
                    [
                        'question' => 'Metode pembayaran apa saja yang tersedia?',
                        'answer' => 'Saat ini kami hanya mendukung COD (Cash on Delivery) baik untuk meet-up maupun pengiriman.',
                    ],
                ],
            ],
            [
                'category' => 'Penjualan',
                'questions' => [
                    [
                        'question' => 'Bagaimana cara menjual produk?',
                        'answer' => 'Login ke akun Anda, lalu klik "Jual Barang" di header. Isi form dengan detail produk, upload foto, dan publikasikan. Produk Anda akan muncul di halaman beranda.',
                    ],
                    [
                        'question' => 'Berapa komisi yang dikenakan?',
                        'answer' => 'Kami mengambil komisi kecil dari setiap transaksi yang berhasil. Detail komisi akan ditampilkan saat Anda membuat listing produk.',
                    ],
                ],
            ],
        ];

        return view('pages.faq', compact('faqs'));
    }

    public function terms()
    {
        return view('pages.terms');
    }

    public function privacy()
    {
        return view('pages.privacy');
    }

    public function returnPolicy()
    {
        return view('pages.return-policy');
    }
}

