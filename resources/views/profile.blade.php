<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Profile Card</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        /* Reset dasar & Pengaturan Body */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
            /* Warna latar belakang yang lembut */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            color: #333;
        }

        /* Container utama untuk kartu profil */
        .profile-card {
            background-color: #ffffff;
            border-radius: 15px;
            /* Sudut yang lebih tumpul */
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            /* Efek bayangan yang lebih halus */
            text-align: center;
            padding: 40px 30px;
            width: 90%;
            max-width: 350px;
            /* Lebar maksimum agar tidak terlalu lebar di layar besar */
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        /* Efek hover untuk kartu */
        .profile-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        /* Foto Profil */
        .profile-picture {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            margin: 0 auto 20px auto;
            border: 4px solid #007bff;
            /* Memberi border dengan warna aksen */
            background-size: cover;
            background-position: center;

            /* Placeholder jika tidak ada gambar */
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
            color: #adb5bd;
        }

        /* Nama Pengguna */
        .profile-name {
            font-size: 1.8em;
            font-weight: 600;
            margin: 0;
            color: #212529;
        }

        /* Container untuk info detail */
        .info-section {
            margin-top: 30px;
            text-align: left;
            /* Teks info rata kiri agar lebih rapi */
        }

        .info-item {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 12px 18px;
            margin-bottom: 12px;
            border-left: 4px solid #007bff;
            /* Aksen garis di kiri */
        }

        .info-item strong {
            color: #495057;
            display: block;
            /* Membuat label (Nama, Kelas) jadi satu baris sendiri */
            font-size: 0.8em;
            margin-bottom: 2px;
        }
    </style>
</head>

<body>
    <div class="profile-card">
        <div class="profile-picture"
            @if ($photoUrl) style="background-image: url('{{ $photoUrl }}');" @endif>
            @if (!$photoUrl)
                &#128100;
            @endif
        </div>

        <div class="info-section">
            <div class="info-item">
                <strong>Nama:</strong>
                <span>{{ $Nama }}</span>
            </div>
            <div class="info-item">
                <strong>Kelas:</strong>
                <span>{{ $Kelas }}</span>
            </div>
            <div class="info-item">
                <strong>NPM:</strong>
                <span>{{ $NPM }}</span>
            </div>
        </div>

        @if (isset($errors) && $errors->any())
            <div style="color: red; margin-bottom: 10px;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('profile.upload') }}" method="POST" enctype="multipart/form-data"
            style="margin-top: 20px;">
            @csrf
            <input type="hidden" name="Nama" value="{{ $Nama }}">
            <input type="hidden" name="NPM" value="{{ $NPM }}">
            <input type="hidden" name="Kelas" value="{{ $Kelas }}">
            <input type="file" name="photo" accept="image/*" required>
            <button type="submit"
                style="margin-top: 10px; padding: 8px 16px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
                Upload Foto
            </button>
        </form>
    </div>
</body>

</html>
