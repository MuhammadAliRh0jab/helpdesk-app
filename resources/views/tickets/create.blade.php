@extends('layouts.app')

@section('title', 'Buat Aduan Baru')

@section('content')
<div class="card-header shadow mb-4">
    <h1 class="h4 text-white fs-4">Buat Aduan Baru</h1>
    <p class="text-white fs-6">Helpdesk Pemerintah Kota Blitar</p>
</div>
<form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data" class="card-form p-4 shadow">
    @csrf
    <div class="mb-3">
        <label for="unit_id" class="form-label fw-semibold text-dark">Unit Kerja</label>
        <select name="unit_id" id="unit_id" class="form-select" required>
            <option value="">Pilih Unit Kerja</option>
            @foreach (\App\Models\Unit::all() as $unit)
            <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
            @endforeach
        </select>
        @error('unit_id')
        <p class="text-danger small mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-3">
        <label for="service_id" class="form-label fw-semibold text-dark">Layanan/Topik</label>
        <select name="service_id" id="service_id" class="form-select" required>
            <option value="">Pilih Layanan/Topik</option>
        </select>
        @error('service_id')
        <p class="text-danger small mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-3">
        <label for="title" class="form-label fw-semibold text-dark">Judul Aduan</label>
        <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" placeholder="Masukkan Judul Aduan" required>
        @error('title')
        <p class="text-danger small mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-3">
        <label for="description" class="form-label fw-semibold text-dark">Deskripsi Aduan</label>
        <textarea name="description" id="description" class="form-control" rows="5" placeholder="Masukkan Deskripsi Aduan" required>{{ old('description') }}</textarea>
        @error('description')
        <p class="text-danger small mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-3">
        <label for="images" class="form-label fw-semibold text-dark">Lampirkan Gambar (opsional)</label>
        <div class="input-group">
            <button class="btn btn-outline-secondary" type="button" id="custom-button">Pilih File</button>
            <span class="input-group-text" id="file-name" style="width: 100%;">Tidak ada file dipilih</span>
            <input type="file" name="images[]" id="images" multiple class="form-control d-none" placeholder="Pilih Gambar">
        </div>
        @error('images')
        <p class="text-danger small mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="d-flex justify-content-end gap-2">
        <button type="submit" class="btn btn-primary px-4 py-2">
            Kirim
        </button>
        <button type="reset" class="btn btn-dark px-4 py-2">
            Batal
        </button>
    </div>
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

    document.getElementById('custom-button').addEventListener('click', function() {
        document.getElementById('images').click();
    });

    // Tampilkan status file setelah dipilih
    document.getElementById('images').addEventListener('change', function() {
        const files = this.files;
        const fileNameDisplay = document.getElementById('file-name');
        if (files.length > 0) {
            fileNameDisplay.textContent = files.length > 1 ?
                `${files.length} file dipilih` :
                files[0].name;
        } else {
            fileNameDisplay.textContent = 'Tidak ada file dipilih';
        }
    });
</script>

@endsection