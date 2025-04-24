<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />

<div id="sidebar" class="sidebar">
    <div class="sidebar-content">
        <img id="side-logo" src="{{ asset('assets/media/img/logo-helpdesk-black.png') }}" class="img-fluid">        

        <ul class="nav flex-column mt-3">
            @if (auth()->user()->role_id == 2)
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('dashboard.operator') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('tickets.index') }}"><i class="fa-solid fa-list"></i> Detail Aduan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('services.index') }}"><i class="fa-solid fa-list-check"></i> Kelola Layanan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('tickets.created') }}"><i class="fa-solid fa-clock-rotate-left"></i> Riwayat Aduan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('tickets.create') }}"><i class="fa-solid fa-marker"></i> Buat Aduan Baru</a>
            </li>
            @elseif (auth()->user()->role_id == 3)
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('dashboard.pegawai') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('tickets.index') }}"><i class="fa-solid fa-list"></i> Detail Aduan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('tickets.assigned') }}"><i class="fa-solid fa-briefcase"></i> Aduan Ditugaskan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('tickets.create') }}"><i class="fa-solid fa-marker"></i> Buat Aduan Baru</a>
            </li>
            @elseif (auth()->user()->role_id == 1)
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('dashboard.admin') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('tickets.index') }}"><i class="fa-solid fa-list"></i> Detail Aduan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('users.index') }}"><i class="fa-solid fa-users"></i> Kelola Pengguna</a>
            </li>
            @else
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('dashboard.warga') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('tickets.index') }}"><i class="fa-solid fa-list"></i> Detail Aduan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('tickets.create') }}"><i class="fa-solid fa-marker"></i> Buat Aduan Baru</a>
            </li>
            @endif
        </ul>

    </div>
</div>

<nav id="header" class="navbar fixed-top p-4 z-1 bg-dark">
    <div class="container d-flex justify-content-between align-items-center">
        <button class="navbar-toggler d-lg-none" type="button" id="sidebarToggle">
            <i class="fas fa-bars text-white"></i>
        </button>

        <div class="dropdown ms-auto">
            <div class="user-profile d-flex px-3 align-items-center" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false" role="button">
                <div class="profile-initial rounded-circle text-white d-flex align-items-center justify-content-center">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="ms-3 d-none d-sm-block">
                    <span class="text-white">{{ auth()->user()->name }}</span>
                    <p class="text-primary small mb-0">Pengguna</p>
                </div>
            </div>

            <div class="dropdown-menu dropdown-menu-end p-3 shadow text-dark mt-3" aria-labelledby="userDropdown">
                <div class="text-center mb-3">
                    <h6 class="mb-0"><strong>{{ auth()->user()->name }}</strong></h6>
                    <small class="text-dark ">{{ auth()->user()->email }}</small>
                </div>
                <hr>

                <a class="dropdown-item text-dark d-flex align-items-center gap-2 py-2" href="{{ route('profile') }}">
                    <i class="fas fa-user text-dark"></i> Pengaturan Akun
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger">
                        <i class="fas fa-power-off"></i> Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<style>
    .user-profile {
        margin-left: auto;
    }

    .profile-initial {
        width: 40px;
        height: 40px;
        background-color: rgb(18, 49, 142);
        font-size: 20px;
        border-radius: 50%;
        border: 2px solid white;
    }

    .dropdown-menu {
        background-color: rgb(255, 255, 255);
        border-radius: 10px !important;
    }

    .dropdown-item:hover {
        background-color:rgba(21, 113, 232, 0.22);
        border-radius: 2px;
    }
</style>