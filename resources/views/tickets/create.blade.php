@extends('layouts.app')

@section('title', 'Buat Aduan Baru')

@section('content')
<div id="page-container" class="sidebar-o sidebar-dark enable-page-overlay side-scroll page-header-fixed main-content-narrow">
    <main id="main-container">
        <div class="content">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
                <div class="flex-grow-1">
                    <h1 class="h3 fw-bold mb-1">Buat Laporan</h1>
                    <h2 class="fs-base lh-base fw-medium text-muted mb-0">
                        Pilih layanan atau isi detail Laporan Anda
                    </h2>
                </div>
                <nav class="flex-shrink-0 mt-3 mt-sm-0 ms-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">
                            <a class="link-fx" href="{{ route('tickets.index') }}">Laporan</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">Buat</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card mt-4 col-lg-10 mx-auto shadow-sm border-0 rounded-3 p-3">
            <div class="card-header bg-primary text-white rounded-top">
                <h3 class="card-title m-0 fs-5" id="form-title">
                    <i class="fas fa-edit me-2"></i>Form Laporan {{ isset($service) ? $service->svc_name : '' }}
                </h3>
            </div>
            <div class="card-body p-4">
                <div class="service-list {{ isset($service) ? 'hidden' : '' }}" id="service-list">
                    <div class="mb-4">
                        <div class="input-group input-group-lg shadow-sm">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-search text-primary"></i>
                            </span>
                            <input type="text" id="search" class="form-control border-start-0"
                                   placeholder="Cari layanan..." aria-label="Cari layanan">
                        </div>
                        <div class="form-text" id="search-hint" style="display: none;">
                            <i class="fas fa-info-circle me-1"></i> Pencarian akan menampilkan hasil dari semua unit
                        </div>
                    </div>

                    <!-- Quick Jump Menu -->
                    <div class="mb-4" id="unit-pills-container">
                        <div class="d-flex align-items-center mb-2">
                            <span class="text-muted me-2"><i class="fas fa-filter me-1"></i> Pilih Unit:</span>
                            <div class="unit-pills">
                                @php
                                    $units = $services->pluck('unit_id', 'unit.unit_name')->toArray();
                                @endphp
                                @foreach($units as $unitName => $unitId)
                                    <button onclick="scrollToUnit({{ $unitId }})"
                                            class="btn btn-sm btn-outline-primary rounded-pill me-2 mb-2"
                                            aria-label="Pilih unit {{ $unitName }}">
                                        {{ $unitName }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Daftar Unit dengan Layanan -->
                    <div class="scrolling-units" id="service-list-content">
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
                                    @foreach ($services->where('unit_id', $unitId) as $svc)
                                        <div class="col service-item">
                                            <div class="card h-100 border-0 shadow-sm hover-shadow transition-300">
                                                <div class="card-body p-4">
                                                    <button
                                                        onclick="selectService({{ $svc->id ?? 0 }}, {{ $svc->unit_id ?? 0 }}, '{{ addslashes($svc->svc_name) }}')"
                                                        class="btn btn-link text-decoration-none p-0 w-100 text-start"
                                                        aria-label="Pilih layanan {{ $svc->svc_name }} dari unit {{ $svc->unit->unit_name }}">
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-shrink-0 me-3">
                                                                <div class="bg-light text-primary rounded-circle p-3">
                                                                    <i class="fas fa-concierge-bell fa-fw"></i>
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h5 class="mb-1">{{ $svc->svc_name }}</h5>
                                                                <div class="text-muted small">
                                                                    {{ $svc->unit->unit_name }}
                                                                </div>
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

                <div class="form-section {{ isset($service) ? 'visible' : '' }}" id="form-section">
                    <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data" id="ticket-form">
                        @csrf
                        <input type="hidden" name="unit_id" id="unit_id" value="{{ isset($service) ? $service->unit_id : '' }}">
                        <input type="hidden" name="service_id" id="service_id" value="{{ isset($service) ? $service->id : '' }}">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="title" name="title"
                                           placeholder="Judul Aduan" value="{{ old('title') }}" required>
                                    <label for="title"><i class="fas fa-heading me-2"></i>Judul Laporan</label>
                                    @error('title')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <textarea class="form-control" id="description" name="description"
                                              placeholder="Deskripsikan masalah Anda secara detail" style="height: 150px"
                                              required>{{ old('description') }}</textarea>
                                    <label for="description"><i class="fas fa-comment-alt me-2"></i>Deskripsi Masalah</label>
                                    @error('description')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-4">
                                    <label for="location" class="form-label fw-bold"><i class="fas fa-map-marker-alt me-2"></i>Lokasi Laporan (Opsional)</label>
                                    <div class="mb-3">
                                        <div id="map"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="latitude" name="latitude"
                                                       placeholder="Latitude" readonly>
                                                <label for="latitude"><i class="fas fa-globe me-2"></i>Latitude</label>
                                                @error('latitude')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="longitude" name="longitude"
                                                       placeholder="Longitude" readonly>
                                                <label for="longitude"><i class="fas fa-globe me-2"></i>Longitude</label>
                                                @error('longitude')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-text mt-2">
                                        <i class="fas fa-info-circle me-1"></i> Lokasi akan otomatis terdeteksi menggunakan GPS. Anda juga dapat menyeret pin pada peta untuk menyesuaikan lokasi.
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-4">
                                    <label for="images" class="form-label fw-bold"><i class="fas fa-images me-2"></i>Unggah Gambar Pendukung (Opsional)</label>
                                    <div class="dropzone-container p-4 text-center border border-dashed rounded-3 bg-light" id="dropzone">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-cloud-upload-alt text-primary fa-3x mb-3"></i>
                                            <p class="mb-2">Klik atau seret file ke sini</p>
                                            <p class="text-muted small mb-0" id="fileNames">Tidak ada file dipilih</p>
                                        </div>
                                        <input type="file" name="images[]" id="images" multiple class="d-none" accept="image/*">
                                    </div>
                                    @error('images')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                    <div id="preview-container" class="row mt-3"></div>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i> Format yang didukung: JPG, PNG, GIF (maks 2MB per file)
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-between gap-3">
                                    <button type="button" onclick="showServiceList()"
                                            class="btn btn-outline-secondary btn-lg px-4"
                                            aria-label="Kembali ke daftar layanan">
                                        <i class="fas fa-arrow-left me-2"></i>Kembali
                                    </button>
                                    <button type="submit" class="btn btn-primary btn-lg px-5">
                                        <i class="fas fa-paper-plane me-2"></i>Kirim Laporan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-footer bg-light text-center py-3">
            <p class="text-muted mb-0">
                <i class="fas fa-info-circle me-2"></i>Laporan Anda akan diverifikasi oleh tim kami sebelum diproses lebih lanjut
            </p>
        </div>
    </main>
</div>
@endsection

@section('scripts')
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="{{ asset('assets/leaflet/leaflet.js') }}"></script>
    <script>
    // Prevent DataTables initialization
    window.One = window.One || {};
    window.One.initDataTables = function() {
        console.log('DataTables initialization skipped for create page');
    };

    document.addEventListener('DOMContentLoaded', function() {
        let mapInstance = null;
        let marker = null;
        let selectedFiles = [];

        // Map initialization (ported from guest.blade.php)
        function initializeMap() {
            const mapContainer = document.getElementById('map');
            if (!mapContainer) {
                console.error('Map container not found');
                return;
            }

            // Set map container styles
            mapContainer.style.height = '300px';
            mapContainer.style.width = '100%';

            // Get initial coordinates
            let lat = parseFloat(document.getElementById('latitude').value) || -8.0916;
            let lng = parseFloat(document.getElementById('longitude').value) || 112.1814;

            // Remove existing map instance if present
            if (mapInstance) {
                mapInstance.remove();
            }

            // Initialize map
            mapInstance = L.map('map').setView([lat, lng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(mapInstance);

            // Add draggable marker
            marker = L.marker([lat, lng], {
                draggable: true
            }).addTo(mapInstance);

            // Update inputs on marker drag
            marker.on('dragend', function(e) {
                const position = marker.getLatLng();
                document.getElementById('latitude').value = position.lat.toFixed(6);
                document.getElementById('longitude').value = position.lng.toFixed(6);
            });

            // Ensure map renders correctly
            setTimeout(() => {
                mapInstance.invalidateSize();
            }, 100);
        }

        // Geolocation handling
        function setGeolocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        document.getElementById('latitude').value = lat.toFixed(6);
                        document.getElementById('longitude').value = lng.toFixed(6);
                        if (mapInstance && marker) {
                            mapInstance.setView([lat, lng], 13);
                            marker.setLatLng([lat, lng]);
                            mapInstance.invalidateSize();
                        }
                    },
                    function(error) {
                        console.warn('Geolocation error:', error.message);
                        document.getElementById('latitude').value = -8.0916;
                        document.getElementById('longitude').value = 112.1814;
                        if (mapInstance && marker) {
                            mapInstance.setView([-8.0916, 112.1814], 13);
                            marker.setLatLng([-8.0916, 112.1814]);
                        }
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            } else {
                console.warn('Geolocation not supported');
                document.getElementById('latitude').value = -8.0916;
                document.getElementById('longitude').value = 112.1814;
            }
        }

        // MutationObserver to initialize map when form is visible
        const formSection = document.getElementById('form-section');
        if (formSection) {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === 'class' && formSection.classList.contains('visible')) {
                        setTimeout(() => {
                            initializeMap();
                            setGeolocation();
                        }, 250);
                    }
                });
            });

            observer.observe(formSection, {
                attributes: true,
                attributeFilter: ['class']
            });

            // Initialize map immediately if form is visible (e.g., via UUID)
            if (formSection.classList.contains('visible')) {
                setTimeout(() => {
                    initializeMap();
                    setGeolocation();
                }, 250);
            }
        } else {
            console.error('Form section not found');
        }

        // Service selection
        window.selectService = function(serviceId, unitId, serviceName) {
            document.getElementById('service_id').value = serviceId;
            document.getElementById('unit_id').value = unitId;
            document.getElementById('form-title').textContent = `Form Aduan ${serviceName}`;

            const serviceList = document.getElementById('service-list');
            const formSection = document.getElementById('form-section');
            if (serviceList && formSection) {
                serviceList.classList.add('hidden');
                formSection.classList.add('visible');
                formSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                if (typeof AOS !== 'undefined') {
                    AOS.refresh();
                }
            }
        };

        // Show service list
        window.showServiceList = function() {
            const serviceList = document.getElementById('service-list');
            const formSection = document.getElementById('form-section');
            if (serviceList && formSection) {
                serviceList.classList.remove('hidden');
                formSection.classList.remove('visible');
                document.getElementById('service_id').value = '';
                document.getElementById('unit_id').value = '';
                document.getElementById('form-title').textContent = 'Form Aduan';
                serviceList.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                if (typeof AOS !== 'undefined') {
                    AOS.refresh();
                }
            }
        };

        // Scroll to unit
        window.scrollToUnit = function(unitId) {
            const unitElement = document.getElementById(`unit-${unitId}`);
            if (unitElement) {
                unitElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        };

        // File upload and preview
        const dropzone = document.getElementById('dropzone');
        const input = document.getElementById('images');
        const fileNamesElement = document.getElementById('fileNames');
        const previewContainer = document.getElementById('preview-container');

        function updateFileNamesAndPreview(event) {
            const newFiles = Array.from(event.target.files).filter(file => {
                const isImage = file.type.startsWith('image/');
                const isValidSize = file.size <= 2 * 1024 * 1024; // 2MB
                if (!isImage) {
                    alert(`${file.name} bukan gambar (hanya JPG, PNG, GIF yang didukung).`);
                    return false;
                }
                if (!isValidSize) {
                    alert(`${file.name} terlalu besar (maks 2MB).`);
                    return false;
                }
                return true;
            });
            selectedFiles = [...selectedFiles, ...newFiles];

            const dataTransfer = new DataTransfer();
            selectedFiles.forEach(file => dataTransfer.items.add(file));
            input.files = dataTransfer.files;

            if (selectedFiles.length > 0) {
                fileNamesElement.textContent = selectedFiles.length === 1 ?
                    selectedFiles[0].name :
                    `${selectedFiles.length} file dipilih`;
                renderPreview();
            } else {
                fileNamesElement.textContent = 'Tidak ada file dipilih';
                previewContainer.innerHTML = '';
            }
        }

        function renderPreview() {
            previewContainer.innerHTML = '';
            selectedFiles.forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const col = document.createElement('div');
                        col.className = 'col-6 col-md-3 mb-3';
                        col.innerHTML = `
                            <div class="position-relative">
                                <img src="${e.target.result}" class="img-fluid rounded shadow-sm" style="max-height: 150px; object-fit: cover;">
                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0" 
                                        style="transform: translate(50%, -50%);"
                                        onclick="removeFile(${index})"
                                        aria-label="Hapus gambar ${file.name}">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <small class="d-block text-muted mt-1 text-center">${file.name}</small>
                            </div>
                        `;
                        previewContainer.appendChild(col);
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        window.removeFile = function(index) {
            selectedFiles.splice(index, 1);
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach(file => dataTransfer.items.add(file));
            input.files = dataTransfer.files;
            fileNamesElement.textContent = selectedFiles.length === 0 ?
                'Tidak ada file dipilih' :
                selectedFiles.length === 1 ?
                selectedFiles[0].name :
                `${selectedFiles.length} file dipilih`;
            renderPreview();
        };

        // Drag-and-drop events
        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('border-primary', 'bg-primary-subtle');
        });

        dropzone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            dropzone.classList.remove('border-primary', 'bg-primary-subtle');
        });

        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.classList.remove('border-primary', 'bg-primary-subtle');
            const newFiles = Array.from(e.dataTransfer.files).filter(file => {
                const isImage = file.type.startsWith('image/');
                const isValidSize = file.size <= 2 * 1024 * 1024; // 2MB
                if (!isImage) {
                    alert(`${file.name} bukan gambar (hanya JPG, PNG, GIF yang didukung).`);
                    return false;
                }
                if (!isValidSize) {
                    alert(`${file.name} terlalu besar (maks 2MB).`);
                    return false;
                }
                return true;
            });
            selectedFiles = [...selectedFiles, ...newFiles];
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach(file => dataTransfer.items.add(file));
            input.files = dataTransfer.files;
            const event = new Event('change', { bubbles: true });
            input.dispatchEvent(event);
        });

        dropzone.addEventListener('click', () => input.click());
        input.addEventListener('change', updateFileNamesAndPreview);

        // Search functionality
        const searchInput = document.getElementById('search');
        searchInput.addEventListener('input', function() {
            const searchValue = searchInput.value.toLowerCase();
            const serviceItems = document.querySelectorAll('.service-item');
            serviceItems.forEach(item => {
                const serviceName = item.querySelector('h5').textContent.toLowerCase();
                item.style.display = serviceName.includes(searchValue) ? 'block' : 'none';
            });
            document.getElementById('search-hint').style.display = searchValue ? 'block' : 'none';
            document.getElementById('unit-pills-container').style.display = searchValue ? 'none' : 'block';
            document.querySelectorAll('.unit-section').forEach(section => {
                section.style.display = searchValue ? 'none' : 'block';
            });
        });

        // Initialize AOS
        AOS.init();
    });
    </script>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/aos/aos.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/leaflet/leaflet.css') }}">
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
        .form-section {
            display: none;
        }
        .form-section.visible {
            display: block;
        }
        .service-list {
            display: block;
        }
        .service-list.hidden {
            display: none;
        }
        #map {
            height: 300px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
        }
        #preview-container img {
            transition: all 0.3s ease;
        }
        #preview-container img:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        #preview-container .btn-danger {
            padding: 2px 8px;
            line-height: 1;
        }
        #preview-container .btn-danger:hover {
            background-color: #dc3545;
            border-color: #dc3545;
        }
    </style>
@endsection