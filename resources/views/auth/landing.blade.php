<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang - Helpdesk Kota</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4/dist/tailwind.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 dark:bg-gray-900 flex items-center justify-center h-screen">
    <div class="text-center">
        <h1 class="text-4xl font-bold mb-4 text-gray-800 dark:text-gray-200">Selamat Datang di Helpdesk Kota</h1>
        <p class="text-lg mb-6 text-gray-600 dark:text-gray-400">Laporkan pengaduan Anda dengan mudah dan cepat!</p>
        <a href="{{ route('login') }}" class="bg-blue-500 text-white px-6 py-3 rounded hover:bg-blue-600">Login</a>
        <a href="{{ route('register') }}" class="bg-green-500 text-white px-6 py-3 rounded ml-4 hover:bg-green-600">Daftar</a>
    </div>
</body>
</html>