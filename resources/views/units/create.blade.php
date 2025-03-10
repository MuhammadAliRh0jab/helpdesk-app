<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Unit Kerja</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4/dist/tailwind.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 dark:bg-gray-900 p-6">
    <h1 class="text-2xl font-bold mb-4 text-gray-800 dark:text-gray-200">Tambah Unit Kerja</h1>

    @if ($errors->any())
        <div class="bg-red-100 text-red-800 p-4 mb-4 rounded dark:bg-red-900 dark:text-red-200">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('units.store') }}" method="POST" class="max-w-lg bg-white dark:bg-gray-800 p-6 rounded shadow dark:shadow-gray-700">
        @csrf
        <div class="mb-4">
            <label for="unit_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Unit</label>
            <input type="text" id="unit_name" name="unit_name" class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:border-indigo-300 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 dark:bg-gray-900 dark:text-gray-200" required>
        </div>
        <div class="mb-4">
            <label for="unit_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kode Unit</label>
            <input type="text" id="unit_code" name="unit_code" class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:border-indigo-300 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 dark:bg-gray-900 dark:text-gray-200" required>
        </div>
        <div class="mb-4">
            <label for="unit_short" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Singkatan</label>
            <input type="text" id="unit_short" name="unit_short" class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:border-indigo-300 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 dark:bg-gray-900 dark:text-gray-200" required>
        </div>
        <div class="mb-4">
            <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alamat</label>
            <textarea id="address" name="address" class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:border-indigo-300 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 dark:bg-gray-900 dark:text-gray-200" rows="2"></textarea>
        </div>
        <div class="flex items-center justify-end">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700">Simpan</button>
            <a href="{{ route('units.index') }}" class="ml-4 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">Batal</a>
        </div>
    </form>
</body>
</html>