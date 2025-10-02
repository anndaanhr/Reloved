<footer class="bg-dark text-light py-5 mt-5">
    <div class="container">
        <div class="row">
            <!-- About Section -->
            <div class="col-lg-4 col-md-6 mb-4">
                <h5 class="text-primary mb-3">
                    <i class="bi bi-layers-fill me-2"></i>Laravel User App
                </h5>
                <p class="text-light-emphasis mb-3">
                    Aplikasi manajemen data pengguna dan kelas berbasis Laravel. 
                    Dikembangkan untuk memudahkan pengelolaan data mahasiswa.
                </p>
                <div class="d-flex gap-3">
                    <a href="#" class="text-light-emphasis hover-primary">
                        <i class="bi bi-github fs-5"></i>
                    </a>
                    <a href="#" class="text-light-emphasis hover-primary">
                        <i class="bi bi-linkedin fs-5"></i>
                    </a>
                    <a href="#" class="text-light-emphasis hover-primary">
                        <i class="bi bi-envelope fs-5"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6 mb-4">
                <h6 class="text-primary mb-3">Menu Utama</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="{{ url('/') }}" class="text-light-emphasis text-decoration-none hover-primary">
                            <i class="bi bi-house me-2"></i>Beranda
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ url('/user') }}" class="text-light-emphasis text-decoration-none hover-primary">
                            <i class="bi bi-people me-2"></i>Daftar User
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ url('/user/create') }}" class="text-light-emphasis text-decoration-none hover-primary">
                            <i class="bi bi-person-plus me-2"></i>Tambah User
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('my.profile') }}" class="text-light-emphasis text-decoration-none hover-primary">
                            <i class="bi bi-person-circle me-2"></i>Profile
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Features -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h6 class="text-primary mb-3">Fitur Aplikasi</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <span class="text-light-emphasis">Manajemen User</span>
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <span class="text-light-emphasis">Manajemen Kelas</span>
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <span class="text-light-emphasis">Upload Profile</span>
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <span class="text-light-emphasis">Search & Filter</span>
                    </li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h6 class="text-primary mb-3">Informasi Developer</h6>
                <div class="mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-person-badge text-primary me-2"></i>
                        <span class="text-light-emphasis">Ananda Anhar Subing</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-hash text-primary me-2"></i>
                        <span class="text-light-emphasis">2317051082</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-building text-primary me-2"></i>
                        <span class="text-light-emphasis">Kelas A</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-calendar text-primary me-2"></i>
                        <span class="text-light-emphasis">{{ date('Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-4 border-secondary">

        <!-- Copyright -->
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="text-light-emphasis mb-0">
                    <i class="bi bi-c-circle me-1"></i>
                    {{ date('Y') }} Laravel User App. Praktikum Web Lanjut.
                </p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="text-light-emphasis mb-0">
                    Dibuat dengan <i class="bi bi-heart-fill text-danger"></i> menggunakan Laravel & Bootstrap
                </p>
            </div>
        </div>
    </div>
</footer>

<style>
.hover-primary:hover {
    color: var(--bs-primary) !important;
    transition: color 0.3s ease;
}

footer {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
}

footer .text-light-emphasis {
    color: rgba(255, 255, 255, 0.8) !important;
}

footer .text-primary {
    color: #4facfe !important;
}

footer hr {
    opacity: 0.3;
}
</style>
