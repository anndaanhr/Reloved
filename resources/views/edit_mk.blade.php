@extends('layouts.app')

@section('title', 'Edit Mata Kuliah')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil-square me-2"></i>Edit Mata Kuliah
                    </h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('mata-kuliah.update', $mataKuliah->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="nama_mk" class="form-label">
                                <i class="bi bi-book me-1"></i>Nama Mata Kuliah
                            </label>
                            <input type="text" 
                                   class="form-control @error('nama_mk') is-invalid @enderror" 
                                   id="nama_mk" 
                                   name="nama_mk" 
                                   value="{{ old('nama_mk', $mataKuliah->nama_mk) }}" 
                                   placeholder="Masukkan nama mata kuliah"
                                   required>
                            @error('nama_mk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="sks" class="form-label">
                                <i class="bi bi-hash me-1"></i>Jumlah SKS
                            </label>
                            <select class="form-select @error('sks') is-invalid @enderror" 
                                    id="sks" 
                                    name="sks" 
                                    required>
                                <option value="">Pilih jumlah SKS</option>
                                @for($i = 1; $i <= 6; $i++)
                                    <option value="{{ $i }}" {{ old('sks', $mataKuliah->sks) == $i ? 'selected' : '' }}>
                                        {{ $i }} SKS
                                    </option>
                                @endfor
                            </select>
                            @error('sks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('mata-kuliah.index') }}" class="btn btn-secondary me-md-2">
                                <i class="bi bi-arrow-left me-1"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-check-lg me-1"></i>Perbarui
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
