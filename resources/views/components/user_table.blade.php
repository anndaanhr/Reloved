<div class="container mt-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold text-primary mb-1">
                        <i class="bi bi-people-fill me-2"></i>Daftar Pengguna
                    </h2>
                    <p class="text-muted mb-0">Kelola data pengguna sistem</p>
                </div>
                <div>
                    <a href="{{ url('/user/create') }}" class="btn btn-primary btn-lg shadow-sm">
                        <i class="bi bi-plus-circle me-2"></i>Tambah User
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-gradient-primary text-white border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-1">Total Pengguna</h6>
                            <h3 class="mb-0 fw-bold">{{ count($user) }}</h3>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="bi bi-people fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-gradient-success text-white border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-1">Kelas Aktif</h6>
                            <h3 class="mb-0 fw-bold">{{ collect($user)->pluck('nama_kelas')->unique()->count() }}</h3>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="bi bi-building fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-gradient-info text-white border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50 mb-1">Status</h6>
                            <h3 class="mb-0 fw-bold">Aktif</h3>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="bi bi-check-circle fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari berdasarkan nama atau NPM..." id="searchInput">
            </div>
        </div>
        <div class="col-md-4">
            <select class="form-select" id="kelasFilter">
                <option value="">Semua Kelas</option>
                @foreach(collect($user)->pluck('nama_kelas')->unique() as $kelas)
                    <option value="{{ $kelas }}">Kelas {{ $kelas }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- User Cards Grid -->
    <div class="row" id="userContainer">
        @forelse ($user as $users)
            <div class="col-lg-4 col-md-6 mb-4 user-card" data-nama="{{ strtolower($users->nama) }}" data-npm="{{ $users->npm }}" data-kelas="{{ $users->nama_kelas }}">
                <div class="card h-100 shadow-sm border-0 hover-lift">
                    <div class="card-body text-center">
                        <!-- Avatar -->
                        <div class="avatar-container mb-3">
                            <div class="avatar bg-gradient-primary text-white rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="bi bi-person-fill fs-2"></i>
                            </div>
                            <div class="badge bg-success position-absolute translate-middle border border-light rounded-pill" style="top: 75%; left: 75%;">
                                <i class="bi bi-check-lg"></i>
                            </div>
                        </div>
                        
                        <!-- User Info -->
                        <h5 class="card-title fw-bold text-dark mb-1">{{ $users->nama }}</h5>
                        <p class="text-muted mb-2">
                            <i class="bi bi-hash me-1"></i>{{ $users->npm }}
                        </p>
                        
                        <!-- Class Badge -->
                        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill mb-3">
                            <i class="bi bi-building me-1"></i>Kelas {{ $users->nama_kelas }}
                        </span>
                        
                        <!-- Actions -->
                        <div class="d-flex justify-content-center gap-2 mt-3">
                            <button class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                <i class="bi bi-eye me-1"></i>Detail
                            </button>
                            <button class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                <i class="bi bi-pencil me-1"></i>Edit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-people text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="text-muted mb-2">Belum Ada Data Pengguna</h4>
                    <p class="text-muted mb-4">Mulai dengan menambahkan pengguna pertama Anda</p>
                    <a href="{{ url('/user/create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Pengguna
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Custom Styles -->
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .bg-gradient-success {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    .bg-gradient-info {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }
    .hover-lift {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
    }
    .avatar-container {
        position: relative;
        display: inline-block;
    }
    .avatar {
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .card {
        border-radius: 15px;
        overflow: hidden;
    }
    .btn {
        border-radius: 25px;
    }
    .form-control, .form-select {
        border-radius: 10px;
    }
    .input-group-text {
        border-radius: 10px 0 0 10px;
    }
    .form-control {
        border-radius: 0 10px 10px 0;
    }
</style>

<!-- Search and Filter JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const kelasFilter = document.getElementById('kelasFilter');
    const userCards = document.querySelectorAll('.user-card');

    function filterUsers() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedKelas = kelasFilter.value;

        userCards.forEach(card => {
            const nama = card.dataset.nama;
            const npm = card.dataset.npm;
            const kelas = card.dataset.kelas;

            const matchesSearch = nama.includes(searchTerm) || npm.includes(searchTerm);
            const matchesKelas = !selectedKelas || kelas === selectedKelas;

            if (matchesSearch && matchesKelas) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', filterUsers);
    kelasFilter.addEventListener('change', filterUsers);
});
</script>
