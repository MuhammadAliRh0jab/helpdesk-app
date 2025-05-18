@extends('layouts.app')

@section('title', 'Buat Aduan Baru')

@section('content')
    <div id="page-container"
        class="sidebar-o sidebar-dark enable-page-overlay side-scroll page-header-fixed main-content-narrow">
        <main id="main-container">
            <div class="content">
                <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
                    <div class="flex-grow-1">
                        <h1 class="h3 fw-bold mb-1">Buat Aduan</h1>
                        <h2 class="fs-base lh-base fw-medium text-muted mb-0">
                            Pilih layanan atau isi detail aduan Anda
                        </h2>
                    </div>
                    <nav class="flex-shrink-0 mt-3 mt-sm-0 ms-sm-3" aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-alt">
                            <li class="breadcrumb-item">
                                <a class="link-fx" href="{{ route('tickets.index') }}">Aduan</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">Buat</li>
                        </ol>
                    </nav>
                </div>
            </div>

<<<<<<< HEAD
            <div class="card mt-4 col-lg-10 mx-auto shadow-sm border-0 rounded-3">
                <div class="card-header bg-primary text-white rounded-top">
                    <h3 class="card-title m-0 fs-5">Form Aduan</h3>
                </div>
                <div class="card-body p-4">
                    <div x-data="{
                        showServices: true,
                        selectedService: null,
                        selectedUnit: null,
                        search: '',
                        fileNames: 'Tidak ada file dipilih',
                        scrollToUnit(unitId) {
                            const unitElement = document.getElementById('unit-' + unitId);
                            if (unitElement) {
                                unitElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            }
                        },
                        updateFileNames(event) {
                            const files = event.target.files;
                            this.fileNames = files.length > 0 ?
                                files.length > 1 ?
                                `${files.length} file dipilih` :
                                files[0].name :
                                'Tidak ada file dipilih';
                        }
                    }">
                        <!-- Daftar Tombol Layanan -->
                        <div x-show="showServices" x-transition>
                            <div class="mb-4">
                                <div class="input-group input-group-lg shadow-sm">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fas fa-search text-primary"></i>
                                    </span>
                                    <input x-model="search" type="text" class="form-control border-start-0"
                                        placeholder="Cari layanan...">
                                </div>
                                <div class="form-text" x-show="search !== ''">
                                    <i class="fas fa-info-circle me-1"></i> Pencarian akan menampilkan hasil dari semua unit
=======
        <div class="card mt-4 col-lg-10 mx-auto shadow-sm border-0 rounded-3">
            <div class="card-header bg-primary text-white rounded-top">
                <h3 class="card-title m-0 fs-5">Form Aduan</h3>
            </div>
            <div class="card-body p-4">
                <div x-data="{
                    showServices: true,
                    selectedService: null,
                    selectedUnit: null,
                    search: '',
                    fileNames: 'Tidak ada file dipilih',
                    latitude: null,
                    longitude: null,
                    mapInitialized: false,

                    scrollToUnit(unitId) {
                        const unitElement = document.getElementById('unit-' + unitId);
                        if (unitElement) {
                            unitElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    },

                    updateFileNames(event) {
                        const files = event.target.files;
                        this.fileNames = files.length > 0 
                            ? (files.length > 1 ? `${files.length} file dipilih` : files[0].name) 
                            : 'Tidak ada file dipilih';
                        console.log('File names updated:', this.fileNames);
                    },

                    initMap() {
                        console.log('initMap called');
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(
                                (position) => {
                                    this.latitude = position.coords.latitude;
                                    this.longitude = position.coords.longitude;
                                    console.log('Geolocation found:', this.latitude, this.longitude);
                                    this.setupMap();
                                },
                                (error) => {
                                    console.error('Geolocation error:', error);
                                    this.latitude = -6.2088;
                                    this.longitude = 106.8456;
                                    console.log('Falling back to Jakarta coordinates:', this.latitude, this.longitude);
                                    this.setupMap();
                                }
                            );
                        } else {
                            console.log('Geolocation not supported');
                            this.latitude = -6.2088;
                            this.longitude = 106.8456;
                            this.setupMap();
                        }
                    },

                    setupMap() {
                        console.log('setupMap called', this.latitude, this.longitude);
                        if (this.latitude === null || this.longitude === null) {
                            console.error('Coordinates not available');
                            return;
                        }

                        const mapElement = document.getElementById('map');
                        if (!mapElement) {
                            console.error('Map element not found');
                            return;
                        }

                        const map = L.map(mapElement).setView([this.latitude, this.longitude], 13);

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© <a href=\'https://www.openstreetmap.org/copyright\'>OpenStreetMap</a> contributors'
                        }).addTo(map);

                        const marker = L.marker([this.latitude, this.longitude], { draggable: true }).addTo(map);

                        marker.on('dragend', (e) => {
                            const position = e.target.getLatLng();
                            this.latitude = position.lat;
                            this.longitude = position.lng;
                            console.log('Marker moved to:', this.latitude, this.longitude);
                        });

                        console.log('Map initialized successfully');
                        this.mapInitialized = true;
                    }
                }" x-init="$watch('showServices', (value) => { if (!value && !mapInitialized) initMap(); })">
                    <!-- Daftar Tombol Layanan -->
                    <div x-show="showServices" x-transition>
                        <div class="mb-4">
                            <div class="input-group input-group-lg shadow-sm">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="fas fa-search text-primary"></i>
                                </span>
                                <input x-model="search" type="text" class="form-control border-start-0" placeholder="Cari layanan...">
                            </div>
                            <div class="form-text" x-show="search !== ''">
                                <i class="fas fa-info-circle me-1"></i> Pencarian akan menampilkan hasil dari semua unit
                            </div>
                        </div>

                        <!-- Quick Jump Menu -->
                        <div class="mb-4" x-show="search === ''">
                            <div class="d-flex align-items-center mb-2">
                                <span class="text-muted me-2"><i class="fas fa-filter me-1"></i> Pilih Unit:</span>
                                <div class="unit-pills">
                                    @php
                                        $units = $services->pluck('unit_id', 'unit.unit_name')->toArray();
                                    @endphp
                                    @foreach($units as $unitName => $unitId)
                                        <button @click="scrollToUnit({{ $unitId }})"
                                                class="btn btn-sm btn-outline-primary rounded-pill me-2 mb-2">
                                            {{ $unitName }}
                                        </button>
                                    @endforeach
>>>>>>> cb8352ec6656dbd66c980ab6516350d4a7520d32
                                </div>
                            </div>

<<<<<<< HEAD
                            <!-- Quick Jump Menu -->
                            <div class="mb-4" x-show="search === ''">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="text-muted me-2"><i class="fas fa-filter me-1"></i> Pilih Unit:</span>
                                    <div class="unit-pills">
                                        @php
                                            // Menggunakan collection untuk mendapatkan unit unik
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
=======
                        <!-- Daftar Unit dengan Layanan -->
                        <div class="scrolling-units">
                            <div x-show="search !== ''" class="mb-4">
                                <div class="alert alert-info d-flex align-items-center">
                                    <i class="fas fa-search me-2"></i>
                                    <div>Menampilkan hasil pencarian untuk: <strong x-text="search"></strong></div>
                                </div>
                                <div class="row row-cols-1 row-cols-md-3 g-3">
                                    @foreach ($services as $service)
                                        <div class="col service-item"
                                             x-show="'{{ $service->svc_name }}'.toLowerCase().includes(search.toLowerCase())">
                                            <div class="card h-100 border-0 shadow-sm hover-shadow transition-300">
                                                <div class="card-body p-4">
                                                    <div class="unit-badge mb-2">{{ $service->unit->unit_name }}</div>
                                                    <button
                                                        @click="showServices = false; selectedService = {{ $service->id }}; selectedUnit = {{ $service->unit_id }}"
                                                        class="btn btn-link text-decoration-none p-0 w-100 text-start">
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-shrink-0 me-3">
                                                                <div class="bg-light text-primary rounded-circle p-3">
                                                                    <i class="fas fa-concierge-bell fa-fw"></i>
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h5 class="mb-1">{{ $service->svc_name }}</h5>
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
                            </div>
                            <div x-show="search === ''">
                                @php
                                    $units = $services->pluck('unit_id', 'unit.unit_name')->toArray();
                                @endphp
                                @foreach($units as $unitName => $unitId)
                                    <div id="unit-{{ $unitId }}" class="unit-section mb-4">
                                        <div class="unit-header">
                                            <h4 class="mb-3 pb-2 border-bottom d-flex align-items-center">
                                                <i class="fas fa-building me-2 text-primary"></i>
                                                <span>{{ $unitName }}</span>
                                                <div class="ms-auto">
                                                    <span class="badge bg-primary rounded-pill">
                                                        {{ $services->where('unit_id', $unitId)->count() }} Layanan
                                                    </span>
                                                </div>
                                            </h4>
                                        </div>
                                        <div class="row row-cols-1 row-cols-md-3 g-3">
                                            @foreach ($services->where('unit_id', $unitId) as $service)
                                                <div class="col service-item">
                                                    <div class="card h-100 border-0 shadow-sm hover-shadow transition-300">
                                                        <div class="card-body p-4">
                                                            <button
                                                                @click="showServices = false; selectedService = {{ $service->id }}; selectedUnit = {{ $service->unit_id }}"
                                                                class="btn btn-link text-decoration-none p-0 w-100 text-start">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="flex-shrink-0 me-3">
                                                                        <div class="bg-light text-primary rounded-circle p-3">
                                                                            <i class="fas fa-concierge-bell fa-fw"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <h5 class="mb-1">{{ $service->svc_name }}</h5>
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
                                    </div>
                                @endforeach
>>>>>>> cb8352ec6656dbd66c980ab6516350d4a7520d32
                            </div>

<<<<<<< HEAD
                            <!-- Daftar Unit dengan Layanan -->
                            <div class="scrolling-units">
                                <!-- Jika ada pencarian, tampilkan semua layanan yang cocok -->
                                <div x-show="search !== ''" class="mb-4">
                                    <div class="alert alert-info d-flex align-items-center">
                                        <i class="fas fa-search me-2"></i>
                                        <div>Menampilkan hasil pencarian untuk: <strong x-text="search"></strong></div>
                                    </div>

                                    <div class="row row-cols-1 row-cols-md-3 g-3">
                                        @foreach ($services as $service)
                                            <div class="col service-item"
                                                x-show="'{{ $service->svc_name }}'.toLowerCase().includes(search.toLowerCase())">
                                                <div class="card h-100 border-0 shadow-sm hover-shadow transition-300">
                                                    <div class="card-body p-4">
                                                        <div class="unit-badge mb-2">{{ $service->unit->unit_name }}</div>
                                                        <button
                                                            @click="showServices = false; selectedService = {{ $service->id }}; selectedUnit = {{ $service->unit_id }}"
                                                            class="btn btn-link text-decoration-none p-0 w-100 text-start">
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-shrink-0 me-3">
                                                                    <div class="bg-light text-primary rounded-circle p-3">
                                                                        <i class="fas fa-concierge-bell fa-fw"></i>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <h5 class="mb-1">{{ $service->svc_name }}</h5>
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
=======
                    <!-- Form Detail Aduan -->
                    <div x-show="!showServices" x-transition class="animate__animated animate__fadeIn">
                        <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="unit_id" x-bind:value="selectedUnit">
                            <input type="hidden" name="service_id" x-bind:value="selectedService">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-4">
                                        <label for="title" class="form-label fw-bold">Judul Aduan</label>
                                        <input type="text" class="form-control form-control-lg shadow-sm" id="title"
                                               name="title" placeholder="Masukkan Judul Aduan" value="{{ old('title') }}"
                                               required>
                                        @error('title')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label for="description" class="form-label fw-bold">Deskripsi Aduan</label>
                                        <textarea class="form-control shadow-sm" id="description" name="description"
                                                  rows="6" placeholder="Masukkan Deskripsi Aduan"
                                                  required>{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label for="location" class="form-label fw-bold">Lokasi Aduan (opsional)</label>
                                        <div class="mb-3">
                                            <div id="map"
                                                 style="height: 300px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="latitude" class="form-label">Latitude</label>
                                                <input type="text" class="form-control shadow-sm" id="latitude"
                                                       name="latitude" x-model="latitude" placeholder="Latitude" readonly>
                                                @error('latitude')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="longitude" class="form-label">Longitude</label>
                                                <input type="text" class="form-control shadow-sm" id="longitude"
                                                       name="longitude" x-model="longitude" placeholder="Longitude" readonly>
                                                @error('longitude')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-text mt-2">
                                            <i class="fas fa-info-circle me-1"></i> Lokasi akan otomatis terdeteksi
                                            menggunakan GPS. Anda juga dapat menyeret pin pada peta untuk menyesuaikan
                                            lokasi.
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="images" class="form-label fw-bold">Lampirkan Gambar (opsional)</label>
                                        <div class="dropzone-container p-4 text-center border border-dashed rounded-3 bg-light"
                                             @click="$refs.fileInput.click()">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fas fa-cloud-upload-alt text-primary fa-3x mb-3"></i>
                                                <p class="mb-2">Klik atau seret file ke sini</p>
                                                <p class="text-muted small mb-0" x-text="fileNames">Tidak ada file dipilih</p>
                                            </div>
                                            <input type="file" name="images[]" id="images" multiple class="d-none"
                                                   x-ref="fileInput" @change="updateFileNames">
                                        </div>
                                        @error('images')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">
                                            <i class="fas fa-info-circle me-1"></i> Format yang didukung: JPG, PNG, GIF
                                            (maks 2MB per file)
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between gap-3 mt-4">
                                        <button type="button"
                                                @click="showServices = true; selectedService = null; selectedUnit = null"
                                                class="btn btn-outline-secondary btn-lg px-4">
                                            <i class="fas fa-arrow-left me-2"></i>Kembali
                                        </button>
                                        <button type="submit" class="btn btn-primary btn-lg px-5">
                                            <i class="fas fa-paper-plane me-2"></i>Kirim Aduan
                                        </button>
>>>>>>> cb8352ec6656dbd66c980ab6516350d4a7520d32
                                    </div>
                                </div>
                                <!-- Tampilkan layanan dikelompokkan per unit saat tidak ada pencarian -->
                                <div x-show="search === ''">
                                    @php
                                        // Menggunakan collection untuk mendapatkan unit unik
                                        $units = $services->pluck('unit_id', 'unit.unit_name')->toArray();
                                    @endphp

                                    @foreach ($units as $unitName => $unitId)
                                        <div id="unit-{{ $unitId }}" class="unit-section mb-4">
                                            <div class="unit-header">
                                                <h4 class="mb-3 pb-2 border-bottom d-flex align-items-center">
                                                    <i class="fas fa-building me-2 text-primary"></i>
                                                    <span>{{ $unitName }}</span>
                                                    <div class="ms-auto">
                                                        <span class="badge bg-primary rounded-pill">
                                                            {{ $services->where('unit_id', $unitId)->count() }} Layanan
                                                        </span>
                                                    </div>
                                                </h4>
                                            </div>

                                            <div class="row row-cols-1 row-cols-md-3 g-3">
                                                @foreach ($services->where('unit_id', $unitId) as $service)
                                                    <div class="col service-item">
                                                        <div
                                                            class="card h-100 border-0 shadow-sm hover-shadow transition-300">
                                                            <div class="card-body p-4">
                                                                <button
                                                                    @click="showServices = false; selectedService = {{ $service->id }}; selectedUnit = {{ $service->unit_id }}"
                                                                    class="btn btn-link text-decoration-none p-0 w-100 text-start">
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="flex-shrink-0 me-3">
                                                                            <div
                                                                                class="bg-light text-primary rounded-circle p-3">
                                                                                <i class="fas fa-concierge-bell fa-fw"></i>
                                                                            </div>
                                                                        </div>
                                                                        <div class="flex-grow-1">
                                                                            <h5 class="mb-1">{{ $service->svc_name }}
                                                                            </h5>
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
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Form Detail Aduan -->
                        <div x-show="!showServices" x-transition class="animate__animated animate__fadeIn">
                            <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="unit_id" x-bind:value="selectedUnit">
                                <input type="hidden" name="service_id" x-bind:value="selectedService">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="mb-4">
                                            <label for="title" class="form-label fw-bold">Judul Aduan</label>
                                            <input type="text" class="form-control form-control-lg shadow-sm"
                                                id="title" name="title" placeholder="Masukkan Judul Aduan"
                                                value="{{ old('title') }}" required>
                                            @error('title')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-4">
                                            <label for="description" class="form-label fw-bold">Deskripsi Aduan</label>
                                            <textarea class="form-control shadow-sm" id="description" name="description" rows="6"
                                                placeholder="Masukkan Deskripsi Aduan" required>{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-4">
                                            <label for="images" class="form-label fw-bold">Lampirkan Gambar
                                                (opsional)</label>
                                            <div class="dropzone-container p-4 text-center border border-dashed rounded-3 bg-light"
                                                @click="$refs.fileInput.click()">
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="fas fa-cloud-upload-alt text-primary fa-3x mb-3"></i>
                                                    <p class="mb-2">Klik atau seret file ke sini</p>
                                                    <p class="text-muted small mb-0" x-text="fileNames">Tidak ada file
                                                        dipilih</p>
                                                </div>
                                                <input type="file" name="images[]" id="images" multiple
                                                    class="d-none" x-ref="fileInput" @change="updateFileNames">
                                            </div>
                                            @error('images')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">
                                                <i class="fas fa-info-circle me-1"></i> Format yang didukung: JPG, PNG, GIF
                                                (maks 2MB per file)
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between gap-3 mt-4">
                                            <button type="button"
                                                @click="showServices = true; selectedService = null; selectedUnit = null"
                                                class="btn btn-outline-secondary btn-lg px-4">
                                                <i class="fas fa-arrow-left me-2"></i>Kembali
                                            </button>
                                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                                <i class="fas fa-paper-plane me-2"></i>Kirim Aduan
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection

<<<<<<< HEAD
@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        .hover-shadow {
            transition: all 0.3s ease;
        }

        .hover-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }

        .transition-300 {
            transition: all 0.3s ease;
        }

        .dropzone-container {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .dropzone-container:hover {
            background-color: #f8f9fa;
            border-color: var(--bs-primary) !important;
        }

        .nav-pills .nav-link.active {
            background-color: var(--bs-primary);
        }

        .nav-pills .nav-link {
            color: var(--bs-dark);
            font-weight: 500;
            border-radius: 0.5rem;
            padding: 0.75rem 1.5rem;
        }

        .service-item .card {
            transition: all 0.2s ease;
        }

        .service-item .card:hover {
            border-color: var(--bs-primary);
        }

        .unit-section {
            padding: 15px;
            border-radius: 8px;
            background-color: #fff;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.03);
        }

        .unit-header {
            position: sticky;
            top: -1px;
            background-color: #fff;
            z-index: 10;
            padding-top: 10px;
            padding-bottom: 10px;
            border-radius: 8px 8px 0 0;
        }

        .unit-badge {
            display: inline-block;
            padding: 4px 10px;
            background-color: #e9ecef;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            color: #495057;
            margin-bottom: 8px;
        }

        .unit-pills {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
        }

        .scrolling-units {
            max-height: 100%;
            overflow-y: auto;
            padding-right: 5px;
        }

        /* Custom scrollbar */
        .scrolling-units::-webkit-scrollbar {
            width: 8px;
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
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.0/dist/cdn.min.js" defer></script>
@endsection
=======
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            console.log('DOM content loaded');
            console.log('Alpine available:', typeof Alpine !== 'undefined');
            console.log('Leaflet available:', typeof L !== 'undefined');
        });
    </script>
@endsection

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        .hover-shadow {
            transition: all 0.3s ease;
        }
        .hover-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }
        .transition-300 {
            transition: all 0.3s ease;
        }
        .dropzone-container {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .dropzone-container:hover {
            background-color: #f8f9fa;
            border-color: var(--bs-primary) !important;
        }
        .nav-pills .nav-link.active {
            background-color: var(--bs-primary);
        }
        .nav-pills .nav-link {
            color: var(--bs-dark);
            font-weight: 500;
            border-radius: 0.5rem;
            padding: 0.75rem 1.5rem;
        }
        .service-item .card {
            transition: all 0.2s ease;
        }
        .service-item .card:hover {
            border-color: var(--bs-primary);
        }
        .unit-section {
            padding: 15px;
            border-radius: 8px;
            background-color: #fff;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.03);
        }
        .unit-header {
            position: sticky;
            top: -1px;
            background-color: #fff;
            z-index: 10;
            padding-top: 10px;
            padding-bottom: 10px;
            border-radius: 8px 8px 0 0;
        }
        .unit-badge {
            display: inline-block;
            padding: 4px 10px;
            background-color: #e9ecef;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            color: #495057;
            margin-bottom: 8px;
        }
        .unit-pills {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
        }
        .scrolling-units {
            max-height: 100%;
            overflow-y: auto;
            padding-right: 5px;
        }
        .scrolling-units::-webkit-scrollbar {
            width: 8px;
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
>>>>>>> cb8352ec6656dbd66c980ab6516350d4a7520d32
