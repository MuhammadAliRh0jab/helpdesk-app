@extends('layouts.app')

@section('title', 'Buat Aduan Baru')

@section('content')
<div id="page-container" class="sidebar-o sidebar-dark enable-page-overlay side-scroll page-header-fixed main-content-narrow">
    <main id="main-container">
        <div class="content">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
                <div class="flex-grow-1">
                    <h1 class="h3 fw-bold mb-1">
                        Buat Aduan
                    </h1>
                    <h2 class="fs-base lh-base fw-medium text-muted mb-0">
                        Isikan form berikut sesuai keluhan Anda
                    </h2>
                </div>
                <nav class="flex-shrink-0 mt-3 mt-sm-0 ms-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">
                            <a class="link-fx" href="javascript:void(0)">Aduan</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">
                            Buat
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="block block-rounded mt-4 col-lg-10 mx-auto">
            <div class="block-header block-header-default">
                <h3 class="block-title">Form Aduan</h3>
            </div>
            <div class="block-content block-content-full">
                <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return false;">
                    @csrf
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-floating mb-4">
                                <select class="form-select" id="unit_id" name="unit_id" required>
                                    <option value="">Pilih Unit Kerja</option>
                                    @foreach (\App\Models\Unit::all() as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                                    @endforeach
                                </select>
                                <label for="unit_id">Unit Kerja</label>
                                @error('unit_id')
                                <p class="text-danger small mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-floating mb-4">
                                <select class="form-select" id="service_id" name="service_id" required>
                                    <option value="">Pilih Layanan/Topik</option>
                                </select>
                                <label for="service_id">Layanan/Topik</label>
                                @error('service_id')
                                <p class="text-danger small mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-floating mb-4">
                                <input type="text" class="form-control" id="title" name="title" placeholder="Masukkan Judul Aduan" value="{{ old('title') }}" required>
                                <label for="title">Judul Aduan</label>
                                @error('title')
                                <p class="text-danger small mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-floating mb-4">
                                <textarea class="form-control" id="description" name="description" style="height: 200px" placeholder="Masukkan Deskripsi Aduan" required>{{ old('description') }}</textarea>
                                <label for="description">Deskripsi Aduan</label>
                                @error('description')
                                <p class="text-danger small mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="images" class="form-label text-dark">Lampirkan Gambar (opsional)</label>
                                <div class="input-group">
                                    <button class="btn btn-outline-secondary" type="button" id="custom-button">Pilih File</button>
                                    <span class="input-group-text" id="file-name" style="width: 100%;">Tidak ada file dipilih</span>
                                    <input type="file" name="images[]" id="images" multiple class="form-control d-none">
                                </div>
                                @error('images')
                                <p class="text-danger small mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <button type="submit" class="btn btn-primary px-4 py-2">
                                    <i class="fas fa-paper-plane me-2"></i>Kirim
                                </button>
                                <button type="reset" class="btn btn-dark px-4 py-2">
                                    <i class="fas fa-times me-2"></i>Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>

    </main>
</div>


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
            fileNameDisplay.textContent = 'Tidak ada file yang dipilih';
        }
    });
</script>

@endsection