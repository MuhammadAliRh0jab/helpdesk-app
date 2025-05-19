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
                            latitude: null,
                            longitude: null,
                            mapInitialized: false,
                            scrollToUnit(unitId) {
                                const unitElement = document.getElementById('unit-' + unitId);
                                if (unitElement) {
                                    unitElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                                }
                            },
                            initMap() {
                                if (this.latitude === null || this.longitude === null) {
                                    console.log('Coordinates not set yet, waiting for GPS');
                                    return;
                                }
                                const mapElement = document.getElementById('map');
                                if (!mapElement) {
                                    console.error('Map element not found');
                                    return;
                                }
                                console.log('Initializing map with container dimensions:', mapElement.offsetWidth, mapElement.offsetHeight);
                                try {
                                    this.map = L.map(mapElement, {
                                        scrollWheelZoom: false // Nonaktifkan zoom dengan scroll wheel
                                    }).setView([this.latitude, this.longitude], 13);
                                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                        attribution: '© <a href=\'https://www.openstreetmap.org/copyright\'>OpenStreetMap</a> contributors'
                                    }).addTo(this.map);
                                    const defaultIcon = L.icon({
                                        iconUrl: 'https://unpkg.com/leaflet@1.9.3/dist/images/marker-icon.png',
                                        iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.3/dist/images/marker-icon-2x.png',
                                        shadowUrl: 'https://unpkg.com/leaflet@1.9.3/dist/images/marker-shadow.png',
                                        iconSize: [25, 41],
                                        iconAnchor: [12, 41],
                                        popupAnchor: [1, -34],
                                        shadowSize: [41, 41]
                                    });
                                    this.marker = L.marker([this.latitude, this.longitude], {
                                        icon: defaultIcon
                                    }).addTo(this.map);
                                    // Tambahkan event klik pada peta untuk memindahkan marker
                                    this.map.on('click', (e) => {
                                        const { lat, lng } = e.latlng;
                                        this.latitude = lat;
                                        this.longitude = lng;
                                        this.marker.setLatLng([lat, lng]);
                                        console.log('Marker moved to:', lat, lng);
                                    });
                                    // Tambahkan event moveend untuk memastikan peta diperbarui setelah pan
                                    this.map.on('moveend', () => {
                                        this.map.invalidateSize();
                                        console.log('Map panned, invalidated size');
                                    });
                                    this.mapInitialized = true;
                                    setTimeout(() => {
                                        this.map.invalidateSize();
                                        console.log('Map size invalidated after initialization');
                                    }, 100);
                                    console.log('Map initialized successfully');
                                } catch (e) {
                                    console.error('Error initializing map:', e);
                                    this.mapInitialized = false;
                                    document.getElementById('errorMessage').innerText = 'Error initializing map: ' + e.message;
                                }
                            },
                            getLocation() {
                                console.log('Requesting GPS location');
                                if (typeof AndroidBridge !== 'undefined') {
                                    AndroidBridge.getLocation('setLocation');
                                } else {
                                    console.warn('AndroidBridge not available, using fallback coordinates');
                                    this.latitude = -6.2088;
                                    this.longitude = 106.8456;
                                    this.initMap();
                                }
                            }
                        }" x-init="$watch('showServices', (value) => { if (!value && !mapInitialized) getLocation(); })">
                            <!-- Daftar Tombol Layanan -->
                            <div x-show="showServices" x-transition>
                                <div class="mb-4">
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text border-end-0">
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

                                <div class="mb-4" x-show="search === ''">
                                    <div class="d-flex flex-column align-items-center mb-2">
                                        <span class="mb-2">Pilih Unit:</span>
                                        <div class="unit-pills text-center">
                                            @php
                                                $units = $services->pluck('unit_id', 'unit.unit_name')->toArray();
                                            @endphp
                                            @foreach ($units as $unitName => $unitId)
                                                <button @click="scrollToUnit({{ $unitId }})"
                                                    class="btn btn-sm btn-outline-primary rounded-pill me-2 mb-2">
                                                    {{ $unitName }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="scrolling-units">
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

                                    <div x-show="search === ''">
                                        @foreach ($units as $unitName => $unitId)
                                            <div id="unit-{{ $unitId }}" class="unit-section mb-4">
                                                <div class="unit-header">
                                                    <h6 class="mb-3 pb-2 border-bottom d-flex align-items-center">
                                                        <i class="bi bi-building me-2 text-primary"></i>
                                                        <span>{{ $unitName }}</span>
                                                        <div class="ms-auto">
                                                            <span class="badge bg-primary p-2">
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
                                    <div class="form-group mb-3">
                                        <label class="form-label text-dark" for="title">Judul Aduan</label>
                                        <input type="text" class="form-control form-control-clicked" name="title"
                                            id="title" required>
                                        @error('title')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label text-dark" for="description">Deskripsi Aduan</label>
                                        <textarea class="form-control form-control-clicked" id="description" name="description" cols="3"
                                            rows="5" required>{{ old('description') }}</textarea>
                                        @error('description')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label text-dark" for="location">Lokasi Aduan (Opsional)</label>
                                        <div class="mb-2">
                                            <button type="button" @click="getLocation"
                                                class="btn btn-sm btn-primary mb-2">
                                                <i class="fas fa-map-marker-alt me-1"></i> Dapatkan Lokasi
                                            </button>
                                            <div id="map"
                                                style="height: 200px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: none;">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <label class="form-label" for="latitude">Latitude</label>
                                                <input type="text" class="form-control form-control-clicked"
                                                    id="latitude" name="latitude" x-model="latitude" readonly>
                                                @error('latitude')
                                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label" for="longitude">Longitude</label>
                                                <input type="text" class="form-control form-control-clicked"
                                                    id="longitude" name="longitude" x-model="longitude" readonly>
                                                @error('longitude')
                                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-text mt-2">
                                            <i class="bi bi-info-circle me-1"></i> Klik tombol untuk mendeteksi lokasi
                                            menggunakan GPS. Anda juga dapat menyeret pin pada peta untuk menyesuaikan
                                            lokasi.
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label text-dark" for="imageBase64Input">Unggah Gambar Pendukung
                                            (Opsional)</label>
                                        <input type="hidden" name="images[]" id="imageBase64Input">
                                        <img class="mb-1 img-thumbnail" id="imagePreview" src=""
                                            alt="No image yet" style="display:none; max-width:30%;">
                                        <div>
                                            <button type="button" id="openCameraBtn"
                                                class="btn btn-sm btn-primary me-2"><i class="fa fa-camera me-1"></i>Buka
                                                Kamera</button>
                                            <button type="button" id="openGalleryBtn"
                                                class="btn btn-sm btn-primary me-2"><i class="fa fa-image me-1"></i>Pilih
                                                dari Galeri</button>
                                            <input type="file" id="fileFallback" accept="image/*" hidden>
                                        </div>
                                        <p id="errorMessage" class="text-red-500 text-sm mt-1"></p>
                                        <div class="form-text">
                                            <i class="bi bi-info-circle me-1"></i> Format yang didukung: JPG, PNG, GIF
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
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
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

        #map {
            transition: all 0.3s ease;
        }
    </style>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.0/dist/cdn.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <script src="{{ asset('mobile/js/android-bridge.js') }}"></script>
    <script>
        $(document).ready(function() {
            let formDataToSubmit = null;

            $('#ticketForm').on('submit', function(e) {
                e.preventDefault();
                formDataToSubmit = new FormData(this);
                console.log('Form data prepared:', Array.from(formDataToSubmit.entries()));
                $('#confirmModal').modal('show');
            });

            $('#confirmYesBtn').on('click', function() {
                if (!formDataToSubmit) {
                    console.warn('No form data to submit');
                    $('#errorMessage').text('Error: No form data available');
                    $('#confirmModal').modal('hide');
                    return;
                }

                var base64Image = $('#imageBase64Input').val();
                if (base64Image) {
                    try {
                        var byteString = atob(base64Image.split(',')[1]);
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

            $('#confirmNoBtn').on('click', function() {
                formDataToSubmit = null;
                $('#confirmModal').modal('hide');
            });

            $('#openCameraBtn').on('click', function() {
                console.log('Opening camera');
                if (typeof AndroidBridge !== 'undefined') {
                    AndroidBridge.openCamera('showImagePreview');
                } else {
                    $('#fileFallback').click();
                }
            });

            $('#openGalleryBtn').on('click', function() {
                console.log('Opening photo picker');
                if (typeof AndroidBridge !== 'undefined') {
                    AndroidBridge.openPhotoPicker('showImagePreview');
                } else {
                    $('#fileFallback').click();
                }
            });

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

            window.setLocation = function(latitude, longitude) {
                console.log('Received location:', latitude, longitude);
                Alpine.store('form', {
                    latitude: latitude,
                    longitude: longitude
                });
                const mapElement = document.getElementById('map');
                if (mapElement) {
                    mapElement.style.display = 'block';
                }
                const instance = Alpine.$data(document.querySelector('[x-data]'));
                instance.latitude = latitude;
                instance.longitude = longitude;
                if (!instance.mapInitialized) {
                    instance.initMap();
                } else {
                    instance.map.setView([latitude, longitude], 13);
                    instance.marker.setLatLng([latitude, longitude]);
                }
            };
        });
    </script>
@endsection
