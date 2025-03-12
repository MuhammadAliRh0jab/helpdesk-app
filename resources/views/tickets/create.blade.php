<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Aduan Baru - Helpdesk Kota</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4/dist/tailwind.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 dark:bg-gray-900 p-6">
    <h1 class="text-2xl font-bold mb-4 text-gray-800 dark:text-gray-200">Buat Aduan Baru</h1>

    @if ($errors->any())
        <div class="bg-red-100 text-red-800 p-4 mb-4 rounded dark:bg-red-900 dark:text-red-200">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-gray-800 p-6 rounded shadow-md dark:shadow-gray-700" id="ticketForm">
        @csrf

        <div class="mb-4">
            <label for="unit_id" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Pilih Unit yang Menangani</label>
            <select name="unit_id" id="unit_id" class="w-full border p-2 rounded dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" required>
                <option value="">Pilih Unit</option>
                @foreach ($units as $unit)
                    <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                @endforeach
            </select>
            @error('unit_id')
                <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="service_id" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Pilih Layanan</label>
            <select name="service_id" id="service_id" class="w-full border p-2 rounded dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" required>
                <option value="">Pilih Layanan</option>
                @foreach ($services as $service)
                    <option value="{{ $service->id }}">{{ $service->svc_name }}</option>
                @endforeach
            </select>
            @error('service_id')
                <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="title" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Judul Aduan</label>
            <input type="text" name="title" id="title" value="{{ old('title') }}" class="w-full border p-2 rounded dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" required>
            @error('title')
                <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="description" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Deskripsi Aduan</label>
            <textarea name="description" id="description" rows="5" class="w-full border p-2 rounded dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" required>{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="images" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Unggah Gambar (Opsional)</label>
            <input type="file" name="images[]" id="images" multiple class="w-full border p-2 rounded dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
            @error('images.*')
                <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex space-x-4">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700">
                Kirim Aduan
            </button>
            <a href="{{ route('tickets.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700">
                Kembali
            </a>
        </div>
    </form>

    @section('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#unit_id').on('change', function() {
                    var unitId = $(this).val();
                    if (unitId) {
                        $.ajax({
                            url: '{{ route('get.services', ':unitId') }}'.replace(':unitId', unitId),
                            method: 'GET',
                            success: function(data) {
                                var $serviceSelect = $('#service_id');
                                $serviceSelect.empty();
                                $serviceSelect.append('<option value="">Pilih Layanan</option>');
                                $.each(data, function(index, service) {
                                    $serviceSelect.append('<option value="' + service.id + '">' + service.svc_name + '</option>');
                                });
                            },
                            error: function(xhr, status, error) {
                                console.log('Error:', error);
                            }
                        });
                    } else {
                        $('#service_id').empty().append('<option value="">Pilih Layanan</option>');
                    }
                });
            });
        </script>
    @endsection
</body>
</html>