<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Unit Kerja</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4/dist/tailwind.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 dark:bg-gray-900 p-6">
    <h1 class="text-2xl font-bold mb-4 text-gray-800 dark:text-gray-200">Daftar Unit Kerja</h1>

    @if (session('success'))
        <div class="bg-green-100 text-green-800 p-4 mb-4 rounded dark:bg-green-900 dark:text-green-200">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('units.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded mb-4 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700">Tambah Unit Baru</a>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white dark:bg-gray-800 border dark:border-gray-700">
            <thead>
                <tr class="bg-gray-200 dark:bg-gray-700">
                    <th class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">Nama Unit</th>
                    <th class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">Kode Unit</th>
                    <th class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">Singkatan</th>
                    <th class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">Alamat</th>
                    <th class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($units as $unit)
                    <tr>
                        <td class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">{{ $unit->unit_name }}</td>
                        <td class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">{{ $unit->unit_code }}</td>
                        <td class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">{{ $unit->unit_short }}</td>
                        <td class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">{{ $unit->address ?? 'Tidak ada' }}</td>
                        <td class="py-2 px-4 border-b dark:border-gray-600">
                            <a href="{{ route('units.edit', $unit->id) }}" class="text-blue-500 hover:underline dark:text-blue-400 mr-2">Edit</a>
                            <form action="{{ route('units.destroy', $unit->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline dark:text-red-400" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <a href="{{ route('tickets.index') }}" class="inline-block bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700">Kembali ke Aduan</a>
        <a href="{{ route('logout') }}" class="ml-4 inline-block bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 dark:bg-red-600 dark:hover:bg-red-700" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            Logout
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>
</body>
</html>