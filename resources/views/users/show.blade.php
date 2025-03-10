<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4/dist/tailwind.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 dark:bg-gray-900 p-6">
    <h1 class="text-2xl font-bold mb-4 text-gray-800 dark:text-gray-200">Detail Pengguna: {{ $user->name }}</h1>

    <div class="bg-white dark:bg-gray-800 p-4 border rounded shadow dark:shadow-gray-700 dark:border-gray-700">
        <p><strong>Nama:</strong> {{ $user->name }}</p>
        <p><strong>Username:</strong> {{ $user->username }}</p>
        <p><strong>Email:</strong> {{ $user->email ?? 'Tidak ada' }}</p>
        <p><strong>Telepon:</strong> {{ $user->phone ?? 'Tidak ada' }}</p>
        <p><strong>Unit Kerja:</strong> {{ $user->unit ? $user->unit->unit_name : 'Tidak ada' }}</p>
        <p><strong>Role:</strong> {{ $user->role ? $user->role->role_name : 'Tidak ada' }}</p>
        <p><strong>Fungsi:</strong> {{ $user->getUserFunction() }}</p>
    </div>

    <div class="mt-4">
        <a href="{{ route('users.index') }}" class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700">Kembali ke Daftar Pengguna</a>
        <a href="{{ route('tickets.index') }}" class="ml-4 inline-block bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700">Kembali ke Aduan</a>
        <a href="{{ route('logout') }}" class="ml-4 inline-block bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 dark:bg-red-600 dark:hover:bg-red-700" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            Logout
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>
</body>
</html>