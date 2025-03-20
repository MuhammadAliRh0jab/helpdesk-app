<nav id="header" class="navbar navbar-expand-lg navbar-light fixed-top bg-white text-white">
    <div class="container">
        <a class="navbar-brand" href="{{ route('tickets.index') }}">
            <img id="nav-logo" src="{{ asset('assets/media/img/logo-helpdesk-1.png') }}" class="img-fluid" style="width: 150px; height: auto;">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav-content" aria-controls="nav-content" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav-content">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                @if (auth()->user()->role_id == 2)
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.operator') }}">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('tickets.index') }}">Daftar Aduan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('services.index') }}">Kelola Layanan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('tickets.created') }}">Riwayat Aduan Saya</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('tickets.create') }}">Buat Aduan Baru</a>
                </li>
                @elseif (auth()->user()->role_id == 3)
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.pegawai') }}">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('tickets.index') }}">Daftar Aduan Saya</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('tickets.assigned') }}">Aduan Ditugaskan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('tickets.create') }}">Buat Aduan Baru</a>
                </li>
                @elseif (auth()->user()->role_id == 1)
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.admin') }}">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('tickets.index') }}">Daftar Aduan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('users.index') }}">Kelola Pengguna</a>
                </li>
                @else
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.warga') }}">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('tickets.index') }}">Daftar Aduan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('tickets.create') }}">Buat Aduan Baru</a>
                </li>
                @endif
            </ul>
            <button class="btn btn-danger text-white fw-bold rounded-pill ms-3 mt-3 mt-lg-0 px-4 py-2 shadow" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                Logout
            </button>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </div>
</nav>

<style>
    #header {
        transition: background-color 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        background: linear-gradient(90deg, #1572e8 0%, rgb(21, 68, 144) 100%);

        border-bottom: 3px solid #1572e8;
    }

    #header .nav-link {
        color: white !important;
        transition: color 0.3s ease-in-out;
    }

    #header .nav-link:hover,
    #header.scrolled .nav-link:hover {
        color:rgb(4, 23, 47) !important;
        background-color: rgba(18, 58, 125, 0.2);
        border-radius: 50px;
        padding: 0.5rem 0.5rem;
    }

    #header.scrolled {
        background-color: white !important;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .btn-danger:hover {
        color: rgb(157, 0, 0) !important;
        background-color: #f8f9fa;
    }
</style>