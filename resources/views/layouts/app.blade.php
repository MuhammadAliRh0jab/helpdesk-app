<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helpdesk Kota - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4/dist/tailwind.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 dark:bg-gray-900">
    <!-- Navbar -->
    <nav class="bg-gray-800 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <a href="{{ route('tickets.index') }}" class="text-white text-xl font-bold">Helpdesk Kota</a>
            <div class="space-x-4">
                @if (auth()->user()->role_id == 2)
                    <a href="{{ route('tickets.index') }}" class="text-white hover:text-gray-300">Daftar Aduan</a>
                    <a href="{{ route('services.index') }}" class="text-white hover:text-gray-300">Kelola Layanan</a>
                    <a href="{{ route('tickets.created') }}" class="text-white hover:text-gray-300">Riwayat Aduan Saya</a>
                    <a href="{{ route('tickets.create') }}" class="text-white hover:text-gray-300">Buat Aduan Baru</a>
                    <a href="{{ route('dashboard.index') }}" class="text-white hover:text-gray-300">Dashboard</a>
                @elseif (auth()->user()->role_id == 3)
                    <a href="{{ route('tickets.index') }}" class="text-white hover:text-gray-300">Daftar Aduan Saya</a>
                    <a href="{{ route('tickets.assigned') }}" class="text-white hover:text-gray-300">Aduan Ditugaskan</a>
                    <a href="{{ route('tickets.create') }}" class="text-white hover:text-gray-300">Buat Aduan Baru</a>
                @else
                    <a href="{{ route('tickets.index') }}" class="text-white hover:text-gray-300">Daftar Aduan</a>
                    <a href="{{ route('tickets.create') }}" class="text-white hover:text-gray-300">Buat Aduan Baru</a>
                @endif
                <a href="{{ route('logout') }}" class="text-white hover:text-gray-300" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="p-6">
        @yield('content')
    </div>

    @yield('scripts')
</body>
</html>