@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold">Selamat Datang di <span class="text-primary">Laravel User App</span></h1>
            <p class="lead text-muted">Aplikasi manajemen data pengguna dan kelas berbasis Laravel.</p>
        </div>

        <div class="row g-4 justify-content-center">
            <div class="col-md-5">
                <div class="card border-0 shadow-sm h-100 text-center">
                    <div class="card-body">
                        <i class="bi bi-people-fill display-4 text-primary mb-3"></i>
                        <h5 class="card-title">Lihat Data Pengguna</h5>
                        <p class="card-text">Tampilkan daftar semua pengguna yang telah terdaftar dalam sistem.</p>
                        <a href="{{ url('/user') }}" class="btn btn-outline-primary mt-2">
                            <i class="bi bi-arrow-right-circle"></i> Lihat User
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card border-0 shadow-sm h-100 text-center">
                    <div class="card-body">
                        <i class="bi bi-person-plus-fill display-4 text-success mb-3"></i>
                        <h5 class="card-title">Tambah Pengguna Baru</h5>
                        <p class="card-text">Formulir cepat dan mudah untuk menambahkan pengguna ke dalam sistem.</p>
                        <a href="{{ url('/user/create') }}" class="btn btn-success mt-2">
                            <i class="bi bi-plus-circle"></i> Tambah User
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 justify-content-center mt-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body">
                        <i class="bi bi-person-circle display-4 text-info mb-3"></i>
                        <h5 class="card-title">Profile Mahasiswa</h5>
                        <div class="mb-3">
                            <p class="mb-1"><strong>Nama:</strong> Ananda Anhar Subing</p>
                            <p class="mb-1"><strong>NPM:</strong> 2317051082</p>
                            <p class="mb-1"><strong>Kelas:</strong> A</p>
                        </div>
                        <a href="{{ route('my.profile') }}" class="btn btn-info text-white mt-2">
                            <i class="bi bi-eye"></i> Lihat Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
