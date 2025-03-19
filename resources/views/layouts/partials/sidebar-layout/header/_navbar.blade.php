<div class="app-navbar flex-shrink-0 bg-gray-800 p-4">
    <div class="container mx-auto d-flex align-items-center justify-between">
        <div class="d-flex align-items-center navbar-container">
            <a href="{{ route('tickets.index') }}" class="app-navbar-item">
                <img src="{{ asset('assets/media/img/logo-helpdesk-1.png') }}" class="logo">
            </a>

            <nav class="d-flex align-items-center gap-1">
                @if (auth()->user()->role_id == 2)
                <a href="{{ route('tickets.index') }}" class="nav-link fs-6">Daftar Aduan</a>
                <a href="{{ route('services.index') }}" class="nav-link fs-6">Kelola Layanan</a>
                <a href="{{ route('tickets.created') }}" class="nav-link fs-6">Riwayat Aduan Saya</a>
                <a href="{{ route('tickets.create') }}" class="nav-link fs-6">Buat Aduan Baru</a>
                <a href="{{ route('dashboard.operator') }}" class="nav-link fs-6">Dashboard</a>
                @elseif (auth()->user()->role_id == 3)
                <a href="{{ route('tickets.index') }}" class="nav-link fs-6">Daftar Aduan Saya</a>
                <a href="{{ route('tickets.assigned') }}" class="nav-link fs-6">Aduan Ditugaskan</a>
                <a href="{{ route('tickets.create') }}" class="nav-link fs-6">Buat Aduan Baru</a>
                <a href="{{ route('dashboard.pegawai') }}" class="nav-link fs-6">Dashboard</a>
                @else
                <a href="{{ route('tickets.index') }}" class="nav-link fs-6">Daftar Aduan</a>
                <a href="{{ route('tickets.create') }}" class="nav-link fs-6">Buat Aduan Baru</a>
                @endif
                <a href="{{ route('logout') }}" class="nav-link-logout fs-6" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </nav>
        </div>
    </div>
</div>


<style>
    .app-navbar {
        background-color: #0f88df;
        padding: 1rem 3rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        width: 100%;
        display: flex;
        align-items: center;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1000;
    }

    .app-navbar-item img.logo {
        max-width: 150px;
        vertical-align: middle;
    }

    .nav-link,
    .nav-link-logout {
        color: white !important;
        text-decoration: none;
        padding: 0.5rem 1rem;
        transition: all 0.3s ease;
        display: inline-block;
        white-space: nowrap;
    }

    .nav-link:hover {
        color: #0f88df !important;
        background-color: white;
        border-radius: 50px;
        transform: scale(1.05);
    }

    .nav-link-logout:hover {
        color:rgb(255, 255, 255) !important;
        background-color:rgb(223, 15, 15);
        border-radius: 50px;
        transform: scale(1.05);
    }

    .navbar-container {
        display: flex;
        align-items: center;
        width: 100%;
    }

    nav {
        display: flex;
        align-items: center;
        margin-left: auto;
    }
</style>