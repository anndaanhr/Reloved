@extends('layouts.app')

@section('title', 'Daftar Mata Kuliah')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-primary">
                    <i class="bi bi-book me-2"></i>Daftar Mata Kuliah
                </h2>
                <a href="{{ route('mata-kuliah.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Tambah Mata Kuliah
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($mataKuliah->count() > 0)
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th scope="col" class="border-0">
                                            <i class="bi bi-hash me-1"></i>No
                                        </th>
                                        <th scope="col" class="border-0">
                                            <i class="bi bi-book me-1"></i>Nama Mata Kuliah
                                        </th>
                                        <th scope="col" class="border-0">
                                            <i class="bi bi-hash me-1"></i>SKS
                                        </th>
                                        <th scope="col" class="border-0">
                                            <i class="bi bi-calendar me-1"></i>Dibuat
                                        </th>
                                        <th scope="col" class="border-0 text-center">
                                            <i class="bi bi-gear me-1"></i>Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($mataKuliah as $index => $mk)
                                        <tr>
                                            <td class="align-middle">{{ $index + 1 }}</td>
                                            <td class="align-middle">
                                                <strong>{{ $mk->nama_mk }}</strong>
                                            </td>
                                            <td class="align-middle">
                                                <span class="badge bg-primary">{{ $mk->sks }} SKS</span>
                                            </td>
                                            <td class="align-middle">
                                                <small class="text-muted">
                                                    {{ $mk->created_at->format('d M Y, H:i') }}
                                                </small>
                                            </td>
                                            <td class="align-middle text-center">
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-outline-primary btn-sm" title="Lihat Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-warning btn-sm" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger btn-sm" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <p class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Menampilkan {{ $mataKuliah->count() }} mata kuliah
                        </p>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="bi bi-book text-muted" style="font-size: 4rem;"></i>
                        </div>
                        <h4 class="text-muted mb-3">Belum Ada Mata Kuliah</h4>
                        <p class="text-muted mb-4">Mulai dengan menambahkan mata kuliah pertama Anda</p>
                        <a href="{{ route('mata-kuliah.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Mata Kuliah
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
