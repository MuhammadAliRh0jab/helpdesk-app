<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Layanan</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4/dist/tailwind.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 dark:bg-gray-900 p-6">
    <h1 class="text-2xl font-bold mb-4 text-gray-800 dark:text-gray-200">Edit Layanan: {{ $service->svc_name }}</h1>

    @if ($errors->any())
        <div class="bg-red-100 text-red-800 p-4 mb-4 rounded dark:bg-red-900 dark:text-red-200">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('services.update', $service->id) }}" method="POST" enctype="multipart/form-data" class="max-w-lg bg-white dark:bg-gray-800 p-6 rounded shadow dark:shadow-gray-700">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label for="unit_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unit Kerja</label>
            <select id="unit_id" name="unit_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:border-indigo-300 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 dark:bg-gray-900 dark:text-gray-200" required>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}" {{ $service->unit_id == $unit->id ? 'selected' : '' }}>{{ $unit->unit_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label for="svc_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Layanan</label>
            <input type="text" id="svc_name" name="svc_name" value="{{ old('svc_name', $service->svc_name) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:border-indigo-300 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 dark:bg-gray-900 dark:text-gray-200" required>
        </div>
        <div class="mb-4">
            <label for="svc_desc" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
            <textarea id="svc_desc" name="svc_desc" class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:border-indigo-300 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 dark:bg-gray-900 dark:text-gray-200" rows="2">{{ old('svc_desc', $service->svc_desc) }}</textarea>
        </div>
        <div class="mb-4">
            <label for="svc_icon" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ikon Layanan (Opsional, Unggah Baru)</label>
            <input type="file" id="svc_icon" name="svc_icon" accept="image/*" class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:border-indigo-300 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 dark:bg-gray-900 dark:text-gray-200">
            @if($service->svc_icon)
                <p class="mt-2 text-gray-800 dark:text-gray-200">Ikon saat ini: <img src="{{ asset('storage/' . $service->svc_icon) }}" alt="{{ $service->svc_name }}" class="h-10 w-10 object-cover inline dark:border-gray-700"></p>
            @endif
        </div>
        <div class="flex items-center justify-end">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700">Update</button>
            <a href="{{ route('services.index') }}" class="ml-4 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">Batal</a>
        </div>
    </form>
</body>
</html>