@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-warning text-dark">
                    <h4 class="mb-0 fw-bold">
                        <i class="bi bi-pencil-square me-2"></i>Edit Data User
                    </h4>
                </div>
                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Error!</strong> Terdapat kesalahan dalam input data:
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('user.update', $user->id) }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama" class="form-label fw-semibold">
                                    <i class="bi bi-person-fill me-1 text-primary"></i>Nama Lengkap
                                </label>
                                <input type="text" 
                                       class="form-control @error('nama') is-invalid @enderror" 
                                       id="nama" 
                                       name="nama" 
                                       value="{{ old('nama', $user->nama) }}" 
                                       placeholder="Masukkan nama lengkap"
                                       required>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="npm" class="form-label fw-semibold">
                                    <i class="bi bi-hash me-1 text-success"></i>NPM
                                </label>
                                <input type="text" 
                                       class="form-control @error('npm') is-invalid @enderror" 
                                       id="npm" 
                                       name="npm" 
                                       value="{{ old('npm', $user->npm) }}" 
                                       placeholder="Masukkan NPM"
                                       required>
                                @error('npm')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="kelas_id" class="form-label fw-semibold">
                                <i class="bi bi-building me-1 text-info"></i>Kelas
                            </label>
                            <select class="form-select @error('kelas_id') is-invalid @enderror" 
                                    id="kelas_id" 
                                    name="kelas_id" 
                                    required>
                                <option value="">Pilih Kelas</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}" {{ old('kelas_id', $user->kelas_id) == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kelas_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('user.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-warning btn-lg px-4">
                                <i class="bi bi-check-circle me-2"></i>Perbarui Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-warning {
        background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%);
    }
    
    .form-control:focus,
    .form-select:focus {
        border-color: #ffc107;
        box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
    }
    
    .btn-warning {
        background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%);
        border: none;
        transition: all 0.3s ease;
    }
    
    .btn-warning:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 193, 7, 0.4);
    }
    
    .card {
        border-radius: 15px;
        overflow: hidden;
    }
    
    .form-label {
        color: #495057;
    }
</style>

<script>
    // Bootstrap form validation
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
</script>
@endsection
