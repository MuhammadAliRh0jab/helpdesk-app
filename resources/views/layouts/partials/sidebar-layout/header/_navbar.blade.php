<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />

<nav id="header" class="navbar fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#">
            <img id="nav-logo" src="{{ asset('assets/media/img/logo-helpdesk-1.png') }}" class="img-fluid" style="width: 150px; height: auto;">
        </a>
        <button class="navbar-toggler d-lg-none" type="button" id="sidebarToggle">
            <i class="fas fa-bars text-white"></i>
        </button>
    </div>
</nav>

<div id="sidebar" class="sidebar">
    <div class="sidebar-content">
        <br><br><br>
        <ul class="nav flex-column mt-3">
            @if (auth()->user()->role_id == 2)
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('dashboard.operator') }}"><i class="fa-solid fa-house-user"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('tickets.index') }}"><i class="fa-solid fa-list"></i> Daftar Aduan</a>
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
            <li class="nav-item">
                <a class="nav-link text-dark" href="#"><i class="fa-solid fa-gears"></i> Pengaturan Akun</a>
            </li>
            @elseif (auth()->user()->role_id == 3)
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('dashboard.pegawai') }}"><i class="fa-solid fa-house-user"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('tickets.index') }}"><i class="fa-solid fa-list"></i> Daftar Aduan Saya</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('tickets.assigned') }}"><i class="fa-solid fa-briefcase"></i> Aduan Ditugaskan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('tickets.create') }}"><i class="fa-solid fa-marker"></i> Buat Aduan Baru</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" href="#"><i class="fa-solid fa-gears"></i> Pengaturan Akun</a>
            </li>
            @elseif (auth()->user()->role_id == 1)
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('dashboard.admin') }}"><i class="fa-solid fa-house-user"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('tickets.index') }}"><i class="fa-solid fa-list"></i> Daftar Aduan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('users.index') }}"><i class="fa-solid fa-users"></i> Kelola Pengguna</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" href="#"><i class="fa-solid fa-gears"></i> Pengaturan Akun</a>
            </li>
            @else
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('dashboard.warga') }}"><i class="fa-solid fa-house-user"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('tickets.index') }}"><i class="fa-solid fa-list"></i> Daftar Aduan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('tickets.create') }}"><i class="fa-solid fa-marker"></i> Buat Aduan Baru</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" href="#"><i class="fa-solid fa-gears"></i> Pengaturan Akun</a>
            </li>
            @endif
        </ul>

        <div class="sidebar-footer position-absolute bottom-0 mb-5 w-100">
            <form id="logout-form" action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger text-white"><i class="fa-solid fa-arrow-up-from-bracket" style="font-size: 16px;"></i> Logout</button>
            </form>
        </div>
    </div>
</div>
