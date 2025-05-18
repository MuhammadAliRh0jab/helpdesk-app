@extends('mobile.master.app')

@section('title', 'Buat Tiket')

@section('header')
    @include('mobile.master.header')
@endsection

@section('sidenav')
    @include('mobile.master.sidenav')
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
                            Sebelum mengisi aduan, pastikan anda memilih layanan yang sesuai dengan apa yang anda ingin adu.
                        </div>

                        <div x-data="{
                            showServices: true,
                            selectedService: null,
                            selectedUnit: null,
                            search: '',
                            scrollToUnit(unitId) {
                                const unitElement = document.getElementById('unit-' + unitId);
                                if (unitElement) {
                                    unitElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                                }
                            }
                        }">
                            <!-- Daftar Tombol Layanan -->
                            <div x-show="showServices" x-transition>
                                <div class="mb-4">
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="bi bi-search text-primary"></i>
                                        </span>
                                        <input x-model="search" type="text" class="form-control border-start-0"
                                            placeholder="Cari layanan...">
                                    </div>
                                    <div class="form-text" x-show="search !== ''">
                                        <i class="bi bi-info-circle me-1"></i> Pencarian akan menampilkan hasil dari semua
                                        unit
                                    </div>
                                </div>

                                <!-- Quick Jump Menu -->
                                <div class="mb-4" x-show="search === ''">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="text-muted"><i class="bi bi-filter me-1"></i> Pilih Unit:</span>
                                        <div class="unit-pills">
                                            @php
                                                $units = \App\Models\Service::with('unit')
                                                    ->get()
                                                    ->pluck('unit_id', 'unit.unit_name')
                                                    ->toArray();
                                            @endphp
                                            @foreach ($units as $unitName => $unitId)
                                                <button @click="scrollToUnit({{ $unitId }})"
                                                    class="btn btn-sm btn-outline-primary rounded-pill m-1">
                                                    {{ $unitName }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <!-- Daftar Unit dengan Layanan -->
                                <div class="scrolling-units">
                                    <!-- Jika ada pencarian, tampilkan semua layanan yang cocok -->
                                    <div x-show="search !== ''" class="mb-4">
                                        <div class="alert alert-info d-flex align-items-center">
                                            <i class="bi bi-search me-2"></i>
                                            <div>Menampilkan hasil pencarian untuk: <strong x-text="search"></strong></div>
                                        </div>
                                        <div class="row g-3">
                                            @foreach (\App\Models\Service::with('unit')->get() as $service)
                                                <div class="col-12 service-item"
                                                    x-show="'{{ $service->svc_name }}'.toLowerCase().includes(search.toLowerCase())">
                                                    <div class="card h-100 border-0 shadow-sm">
                                                        <div class="card-body p-3">
                                                            <div class="unit-badge mb-2">{{ $service->unit->unit_name }}
                                                            </div>
                                                            <button
                                                                @click="showServices = false; selectedService = {{ $service->id }}; selectedUnit = {{ $service->unit_id }}"
                                                                class="btn btn-link text-decoration-none p-0 w-100 text-start">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="flex-shrink-0 me-3">
                                                                        <div
                                                                            class="bg-light text-primary rounded-circle p-2">
                                                                            <i class="fas fa-concierge-bell fa-fw"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <h6 class="m-0">{{ $service->svc_name }}</h6>
                                                                    </div>
                                                                    <div class="flex-shrink-0 ms-2">
                                                                        <i class="bi bi-chevron-right text-muted"></i>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Tampilkan layanan dikelompokkan per unit saat tidak ada pencarian -->
                                    <div x-show="search === ''">
                                        @foreach ($units as $unitName => $unitId)
                                            <div id="unit-{{ $unitId }}" class="unit-section mb-4">
                                                <div class="unit-header">
                                                    <h6 class="mb-3 pb-2 border-bottom d-flex align-items-center">
                                                        <i class="bi bi-building me-2 text-primary"></i>
                                                        <span>{{ $unitName }}</span>
                                                        <div class="ms-auto">
                                                            <span class="badge bg-primary rounded-pill">
                                                                {{ \App\Models\Service::where('unit_id', $unitId)->count() }}
                                                                Layanan
                                                            </span>
                                                        </div>
                                                    </h6>
                                                </div>
                                                <div class="row g-3">
                                                    @foreach (\App\Models\Service::where('unit_id', $unitId)->get() as $service)
                                                        <div class="col-12 service-item">
                                                            <div class="card h-100 border-0 shadow-sm">
                                                                <div class="card-body p-3">
                                                                    <button
                                                                        @click="showServices = false; selectedService = {{ $service->id }}; selectedUnit = {{ $service->unit_id }}"
                                                                        class="btn btn-link text-decoration-none p-0 w-100 text-start">
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="flex-shrink-0 me-3">
                                                                                <div
                                                                                    class="bg-light text-primary rounded-circle p-2">
                                                                                    <i
                                                                                        class="fas fa-concierge-bell fa-fw"></i>
                                                                                </div>
                                                                            </div>
                                                                            <div class="flex-grow-1">
                                                                                <h6 class="m-0">{{ $service->svc_name }}
                                                                                </h6>
                                                                                <div class="text-muted small">
                                                                                    {{ $service->unit->unit_name }}</div>
                                                                            </div>
                                                                            <div class="flex-shrink-0 ms-2">
                                                                                <i
                                                                                    class="bi bi-chevron-right text-muted"></i>
                                                                            </div>
                                                                        </div>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Form Detail Aduan -->
                            <div x-show="!showServices" x-transition>
                                <form id="ticketForm" action="{{ route('tickets.store') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="unit_id" x-bind:value="selectedUnit">
                                    <input type="hidden" name="service_id" x-bind:value="selectedService">
                                    <div class="form-group">
                                        <label class="form-label text-dark" for="title">Judul Aduan</label>
                                        <input type="text" class="form-control form-control-clicked" name="title"
                                            id="title" required>
                                        @error('title')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label text-dark" for="description">Deskripsi Aduan</label>
                                        <textarea class="form-control form-control-clicked" id="description" name="description" cols="3"
                                            rows="5" required>{{ old('description') }}</textarea>
                                        @error('description')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label text-dark" for="imageBase64Input">Unggah Gambar Pendukung
                                            (Opsional)</label>
                                        <input type="hidden" name="images[]" id="imageBase64Input">
                                        <img class="mb-1 img-thumbnail" id="imagePreview" src=""
                                            alt="No image yet" style="display:none; max-width:30%;">
                                        <div>
                                            <button type="button" id="openCameraBtn"
                                                class="btn btn-sm btn-primary me-2">Pilih Gambar (Kamera)</button>
                                            <button type="button" id="openGalleryBtn"
                                                class="btn btn-sm btn-primary me-2">Pilih dari Galeri</button>
                                            <input type="file" id="fileFallback" accept="image/*" hidden>
                                        </div>
                                        <p id="errorMessage" class="text-red-500 text-sm mt-1"></p>
                                        <div class="form-text">
                                            <i class="fas fa-info-circle me-1"></i> Format yang didukung: JPG, PNG, GIF
                                            (maks 2MB per file)
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between gap-3">
                                        <button type="button"
                                            @click="showServices = true; selectedService = null; selectedUnit = null"
                                            class="btn btn-outline-secondary w-50">
                                            <i class="bi bi-arrow-left me-2"></i>Kembali
                                        </button>
                                        <button type="submit" class="btn btn-primary w-50">
                                            Kirim Aduan <i class="bi bi-arrow-right ms-1"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Modal Konfirmasi -->
                            <div class="modal fade" id="confirmModal" tabindex="-1" data-bs-backdrop="static"
                                data-bs-keyboard="false" aria-labelledby="confirmModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-body text-center py-4">
                                            <i class="bi bi-question-circle text-primary" style="font-size: 5rem;"></i>
                                            <h5 id="confirmModalLabel">Konfirmasi</h5>
                                            <p>Apakah anda yakin ingin membuat tiket pengaduan baru?</p>
                                            <div class="d-flex justify-content-center gap-3">
                                                <button type="button" id="confirmNoBtn"
                                                    class="btn btn-outline-secondary px-4"
                                                    data-bs-dismiss="modal">Tidak</button>
                                                <button type="button" id="confirmYesBtn"
                                                    class="btn btn-primary px-4">Ya</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        .unit-section {
            padding: 10px;
            border-radius: 8px;
            background-color: #fff;
            margin-bottom: 15px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.03);
        }

        .unit-header {
            position: sticky;
            top: -1px;
            background-color: #fff;
            z-index: 10;
            padding-top: 5px;
            padding-bottom: 5px;
            border-radius: 8px 8px 0 0;
        }

        .unit-badge {
            display: inline-block;
            padding: 3px 8px;
            background-color: #e9ecef;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 500;
            color: #495057;
            margin-bottom: 6px;
        }

        .unit-pills {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
        }

        .scrolling-units {
            max-height: 400px;
            overflow-y: auto;
            padding-right: 5px;
        }

        .scrolling-units::-webkit-scrollbar {
            width: 6px;
        }

        .scrolling-units::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .scrolling-units::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .scrolling-units::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.0/dist/cdn.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <script src="{{ asset('mobile/js/android-bridge.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Simpan form data sementara untuk digunakan setelah konfirmasi
            let formDataToSubmit = null;

            // Handle form submission via AJAX
            $('#ticketForm').on('submit', function(e) {
                e.preventDefault();
                formDataToSubmit = new FormData(this);

                // Log data yang akan dikirim untuk debugging
                console.log('Form data prepared:', Array.from(formDataToSubmit.entries()));

                // Tampilkan modal konfirmasi
                $('#confirmModal').modal('show');
            });

            // Handle tombol "Ya" pada modal konfirmasi
            $('#confirmYesBtn').on('click', function() {
                if (!formDataToSubmit) {
                    console.warn('No form data to submit');
                    $('#errorMessage').text('Error: No form data available');
                    $('#confirmModal').modal('hide');
                    return;
                }

                // Jika ada Base64, konversi ke file blob
                var base64Image = $('#imageBase64Input').val();
                if (base64Image) {
                    try {
                        var byteString = atob(base64Image.split(',')[
                        1]); // Remove data:image/jpeg;base64, prefix
                        var arrayBuffer = new ArrayBuffer(byteString.length);
                        var uint8Array = new Uint8Array(arrayBuffer);
                        for (var i = 0; i < byteString.length; i++) {
                            uint8Array[i] = byteString.charCodeAt(i);
                        }
                        var blob = new Blob([uint8Array], {
                            type: 'image/jpeg'
                        });
                        formDataToSubmit.set('images[]', blob, 'photo.jpg');
                        console.log('Image blob size:', blob.size / 1024, 'KB');
                    } catch (e) {
                        console.log('Error converting Base64 to blob:', e);
                        $('#errorMessage').text('Error converting image');
                        $('#confirmModal').modal('hide');
                        return;
                    }
                }

                // Kirim data melalui AJAX
                $.ajax({
                    url: $('#ticketForm').attr('action'),
                    method: 'POST',
                    data: formDataToSubmit,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log('Form submitted successfully:', response);
                        $('#confirmModal').modal('hide');
                        window.location.href = '{{ route('tickets.index') }}?success=true';
                    },
                    error: function(xhr) {
                        console.log('Error submitting form:', xhr.responseJSON);
                        var errors = xhr.responseJSON.errors || {};
                        var errorMessage = 'Error: ';
                        for (var field in errors) {
                            errorMessage += errors[field][0] + ' ';
                        }
                        $('#errorMessage').text(errorMessage || 'Unknown error occurred');
                        $('#confirmModal').modal('hide');
                    }
                });
            });

            // Handle tombol "Tidak" pada modal konfirmasi
            $('#confirmNoBtn').on('click', function() {
                formDataToSubmit = null; // Reset form data
                $('#confirmModal').modal('hide');
            });

            // Handle camera button
            $('#openCameraBtn').on('click', function() {
                console.log('Opening camera');
                if (typeof AndroidBridge !== 'undefined') {
                    AndroidBridge.openCamera('showImagePreview');
                } else {
                    $('#fileFallback').click(); // Fallback for desktop
                }
            });

            // Handle gallery button
            $('#openGalleryBtn').on('click', function() {
                console.log('Opening photo picker');
                if (typeof AndroidBridge !== 'undefined') {
                    AndroidBridge.openPhotoPicker('showImagePreview');
                } else {
                    $('#fileFallback').click(); // Fallback for desktop
                }
            });

            // Fallback for desktop file input
            $('#fileFallback').on('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        window.showImagePreview(e.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Function to show image preview
            window.showImagePreview = function(base64Image) {
                console.log('Showing image preview with base64 length:', base64Image.length);
                var $preview = $('#imagePreview');
                var $input = $('#imageBase64Input');

                if ($preview.length && $input.length) {
                    $preview.attr('src', base64Image);
                    $preview.css('display', 'block');
                    $input.val(base64Image);
                } else {
                    console.warn('Preview or input element not found');
                    $('#errorMessage').text('Error: Preview element not found');
                }
            };
        });
    </script>
@endsection
