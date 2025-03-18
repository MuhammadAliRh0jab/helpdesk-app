<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helpdesk Kota - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4/dist/tailwind.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen">
    <!-- Navbar -->
    <nav class="bg-gray-800 p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <a href="{{ route('tickets.index') }}" class="text-white text-xl font-bold">Helpdesk Kota</a>
            <div class="space-x-4">
                @if (auth()->user()->role_id == 2)
                    <!-- Operator -->
                    <a href="{{ route('dashboard.operator') }}" class="text-white hover:text-gray-300 transition duration-200">Dashboard</a>
                    <a href="{{ route('tickets.index') }}" class="text-white hover:text-gray-300 transition duration-200">Daftar Aduan</a>
                    <a href="{{ route('services.index') }}" class="text-white hover:text-gray-300 transition duration-200">Kelola Layanan</a>
                    <a href="{{ route('tickets.created') }}" class="text-white hover:text-gray-300 transition duration-200">Riwayat Aduan Saya</a>
                    <a href="{{ route('tickets.create') }}" class="text-white hover:text-gray-300 transition duration-200">Buat Aduan Baru</a>
                @elseif (auth()->user()->role_id == 3)
                    <!-- Pegawai (PIC) -->
                    <a href="{{ route('dashboard.pegawai') }}" class="text-white hover:text-gray-300 transition duration-200">Dashboard</a>
                    <a href="{{ route('tickets.index') }}" class="text-white hover:text-gray-300 transition duration-200">Daftar Aduan Saya</a>
                    <a href="{{ route('tickets.assigned') }}" class="text-white hover:text-gray-300 transition duration-200">Aduan Ditugaskan</a>
                    <a href="{{ route('tickets.create') }}" class="text-white hover:text-gray-300 transition duration-200">Buat Aduan Baru</a>
                @elseif (auth()->user()->role_id == 4)
                    <!-- Warga -->
                    <a href="{{ route('dashboard.warga') }}" class="text-white hover:text-gray-300 transition duration-200">Dashboard</a>
                    <a href="{{ route('tickets.index') }}" class="text-white hover:text-gray-300 transition duration-200">Daftar Aduan</a>
                    <a href="{{ route('tickets.create') }}" class="text-white hover:text-gray-300 transition duration-200">Buat Aduan Baru</a>
                @elseif (auth()->user()->role_id == 1)
                    <!-- Super Admin -->
                    <a href="{{ route('dashboard.admin') }}" class="text-white hover:text-gray-300 transition duration-200">Dashboard</a>
                    <a href="{{ route('users.index') }}" class="text-white hover:text-gray-300 transition duration-200">Kelola Pengguna</a>
                @endif
                <a href="{{ route('logout') }}" class="text-white hover:text-gray-300 transition duration-200" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="container mx-auto p-6">
        @if (session('success'))
            <div class="bg-green-100 text-green-800 p-4 mb-4 rounded dark:bg-green-900 dark:text-green-200">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 text-red-800 p-4 mb-4 rounded dark:bg-red-900 dark:text-red-200">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </div>

    @yield('scripts')
</body>
</html>