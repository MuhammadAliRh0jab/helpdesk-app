@extends('layouts.app')

@section('title', 'Buat Aduan Baru')

@section('content')
    <h1 class="text-2xl font-bold mb-4 text-gray-800 dark:text-gray-200">Buat Aduan Baru</h1>

    <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
        @csrf
        <div class="mb-4">
            <label for="unit_id" class="block text-gray-800 dark:text-gray-200 font-semibold mb-2">Unit</label>
            <select name="unit_id" id="unit_id" class="w-full border p-2 rounded dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" required>
                <option value="">Pilih Unit</option>
                @foreach (\App\Models\Unit::all() as $unit)
                    <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                @endforeach
            </select>
            @error('unit_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="service_id" class="block text-gray-800 dark:text-gray-200 font-semibold mb-2">Layanan</label>
            <select name="service_id" id="service_id" class="w-full border p-2 rounded dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" required>
                <option value="">Pilih Layanan</option>
            </select>
            @error('service_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="title" class="block text-gray-800 dark:text-gray-200 font-semibold mb-2">Judul Aduan</label>
            <input type="text" name="title" id="title" class="w-full border p-2 rounded dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" value="{{ old('title') }}" required>
            @error('title')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="description" class="block text-gray-800 dark:text-gray-200 font-semibold mb-2">Deskripsi Aduan</label>
            <textarea name="description" id="description" class="w-full border p-2 rounded dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" rows="5" required>{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="images" class="block text-gray-800 dark:text-gray-200 font-semibold mb-2">Lampiran Gambar</label>
            <input type="file" name="images[]" id="images" multiple class="w-full border p-2 rounded dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
            @error('images')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700">
            Kirim Aduan
        </button>
    </form>
@endsection

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