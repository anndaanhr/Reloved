<div class="container mt-4">
    <!-- Success/Error Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-3 fs-4"></i>
                <div>
                    <h6 class="alert-heading mb-1">Berhasil!</h6>
                    <p class="mb-0">{{ session('success') }}</p>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
                <div>
                    <h6 class="alert-heading mb-1">Error!</h6>
                    <p class="mb-0">{{ session('error') }}</p>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

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
                            <a href="{{ route('user.edit', $users->id) }}" class="btn btn-warning btn-sm rounded-pill px-3 action-btn edit-btn">
                                <i class="bi bi-pencil-square me-1"></i>Edit
                            </a>
                            <form action="{{ route('user.destroy', $users->id) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3 action-btn delete-btn" 
                                        onclick="return confirmDelete('{{ $users->nama }}')">
                                    <i class="bi bi-trash3 me-1"></i>Hapus
                                </button>
                            </form>
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
    
    /* Creative Action Button Styles */
    .action-btn {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .edit-btn {
        background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%);
        border: none;
        color: white;
        font-weight: 600;
    }
    
    .edit-btn:hover {
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 8px 25px rgba(255, 193, 7, 0.4);
        color: white;
    }
    
    .edit-btn:before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }
    
    .edit-btn:hover:before {
        left: 100%;
    }
    
    .delete-btn {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        border: none;
        color: white;
        font-weight: 600;
    }
    
    .delete-btn:hover {
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4);
        color: white;
    }
    
    .delete-btn:before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }
    
    .delete-btn:hover:before {
        left: 100%;
    }
    
    /* Card hover effects */
    .user-card:hover .action-btn {
        transform: translateY(-2px);
    }
    
    /* Success/Error Alert Animations */
    .alert {
        border: none;
        border-radius: 12px;
        animation: slideInDown 0.5s ease-out;
    }
    
    @keyframes slideInDown {
        from {
            transform: translateY(-100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    /* Pulse animation for action buttons */
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    .action-btn:active {
        animation: pulse 0.3s ease-in-out;
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

// Delete confirmation function
function confirmDelete(nama) {
    return Swal.fire({
        title: 'Konfirmasi Hapus',
        html: `Apakah Anda yakin ingin menghapus data <strong>"${nama}"</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="bi bi-trash3 me-1"></i>Ya, Hapus!',
        cancelButtonText: '<i class="bi bi-x-circle me-1"></i>Batal',
        reverseButtons: true,
        customClass: {
            popup: 'swal2-popup-custom',
            confirmButton: 'swal2-confirm-custom',
            cancelButton: 'swal2-cancel-custom'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: 'Menghapus...',
                text: 'Sedang menghapus data, mohon tunggu.',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            return true;
        }
        return false;
    });
}

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert.classList.contains('show')) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    });
});

// Add SweetAlert2 CDN if not already loaded
if (typeof Swal === 'undefined') {
    const script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
    script.onload = function() {
        console.log('SweetAlert2 loaded successfully');
    };
    document.head.appendChild(script);
}
</script>

<!-- SweetAlert2 Styles -->
<style>
.swal2-popup-custom {
    border-radius: 15px !important;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2) !important;
}

.swal2-confirm-custom {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
    border: none !important;
    border-radius: 8px !important;
    font-weight: 600 !important;
    transition: all 0.3s ease !important;
}

.swal2-confirm-custom:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4) !important;
}

.swal2-cancel-custom {
    background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%) !important;
    border: none !important;
    border-radius: 8px !important;
    font-weight: 600 !important;
    transition: all 0.3s ease !important;
}

.swal2-cancel-custom:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 25px rgba(108, 117, 125, 0.4) !important;
}
</style>
