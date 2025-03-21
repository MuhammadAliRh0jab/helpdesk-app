    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />

    <nav id="header" class="navbar fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#" id="sidebarToggle">
                <img id="nav-logo" src="{{ asset('assets/media/img/logo-helpdesk-1.png') }}" class="img-fluid" style="width: 150px; height: auto;">
            </a>
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
                    <a class="nav-link text-dark" href="{{ route('tickets.created') }}"><i class="fa-solid fa-clock-rotate-left"></i> Riwayat Aduan Saya</a>
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

            <div class="sidebar-footer position-absolute bottom-0 mb-4 w-100">
                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger text-white"><i class="fa-solid fa-arrow-up-from-bracket" style="font-size: 16px;"></i> Logout</button>
                </form>
            </div>
        </div>
    </div>

    <style>
        #header {
            transition: background-color 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            background: linear-gradient(90deg, #1572e8 0%, rgb(21, 68, 144) 100%);
            border-bottom: 3px solid rgba(9, 9, 9, 0.17);
        }

        .sidebar {
            height: 100%;
            width: 0;
            position: fixed;
            z-index: 1000;
            top: 0;
            left: 0;
            background-color: white;
            overflow-x: hidden;
            transition: 0.3s;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .nav-item{
            padding-bottom: 10px;
        }
        .sidebar.active {
            width: 250px;
        }
        
        .sidebar-content {
            padding: 20px;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .sidebar-header {
            padding-bottom: 20px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .nav-link {
            color: #000 !important;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            background-color: rgba(0, 0, 0, 0.08);
            color: #1572e8 !important;
            border-radius: 5px;
        }
        
        .sidebar-footer {
            padding-top: 20px;
        }
        
        .btn-danger {
            transition: all 0.3s ease;
        }
        
        .btn-danger:hover {
            background-color: rgb(255, 255, 255);
            color: red !important;
        }
        
        .nav-item i {
            font-size: 16px;
            color: #1572e8;
        }
    </style>

    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function(e) {
            e.preventDefault();
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');

            sidebar.classList.toggle('active');
            mainContent.classList.toggle('expanded');
        });
    </script>