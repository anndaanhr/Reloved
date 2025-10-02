<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm fixed-top">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            <i class="bi bi-layers-fill me-2"></i>Ananda Anhar Subing
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                        <i class="bi bi-house-fill me-1"></i> Home
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->is('user*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-people-fill me-1"></i> User
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ url('/user') }}">
                            <i class="bi bi-grid-3x3-gap me-2"></i>Card View
                        </a></li>
                        <li><a class="dropdown-item" href="{{ url('/user/table') }}">
                            <i class="bi bi-table me-2"></i>Table View
                        </a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('user/create') ? 'active' : '' }}"
                        href="{{ url('/user/create') }}">
                        <i class="bi bi-person-plus-fill me-1"></i> Tambah User
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('profile*') ? 'active' : '' }}"
                        href="{{ route('my.profile') }}">
                        <i class="bi bi-person-circle me-1"></i> Profile
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
