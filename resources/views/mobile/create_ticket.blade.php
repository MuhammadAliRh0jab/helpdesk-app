@extends('mobile.master.app')

@section('header')
    @include('mobile.master.header_back')
@endsection

@section('content')
<div class="page-content-wrapper py-3">
    <div class="container">
        <div class="affan-element-item">
            <div class="element-heading-wrapper">
                <i class="bi bi-plus-circle"></i>
                <div class="heading-text">
                    <h5 class="mb-1">Manajemen Tiket</h5>
                    <span>Buat laporan pengaduan baru dengan mudah dan cepat.</span>
                </div>
            </div>
        </div>

        <div class="py-2">
            <div class="card shadow">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0">Buat Pengaduan</h5>
                    </div>
                    <hr>
                    <div class="alert alert-info my-4 text-center shadow-sm">
                        Untuk memudahkan petugas menjawab permasalahan Anda, pastikan Anda mengisi aduan dengan unit dan layanan yang sesuai.
                    </div>
                    <form id="ticketForm" action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label class="form-label text-dark" for="unit_id">Unit</label>
                            <select class="form-control form-control-clicked" name="unit_id" id="unit_id" required>
                                <option value="">Pilih Unit</option>
                                @foreach (\App\Models\Unit::all() as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                                @endforeach
                            </select>
                            @error('unit_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label text-dark" for="service_id">Layanan</label>
                            <select class="form-control form-control-clicked" name="service_id" id="service_id" required>
                                <option value="">Pilih Layanan</option>
                            </select>
                            @error('service_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label text-dark" for="title">Judul Aduan</label>
                            <input type="text" class="form-control form-control-clicked" name="title" id="title" required>
                            @error('title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label text-dark" for="description">Deskripsi Aduan</label>
                            <textarea class="form-control form-control-clicked" id="description" name="description" cols="3" rows="5" required>{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label text-dark" for="imageBase64Input">Unggah Gambar (Opsional)</label>
                            <input type="hidden" name="images[]" id="imageBase64Input">
                            <img class="m-1" id="imagePreview" src="" alt="No image yet" style="display:none; max-width:100%;">
                            <div>
                                <button type="button" id="openCameraBtn" class="btn btn-sm btn-primary">Pilih Gambar</button>
                            </div>
                            <p id="errorMessage" class="text-red-500 text-sm mt-1"></p>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-primary w-100 d-flex align-items-center justify-content-center">
                            Kirim Aduan
                            <i class="bi bi-arrow-right fz-16 ms-1"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
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
                            console.log('Error fetching services:', error);
                            $('#errorMessage').text('Error fetching services');
                        }
                    });
                } else {
                    $('#service_id').empty().append('<option value="">Pilih Layanan</option>');
                }
            });

            $('#ticketForm').on('submit', function(e) {
                e.preventDefault();
                var form = this;
                var formData = new FormData(form);

                // Log data yang akan dikirim
                console.log('Form data:', Array.from(formData.entries()));

                // Jika ada Base64, konversi ke file blob
                var base64Image = $('#imageBase64Input').val();
                if (base64Image) {
                    try {
                        var byteString = atob(base64Image);
                        var arrayBuffer = new ArrayBuffer(byteString.length);
                        var uint8Array = new Uint8Array(arrayBuffer);
                        for (var i = 0; i < byteString.length; i++) {
                            uint8Array[i] = byteString.charCodeAt(i);
                        }
                        var blob = new Blob([uint8Array], { type: 'image/jpeg' });
                        formData.set('images[]', blob, 'photo.jpg');
                        console.log('Image blob size:', blob.size / 1024, 'KB');
                    } catch (e) {
                        console.log('Error converting Base64 to blob:', e);
                        $('#errorMessage').text('Error converting image');
                        return;
                    }
                }

                $.ajax({
                    url: form.action,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log('Form submitted successfully:', response);
                        window.location.href = '{{ route('tickets.index') }}';
                    },
                    error: function(xhr) {
                        console.log('Error submitting form:', xhr.responseJSON);
                        var errors = xhr.responseJSON.errors || {};
                        var errorMessage = 'Error: ';
                        for (var field in errors) {
                            errorMessage += errors[field][0] + ' ';
                        }
                        $('#errorMessage').text(errorMessage || 'Unknown error occurred');
                    }
                });
            });
        });
    </script>

    <script src="{{ asset('mobile/js/android-bridge.js') }}"></script>
    <script>
        document.getElementById('openCameraBtn').addEventListener('click', function () {
            console.log('Opening camera');
            AndroidBridge.openCamera('AndroidBridge.showImagePreview');
        });
    </script>
@endsection
