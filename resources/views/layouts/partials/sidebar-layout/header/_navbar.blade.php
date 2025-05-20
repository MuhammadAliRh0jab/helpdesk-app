<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Helpdesk</title>

    <!-- Icons -->
    <!-- <link rel="shortcut icon" href="{{asset('assets2/media/favicons/favicon.png')}}"> -->
    <!-- <link rel="icon" type="image/png" sizes="192x192" href="{{asset('assets2/media/favicons/favicon-192x192.png')}}"> -->
    <link rel="apple-touch-icon" sizes="180x180"
        href="{{asset('assets2/media/favicons/apple-touch-icon-180x180.png')}}">

    <!-- OneUI framework -->
    <link rel="stylesheet" id="css-main" href="{{asset('assets2/css/oneui.min.css')}}">
    <script src="{{asset('assets2/js/setTheme.js/')}}"></script>
    <style>
        .nav-main-link {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            /* border-radius: 8px; */
            color: #4B5563;
            text-decoration: none;
            transition: background-color 0.3s, color 0.3s;
        }

        .nav-main-link:hover {
            background-color: #487FFF;
            color: white !important;
        }

        .nav-main-link:hover .nav-main-link-icon,
        .nav-main-link:hover .nav-main-link-name {
            color: white !important;
        }

        .nav-main-link.active {
            background-color: #487FFF;
            color: white !important;
            font-weight: 600;
        }

        .nav-main-link.active .nav-main-link-icon,
        .nav-main-link.active .nav-main-link-name {
            color: white !important;
        }

        #page-header {
            background-color: #ebeef2 !important;
            position: fixed;
        }

        #sidebar {
            box-shadow: 1px 0 8px rgba(0, 0, 0, 0.15) !important;
            padding-right: 10px;
        }
    </style>
</head>

<body>
    <div id="page-container" class="sidebar-o sidebar-dark enable-page-overlay side-scroll page-header-fixed main-content-narrow">
        <nav id="sidebar" aria-label="Main Navigation" style="background-color: #ebeef2">
            <div class="content-header d-flex justify-content-center align-items-center" style="background-color: #ebeef2">
                <a class="fw-semibold text-dual text-center">
                    <span class="smini-visible">
                        <i class="fa fa-circle-notch text-primary"></i>
                    </span>
                    <span class="smini-hide">
                        <img src="{{ asset('assets/media/img/logo-helpdesk-black.png') }}" alt="Helpdesk Logo" height="50">
                    </span>
                </a>
                <div class="d-flex align-items-center gap-1">
                    <!-- Close Sidebar, Visible only on mobile screens -->
                    <a class="d-lg-none btn btn-sm btn-alt-secondary ms-1" data-toggle="layout"
                        data-action="sidebar_close" href="javascript:void(0)">
                        <i class="fa fa-fw fa-times"></i>
                    </a>
                </div>
            </div>

            <!-- Sidebar Scrolling -->
            <div class="js-sidebar-scroll">
                <div class="content-side" style="margin-left: 10px;">
                    <ul class="nav-main">
                        @if (auth()->user()->role_id == 2) {{-- Operator --}}
                        <li class="nav-main-item mb-2">
                            <a class="nav-main-link d-flex align-items-center text-dark {{ request()->routeIs('dashboard.operator') ? 'active' : '' }}" href="{{ route('dashboard.operator') }}">
                                <i class="nav-main-link-icon si si-speedometer me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-main-item mb-2">
                            <a class="nav-main-link d-flex align-items-center text-dark" href="{{ route('tickets.index') }}">
                                <i class="nav-main-link-icon si si-layers me-2"></i>
                                Detail Aduan
                            </a>
                        </li>
                        <li class="nav-main-item mb-2">
                            <a class="nav-main-link d-flex align-items-center text-dark" href="{{ route('services.index') }}">
                                <i class="nav-main-link-icon si si-wrench me-2"></i>
                                Kelola Layanan
                            </a>
                        </li>
                        <li class="nav-main-item mb-2">
                            <a class="nav-main-link d-flex align-items-center text-dark" href="{{ route('tickets.created') }}">
                                <i class="nav-main-link-icon si si-clock me-2"></i>
                                Riwayat Aduan
                            </a>
                        </li>
                        <li class="nav-main-item mb-2">
                            <a class="nav-main-link d-flex align-items-center text-dark" href="{{ route('tickets.create') }}">
                                <i class="nav-main-link-icon si si-pencil me-2"></i>
                                Buat Aduan Baru
                            </a>
                        </li>
                        @elseif (auth()->user()->role_id == 3) {{-- Pegawai --}}
                        <li class="nav-main-item mb-2">
                            <a class="nav-main-link d-flex align-items-center text-dark {{ request()->routeIs('dashboard.pegawai') ? 'active' : '' }}" href="{{ route('dashboard.pegawai') }}">
                                <i class="nav-main-link-icon si si-speedometer me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-main-item mb-2">
                            <a class="nav-main-link d-flex align-items-center text-dark" href="{{ route('tickets.index') }}">
                                <i class="nav-main-link-icon si si-layers me-2"></i>
                                Detail Aduan
                            </a>
                        </li>
                        <li class="nav-main-item mb-2">
                            <a class="nav-main-link d-flex align-items-center text-dark" href="{{ route('tickets.assigned') }}">
                                <i class="nav-main-link-icon si si-briefcase me-2"></i>
                                Aduan Ditugaskan
                            </a>
                        </li>
                        <li class="nav-main-item mb-2">
                            <a class="nav-main-link d-flex align-items-center text-dark" href="{{ route('tickets.create') }}">
                                <i class="nav-main-link-icon si si-pencil me-2"></i>
                                Buat Aduan Baru
                            </a>
                        </li>
                        @elseif (auth()->user()->role_id == 1) {{-- Admin --}}
                        <li class="nav-main-item mb-2">
                            <a class="nav-main-link d-flex align-items-center text-dark {{ request()->routeIs('dashboard.admin') ? 'active' : '' }}" href="{{ route('dashboard.admin') }}">
                                <i class="nav-main-link-icon si si-speedometer me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-main-item mb-2">
                            <a class="nav-main-link d-flex align-items-center text-dark" href="{{ route('tickets.index') }}">
                                <i class="nav-main-link-icon si si-layers me-2"></i>
                                Detail Aduan
                            </a>
                        </li>
                        <li class="nav-main-item mb-2">
                            <a class="nav-main-link d-flex align-items-center text-dark" href="{{ route('users.index') }}">
                                <i class="nav-main-link-icon si si-users me-2"></i>
                                Kelola Pengguna
                            </a>
                        </li>
                        @else {{-- Warga --}}
                        <li class="nav-main-item mb-2">
                            <a class="nav-main-link d-flex align-items-center text-dark {{ request()->routeIs('dashboard.warga') ? 'active' : '' }}" href="{{ route('dashboard.warga') }}">
                                <i class="nav-main-link-icon si si-speedometer me-2"></i>
                                Dashboard
                            </a>
                        </li>

                        <li class="nav-main-item mb-2">
                            <a class="nav-main-link d-flex align-items-center text-dark" href="{{ route('tickets.index') }}">
                                <i class="nav-main-link-icon si si-layers me-2"></i>
                                Detail Aduan
                            </a>
                        </li>

                        <li class="nav-main-item mb-2">
                            <a class="nav-main-link d-flex align-items-center text-dark" href="{{ route('tickets.create') }}">
                                <i class="nav-main-link-icon si si-pencil me-2"></i>
                                Buat Aduan Baru
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Header -->
        <header id="page-header">
            <div class="content-header">
                <div class="d-flex align-items-center">
                    <!-- Toggle Sidebar -->
                    <button type="button" class="btn btn-sm btn-alt-secondary me-2 d-lg-none" data-toggle="layout"
                        data-action="sidebar_toggle">
                        <i class="fa fa-fw fa-bars"></i>
                    </button>
                </div>

                <!-- Right Section -->
                <div class="d-flex align-items-center">
                    <div class="dropdown d-inline-block ms-2">
                        <button type="button" class="btn btn-sm d-flex align-items-center"
                            id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                            <img class="rounded-circle" src="{{ asset('assets2/media/avatars/avatar10.jpg') }}"
                                alt="Header Avatar" style="width: 21px;">
                            <span class="d-none d-sm-inline-block ms-2">{{ explode(' ', auth()->user()->name)[0] }}</span>
                            <i class="fa fa-fw fa-angle-down d-none d-sm-inline-block opacity-50 ms-1 mt-1"></i>
                        </button>

                        <div class="dropdown-menu dropdown-menu-md dropdown-menu-end p-0 border-0"
                            aria-labelledby="page-header-user-dropdown">
                            <div class="p-3 text-center bg-body-light border-bottom rounded-top">
                                <img class="img-avatar img-avatar48 img-avatar-thumb"
                                    src="{{ asset('assets2/media/avatars/avatar10.jpg') }}" alt="">
                                <p class="mt-2 mb-0 fw-medium">{{ auth()->user()->name }}</p>
                                <p class="mb-0 text-muted fs-sm fw-medium">{{ auth()->user()->email }}</p>
                            </div>

                            <div class="p-2">
                                <a class="dropdown-item d-flex align-items-center justify-content-between"
                                    href="{{ route('profile') }}">
                                    <i class="si si-settings"></i>
                                    <span class="fs-sm fw-medium">Pengaturan Akun</span>
                                </a>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item d-flex align-items-center justify-content-between text-danger">
                                        <i class="si si-logout"></i>
                                        <span class="fs-sm fw-medium">Keluar</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- Header Search -->
            <div id="page-header-search" class="overlay-header bg-body-extra-light">
                <div class="content-header">
                    <form class="w-100" action="be_pages_generic_search.html" method="POST">
                        <div class="input-group">
                            <button type="button" class="btn btn-alt-danger" data-toggle="layout"
                                data-action="header_search_off">
                                <i class="fa fa-fw fa-times-circle"></i>
                            </button>
                            <input type="text" class="form-control" placeholder="Search or hit ESC.."
                                id="page-header-search-input" name="page-header-search-input">
                        </div>
                    </form>
                </div>
            </div>
            <!-- END Header Search -->

            <!-- Header Loader -->
            <!-- Please check out the Loaders page under Components category to see examples of showing/hiding it -->
            <div id="page-header-loader" class="overlay-header bg-body-extra-light">
                <div class="content-header">
                    <div class="w-100 text-center">
                        <i class="fa fa-fw fa-circle-notch fa-spin"></i>
                    </div>
                </div>
            </div>
        </header>
    </div>
    <style>
        #page-container {
            min-height: 1rem;
        }

        .nav-main-link:hover {
            background-color: #0287ff !important;
            color: rgb(255, 255, 255) !important;
            border-radius: 5px;
            position: relative;
        }

        .dropdown-item:hover {
            background-color: #0287ff !important;
            color: rgb(255, 255, 255) !important;
            border-radius: 5px;
            position: relative;
        }

        .dropdown-item.text-danger:hover {
            background-color: #dc3545 !important;
        }

        .nav-main-link.active,
        .nav-main-link:hover {
            background-color: #0287ff !important;
            color: #ffffff !important;
            border-radius: 5px;
            position: relative;
        }
    </style>
    <script src="{{asset('assets2/js/oneui.app.min.js')}}"></script>
    <script src="{{asset('assets2/js/plugins/chart.js/chart.umd.js')}}"></script>
    <script src="{{asset('assets2/js/pages/be_pages_dashboard.min.js')}}"></script>
    <script>
        // Saat dokumen sudah siap
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.nav-main-link');

            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    // Hapus class 'active' dari semua link
                    navLinks.forEach(nav => nav.classList.remove('active'));

                    // Tambahkan class 'active' ke link yang diklik
                    this.classList.add('active');

                    // Simpan di localStorage agar tetap aktif saat reload
                    localStorage.setItem('activeLink', this.href);
                });
            });

            // Ambil dari localStorage dan tetapkan 'active' saat reload
            const activeHref = localStorage.getItem('activeLink');
            if (activeHref) {
                navLinks.forEach(link => {
                    if (link.href === activeHref) {
                        link.classList.add('active');
                    }
                });
            }
        });
    </script>

</body>

</html>