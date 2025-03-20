@extends('layouts.app')

@section('title', 'Buat Aduan Baru')

@section('content')
<h1 class="h4 mb-4 text-dark fs-2">Buat Aduan Baru</h1>

<form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data" class="card p-4 shadow">
    @csrf
    <div class="mb-3">
        <label for="unit_id" class="form-label fw-semibold text-dark">Unit</label>
        <select name="unit_id" id="unit_id" class="form-select" required>
            <option value="">Pilih Unit</option>
            @foreach (\App\Models\Unit::all() as $unit)
            <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
            @endforeach
        </select>
        @error('unit_id')
        <p class="text-danger small mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-3">
        <label for="service_id" class="form-label fw-semibold text-dark">Layanan</label>
        <select name="service_id" id="service_id" class="form-select" required>
            <option value="">Pilih Layanan</option>
        </select>
        @error('service_id')
        <p class="text-danger small mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-3">
        <label for="title" class="form-label fw-semibold text-dark">Judul Aduan</label>
        <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
        @error('title')
        <p class="text-danger small mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-3">
        <label for="description" class="form-label fw-semibold text-dark">Deskripsi Aduan</label>
        <textarea name="description" id="description" class="form-control" rows="5" required>{{ old('description') }}</textarea>
        @error('description')
        <p class="text-danger small mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-3">
        <label for="images" class="form-label fw-semibold text-dark">Lampiran Gambar</label>
        <input type="file" name="images[]" id="images" multiple class="form-control">
        @error('images')
        <p class="text-danger small mt-1">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary px-4 py-2">
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
                    url: '{{ route("get.services", ":unitId") }}'.replace(':unitId', unitId),
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