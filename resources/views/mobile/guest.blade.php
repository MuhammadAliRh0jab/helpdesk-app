@extends('mobile.master.app')

@section('title', 'Lapor Sebagai Tamu')

@section('content')
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
                    scrollWheelZoom: false
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
                this.map.on('click', (e) => {
                    const { lat, lng } = e.latlng;
                    this.latitude = lat;
                    this.longitude = lng;
                    this.marker.setLatLng([lat, lng]);
                    console.log('Marker moved to:', lat, lng);
                });
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
                this.latitude = -8.0983; // Blitar coordinates
                this.longitude = 112.1651;
                this.initMap();
            }
        }
    }" x-init="$watch('showServices', (value) => { if (!value && !mapInitialized) getLocation(); })">
        <div class="hero-block-wrapper bg-primary">
            <div class="container py-4">
                <h2 class="fw-bold text-center text-white mb-3">Lapor Tanpa Login</h2>
                <p class="text-center text-white mb-4">Laporkan masalah Anda tanpa perlu login untuk layanan publik tertentu.
                </p>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show p-3 mb-3 rounded" role="alert">
                        <strong><i class="fas fa-check-circle me-2"></i>Berhasil!</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show p-3 mb-3 rounded" role="alert">
                        <strong><i class="fas fa-exclamation-circle me-2"></i>Error!</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show p-3 mb-3 rounded" role="alert">
                        <strong><i class="fas fa-exclamation-triangle me-2"></i>Perhatian!</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card shadow border-0 rounded">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="card-title m-0">Laporan Tanpa Login</h5>
                        </div>
                        <hr>
                        <!-- Daftar Tombol Layanan -->
                        <div x-show="showServices" x-transition>
                            <div class="mb-3">
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fas fa-search text-primary"></i>
                                    </span>
                                    <input x-model="search" type="text" class="form-control border-start-0"
                                        placeholder="Cari layanan..." aria-label="Cari layanan">
                                </div>
                                <div class="form-text" x-show="search !== ''">
                                    <i class="fas fa-info-circle me-1"></i> Pencarian akan menampilkan hasil dari semua unit
                                </div>
                            </div>

                            <!-- Quick Jump Menu -->
                            <div class="mb-3" x-show="search === ''">
                                <div class="d-flex flex-column align-items-center mb-2">
                                    <span class="mb-2">Pilih Unit:</span>
                                    <div class="unit-pills text-center">
                                        @php
                                            $units = $services->pluck('unit_id', 'unit.unit_name')->toArray();
                                        @endphp
                                        @foreach ($units as $unitName => $unitId)
                                            <button @click="scrollToUnit({{ $unitId }})"
                                                class="btn btn-sm btn-outline-primary rounded-pill m-1"
                                                aria-label="Pilih unit {{ $unitName }}">
                                                {{ $unitName }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Daftar Unit dengan Layanan -->
                            <div class="scrolling-units" style="max-height: 300px; overflow-y: auto;">
                                <!-- Jika ada pencarian, tampilkan semua layanan yang cocok -->
                                <div x-show="search !== ''" class="mb-3">
                                    <div class="alert alert-info d-flex align-items-center">
                                        <i class="fas fa-search me-2"></i>
                                        <div>Menampilkan hasil pencarian untuk: <strong x-text="search"></strong></div>
                                    </div>
                                    @foreach ($services as $service)
                                        <div class="service-item mb-2"
                                            x-show="'{{ $service->svc_name }}'.toLowerCase().includes(search.toLowerCase())">
                                            <div class="card border-0 shadow-sm hover-shadow transition-300">
                                                <div class="card-body p-3">
                                                    <div class="unit-badge mb-2">{{ $service->unit->unit_name }}</div>
                                                    <button
                                                        @click="showServices = false; selectedService = {{ $service->id }}; selectedUnit = {{ $service->unit_id }}"
                                                        class="btn btn-link text-decoration-none p-0 w-100 text-start"
                                                        aria-label="Pilih layanan {{ $service->svc_name }} dari unit {{ $service->unit->unit_name }}">
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-shrink-0 me-3">
                                                                <div class="bg-light text-primary rounded-circle p-2">
                                                                    <i class="fas fa-concierge-bell fa-fw"></i>
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h6 class="m-0">{{ $service->svc_name }}</h6>
                                                            </div>
                                                            <div class="flex-shrink-0 ms-2">
                                                                <i class="fas fa-chevron-right text-muted"></i>
                                                            </div>
                                                        </div>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Tampilkan layanan dikelompokkan per unit saat tidak ada pencarian -->
                                <div x-show="search === ''">
                                    @foreach ($units as $unitName => $unitId)
                                        <div id="unit-{{ $unitId }}" class="unit-section mb-3">
                                            <div class="unit-header">
                                                <h6 class="mb-3 pb-2 border-bottom d-flex align-items-center">
                                                    <i class="fas fa-building me-2 text-primary"></i>
                                                    <span>{{ $unitName }}</span>
                                                    <div class="ms-auto">
                                                        <span class="badge bg-primary p-2">
                                                            {{ $services->where('unit_id', $unitId)->count() }} Layanan
                                                        </span>
                                                    </div>
                                                </h6>
                                            </div>
                                            @foreach ($services->where('unit_id', $unitId) as $service)
                                                <div class="service-item mb-2">
                                                    <div class="card border-0 shadow-sm hover-shadow transition-300">
                                                        <div class="card-body p-3">
                                                            <button
                                                                @click="showServices = false; selectedService = {{ $service->id }}; selectedUnit = {{ $service->unit_id }}"
                                                                class="btn btn-link text-decoration-none p-0 w-100 text-start"
                                                                aria-label="Pilih layanan {{ $service->svc_name }} dari unit {{ $service->unit->unit_name }}">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="flex-shrink-0 me-3">
                                                                        <div
                                                                            class="bg-light text-primary rounded-circle p-2">
                                                                            <i class="fas fa-concierge-bell fa-fw"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <h6 class="mb-1">{{ $service->svc_name }}</h6>
                                                                        <div class="text-muted small">
                                                                            {{ $service->unit->unit_name }}</div>
                                                                    </div>
                                                                    <div class="flex-shrink-0 ms-2">
                                                                        <i class="fas fa-chevron-right text-muted"></i>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Form Detail Aduan -->
                        <div x-show="!showServices" x-transition class="animate__animated animate__fadeIn">
                            <form id="ticketForm" action="{{ route('tickets.store.guest') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="unit_id" x-bind:value="selectedUnit">
                                <input type="hidden" name="service_id" x-bind:value="selectedService">
                                <div class="mb-3">
                                    <label for="guest_name" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control form-control-clicked" id="guest_name"
                                        name="guest_name" placeholder="Masukkan nama lengkap"
                                        value="{{ old('guest_name') }}" required>
                                    @error('guest_name')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="guest_email" class="form-label">Email</label>
                                    <input type="email" class="form-control form-control-clicked" id="guest_email"
                                        name="guest_email" placeholder="Masukkan email" value="{{ old('guest_email') }}"
                                        required>
                                    @error('guest_email')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="title" class="form-label">Judul Aduan</label>
                                    <input type="text" class="form-control form-control-clicked" id="title"
                                        name="title" placeholder="Masukkan judul aduan" value="{{ old('title') }}"
                                        required>
                                    @error('title')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Deskripsi</label>
                                    <textarea class="form-control form-control-clicked" id="description" name="description"
                                        placeholder="Deskripsikan masalah Anda secara detail" style="height: 120px" required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="location" class="form-label">Lokasi Aduan (Opsional)</label>
                                    <div class="mb-2">
                                        <button type="button" @click="getLocation" class="btn btn-sm btn-primary mb-2">
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
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label" for="longitude">Longitude</label>
                                            <input type="text" class="form-control form-control-clicked"
                                                id="longitude" name="longitude" x-model="longitude" readonly>
                                            @error('longitude')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-text mt-2">
                                        <i class="fas fa-info-circle me-1"></i> Klik tombol untuk mendeteksi lokasi
                                        menggunakan GPS. Anda juga dapat menyeret pin pada peta untuk menyesuaikan lokasi.
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="imageBase64Input" class="form-label fw-bold">Unggah Gambar Pendukung
                                        (Opsional)</label>
                                    <input type="hidden" name="images[]" id="imageBase64Input">
                                    <img class="mb-1 img-thumbnail" id="imagePreview" src="" alt="No image yet"
                                        style="display:none; max-width:30%;">
                                    <div>
                                        <button type="button" id="openCameraBtn"
                                            class="btn btn-sm btn-primary me-2">Pilih Gambar (Kamera)</button>
                                        <button type="button" id="openGalleryBtn"
                                            class="btn btn-sm btn-primary me-2">Pilih dari Galeri</button>
                                        <input type="file" id="fileFallback" accept="image/*" hidden>
                                    </div>
                                    <p id="errorMessage" class="text-danger small mt-1"></p>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i> Format yang didukung: JPG, PNG, GIF
                                        (maks 2MB per file)
                                    </div>
                                </div>
                                <hr>
                                <div class="d-flex flex-column gap-2">
                                    <button type="button"
                                        @click="showServices = true; selectedService = null; selectedUnit = null"
                                        class="btn btn-warning" aria-label="Kembali ke daftar layanan">
                                        <i class="fas fa-arrow-left me-2"></i>Kembali
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-2"></i>Kirim Laporan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-footer bg-light text-center py-3">
                        <p class="text-muted mb-0 small">
                            <i class="fas fa-info-circle me-2"></i>Laporan Anda akan diverifikasi oleh tim kami sebelum
                            diproses lebih lanjut
                        </p>
                    </div>
                </div>
                <a href="{{ route('landing') }}" class="btn btn-warning w-100 mt-3">
                    <i class="bi bi-arrow-left me-2"></i>Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.0/dist/cdn.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <script src="{{ asset('mobile/js/android-bridge.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Handle form submission via AJAX
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
                        var byteString = atob(base64Image.split(',')[1]);
                        var arrayBuffer = new ArrayBuffer(byteString.length);
                        var uint8Array = new Uint8Array(arrayBuffer);
                        for (var i = 0; i < byteString.length; i++) {
                            uint8Array[i] = byteString.charCodeAt(i);
                        }
                        var blob = new Blob([uint8Array], {
                            type: 'image/jpeg'
                        });
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
                        // Show success alert and redirect to landing page
                        $('.alert-success').removeClass('d-none').find('strong').text(
                            'Berhasil! Laporan Anda telah dikirim.');
                        setTimeout(() => {
                            window.location.href =
                                '{{ route('landing') }}'; // Redirect to landing page
                        }, 2000);
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

            // Handle camera button
            $('#openCameraBtn').on('click', function() {
                console.log('Opening camera');
                if (typeof AndroidBridge !== 'undefined') {
                    AndroidBridge.openCamera('showImagePreview');
                } else {
                    $('#fileFallback').click();
                }
            });

            // Handle gallery button
            $('#openGalleryBtn').on('click', function() {
                console.log('Opening photo picker');
                if (typeof AndroidBridge !== 'undefined') {
                    AndroidBridge.openPhotoPicker('showImagePreview');
                } else {
                    $('#fileFallback').click();
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

            // Function to set location from AndroidBridge
            window.setLocation = function(latitude, longitude) {
                console.log('Received location:', latitude, longitude);
                const mapElement = document.getElementById('map');
                if (mapElement) {
                    mapElement.style.display = 'block';
                }
                const instance = Alpine.$data(document.querySelector('[x-data]'));
                instance.latitude = parseFloat(latitude);
                instance.longitude = parseFloat(longitude);
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
