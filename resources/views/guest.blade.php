<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Helpdesk Pemerintah Kota Blitar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/aos/aos.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/leaflet/leaflet.css') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/media/img/logo.png') }}">
    <style>
        .gradient {
            background: linear-gradient(90deg, #1572e8 0%, rgb(21, 68, 144) 100%);
        }

        html,
        body {
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;

        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        p,
        a,
        button,
        input,
        textarea {
            font-family: 'Poppins', sans-serif;
        }

        #header {
            transition: background-color 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            background-color: transparent !important;
            border-bottom: 3px solid #1572e8;
        }

        #header .nav-link {
            color: white !important;
            transition: color 0.3s ease-in-out;
        }

        #header.scrolled .nav-link {
            color: black !important;
        }

        #header .nav-link:hover,
        #header.scrolled .nav-link:hover {
            color: #1572e8 !important;
        }

        #header.scrolled {
            background-color: white !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .nav-link:hover {
            color: #1572e8 !important;
        }

        .btn-primary:hover {
            color: #1572e8 !important;
            background-color: #f8f9fa;
        }

        .row {
            overflow: visible;
        }

        .hover-shadow {
            transition: all 0.3s ease;
        }

        .hover-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }

        /* Hide form by default */
        .form-section {
            display: none;
        }

        /* Show form when visible class is added */
        .form-section.visible {
            display: block;
        }

        /* Show service list by default */
        .service-list {
            display: block;
        }

        /* Hide service list when hidden class is added */
        .service-list.hidden {
            display: none;
        }

        /* Styling untuk peta */
        .dropzone-container {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .dropzone-container:hover {
            background-color: #f8f9fa;
            border-color: var(--bs-primary) !important;
        }

        #map {
            height: 300px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
        }

        /* Styling untuk pratinjau gambar */
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

        @media (max-width: 1200px) {

            html,
            body {
                font-size: 14px;
                /* overflow: hidden; */
            }

            .col-12 img {
                max-width: 60%;
                max-height: auto;
            }

            .col-12 h1 {
                font-size: 20px !important;
            }

            .col-12 p {
                font-size: 12px !important;
                padding: 30px 0 30px 0;
            }

            h2 {
                font-size: 20px !important;
            }

            h3 {
                font-size: 14px !important;
                padding: 10px;
            }

            h4,
            h5 {
                font-size: 14px !important;
            }

            .card-account button {
                width: 5cm !important;
                height: 1cm !important;
                font-size: 13px !important;
            }

            .col-md-4 img {
                max-width: 50%;
                max-height: auto;
            }

            .d-flex button {
                font-size: 12px;
            }
        }
    </style>
</head>

<body class="text-white gradient">
    <!-- Navbar tetap sama -->
    <nav id="header" class="navbar navbar-expand-lg navbar-light fixed-top bg-white text-white">
        <div class="container">
            <div class="navbar-brand">
                <img id="nav-logo" src="{{ asset('assets/media/img/logo-helpdesk-1.png') }}" class="img-fluid"
                    style="width: 150px; height: auto;">
            </div>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav-content"
                aria-controls="nav-content" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="nav-content">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link text-black" href="/">Kembali ke Halaman Utama</a>
                    </li>
                </ul>
                <button class="btn btn-primary text-white fw-bold rounded-pill ms-3 mt-3 mt-lg-0 px-4 py-2 shadow"
                    onclick="window.location.href='{{ route('login') }}'">
                    Login
                </button>
            </div>
        </div>
    </nav>
    <section id="beranda" class="pt-5">
        <div class="position-relative">
            <svg viewBox="0 0 1428 174" version="1.1" xmlns="http://www.w3.org/2000/svg"
                xmlns:xlink="http://www.w3.org/1999/xlink">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <g transform="translate(-2.000000, 44.000000)" fill="#FFFFFF" fill-rule="nonzero">
                        <path
                            d="M0,0 C90.7283404,0.927527913 147.912752,27.187927 291.910178,59.9119003 C387.908462,81.7278826 543.605069,89.334785 759,82.7326078 C469.336065,156.254352 216.336065,153.6679 0,74.9732496"
                            opacity="0.100000001"></path>
                        <path
                            d="M100,104.708498 C277.413333,72.2345949 426.147877,52.5246657 546.203633,45.5787101 C666.259389,38.6327546 810.524845,41.7979068 979,55.0741668 C931.069965,56.122511 810.303266,74.8455141 616.699903,111.243176 C423.096539,147.640838 250.863238,145.462612 100,104.708498 Z"
                            opacity="0.100000001"></path>
                        <path
                            d="M1046,51.6521276 C1130.83045,29.328812 1279.08318,17.607883 1439,40.1656806 L1439,120 C1271.17211,77.9435312 1140.17211,55.1609071 1046,51.6521276 Z"
                            id="Path-4" opacity="0.200000003"></path>
                    </g>
                    <g transform="translate(-4.000000, 76.000000)" fill="#FFFFFF" fill-rule="nonzero">
                        <path
                            d="M0.457,34.035 C57.086,53.198 98.208,65.809 123.822,71.865 C181.454,85.495 234.295,90.29 272.033,93.459 C311.355,96.759 396.635,95.801 461.025,91.663 C486.76,90.01 518.727,86.372 556.926,80.752 C595.747,74.596 622.372,70.008 636.799,66.991 C663.913,61.324 712.501,49.503 727.605,46.128 C780.47,34.317 818.839,22.532 856.324,15.904 C922.689,4.169 955.676,2.522 1011.185,0.432 C1060.705,1.477 1097.39,3.129 1121.236,5.387 C1161.703,9.219 1208.621,17.821 1235.4,22.304 C1285.855,30.748 1354.351,47.432 1440.886,72.354 L1441.191,104.352 L1.121,104.031 L0.457,34.035 Z">
                        </path>
                    </g>
                </g>
            </svg>
        </div>
    </section>

    <!-- Section Lapor Tanpa Login -->
    <section class="bg-white py-5" id="lapor-tanpa-login">
        <div class="container py-4">
            <h2 class="display-4 fw-bold text-center text-dark my-3" data-aos="fade-down" data-aos-duration="3000">Lapor
                Sebagai Tamu</h2>
            <div class="mx-auto w-25 my-3" style="height: 10px; background-color: #1572e8;"></div><br><br>
            <p class="text-center text-muted mb-5" data-aos="fade-down" data-aos-duration="1000">Laporkan masalah Anda
                tanpa perlu login untuk layanan publik tertentu.</p>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show p-4 mb-4 rounded" role="alert">
                            <strong><i class="fas fa-check-circle me-2"></i>Berhasil!</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show p-4 mb-4 rounded" role="alert">
                            <strong><i class="fas fa-exclamation-circle me-2"></i>Error!</strong> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show p-4 mb-4 rounded" role="alert">
                            <strong><i class="fas fa-exclamation-triangle me-2"></i>Perhatian!</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="card shadow border-0 rounded-lg" data-aos="fade-up" data-aos-duration="1000">
                        <h4 class="card-header bg-primary text-white py-3" id="form-title">
                            <i class="fas fa-edit me-2"></i>Form Aduan {{ isset($service) ? $service->svc_name : '' }}
                        </h4>
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
                                        <i class="fas fa-info-circle me-1"></i> Pencarian akan menampilkan hasil dari
                                        semua unit
                                    </div>
                                </div>

                                <!-- Quick Jump Menu -->
                                <div class="mb-4" id="unit-pills-container">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="text-muted me-2"><i class="fas fa-filter me-1"></i> Pilih
                                            Unit:</span>
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
                                                                    onclick="selectService({{ $svc->id ?? 0 }}, {{ $svc->unit_id ?? 0 }}, '{{ $svc->svc_name }}')"
                                                                    class="btn btn-link text-decoration-none p-0 w-100 text-start"
                                                                    aria-label="Pilih layanan {{ $svc->svc_name }} dari unit {{ $svc->unit->unit_name }}">
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="flex-shrink-0 me-3">
                                                                            <div
                                                                                class="bg-light text-primary rounded-circle p-3">
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
                                <form action="{{ route('tickets.store.guest') }}" method="POST"
                                    enctype="multipart/form-data" id="ticket-form">
                                    @csrf
                                    <input type="hidden" name="unit_id" id="unit_id"
                                        value="{{ isset($service) ? $service->unit_id : '' }}">
                                    <input type="hidden" name="service_id" id="service_id"
                                        value="{{ isset($service) ? $service->id : '' }}">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="guest_name"
                                                    name="guest_name" placeholder="Nama Lengkap"
                                                    value="{{ old('guest_name') }}" required>
                                                <label for="guest_name"><i class="fas fa-user me-2"></i>Nama
                                                    Lengkap</label>
                                                @error('guest_name')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <input type="email" class="form-control" id="guest_email"
                                                    name="guest_email" placeholder="Email"
                                                    value="{{ old('guest_email') }}" required>
                                                <label for="guest_email"><i
                                                        class="fas fa-envelope me-2"></i>Email</label>
                                                @error('guest_email')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="title" name="title"
                                            placeholder="Judul Aduan" value="{{ old('title') }}" required>
                                        <label for="title"><i class="fas fa-heading me-2"></i>Judul Aduan</label>
                                        @error('title')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-floating mb-3">
                                        <textarea class="form-control" id="description" name="description"
                                            placeholder="Deskripsikan masalah Anda secara detail" style="height: 150px"
                                            required>{{ old('description') }}</textarea>
                                        <label for="description"><i class="fas fa-comment-alt me-2"></i>Deskripsi
                                            Masalah</label>
                                        @error('description')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Bagian Peta -->
                                    <div class="mb-4">
                                        <label for="location" class="form-label fw-bold"><i
                                                class="fas fa-map-marker-alt me-2"></i>Lokasi Aduan (Opsional)</label>
                                        <div class="mb-3">
                                            <div id="map"></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="latitude"
                                                        name="latitude" placeholder="Latitude" readonly>
                                                    <label for="latitude"><i
                                                            class="fas fa-globe me-2"></i>Latitude</label>
                                                    @error('latitude')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="longitude"
                                                        name="longitude" placeholder="Longitude" readonly>
                                                    <label for="longitude"><i
                                                            class="fas fa-globe me-2"></i>Longitude</label>
                                                    @error('longitude')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-text mt-2">
                                            <i class="fas fa-info-circle me-1"></i> Lokasi akan otomatis terdeteksi
                                            menggunakan GPS. Anda juga dapat menyeret pin pada peta untuk menyesuaikan
                                            lokasi.
                                        </div>
                                    </div>

                                    <!-- Bagian Upload File dengan Pratinjau -->
                                    <div class="mb-4">
                                        <label for="images" class="form-label fw-bold"><i
                                                class="fas fa-images me-2"></i>Unggah Gambar Pendukung
                                            (Opsional)</label>
                                        <div class="dropzone-container p-4 text-center border border-dashed rounded-3 bg-light"
                                            id="dropzone">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fas fa-cloud-upload-alt text-primary fa-3x mb-3"></i>
                                                <p class="mb-2">Klik atau seret file ke sini</p>
                                                <p class="text-muted small mb-0" id="fileNames">Tidak ada file dipilih
                                                </p>
                                            </div>
                                            <input type="file" name="images[]" id="images" multiple class="d-none"
                                                accept="image/*">
                                        </div>
                                        @error('images')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                        <!-- Area untuk pratinjau gambar -->
                                        <div id="preview-container" class="row mt-3"></div>
                                        <div class="form-text">
                                            <i class="fas fa-info-circle me-1"></i> Format yang didukung: JPG, PNG, GIF
                                            (maks 2MB per file)
                                        </div>
                                    </div>

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
                                </form>
                            </div>
                        </div>
                        <div class="card-footer bg-light text-center py-3">
                            <p class="text-muted mb-0">
                                <i class="fas fa-info-circle me-2"></i>Laporan Anda akan diverifikasi oleh tim kami
                                sebelum diproses lebih lanjut
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer style="background: linear-gradient(90deg, #1572e8 0%, rgb(21, 68, 144) 100%);">
        <svg class="wave-top" viewBox="0 0 1439 147" version="1.1" xmlns="http://www.w3.org/2000/svg"
            xmlns:xlink="http://www.w3.org/1999/xlink">
            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <g transform="translate(-1.000000, -14.000000)" fill-rule="nonzero">
                    <g class="wave" fill="#f8fafc">
                        <path
                            d="M1440,84 C1383.555,64.3 1342.555,51.3 1317,45 C1259.5,30.824 1206.707,25.526 1169,22 C1129.711,18.326 1044.426,18.475 980,22 C954.25,23.409 922.25,26.742 884,32 C845.122,37.787 818.455,42.121 804,45 C776.833,50.41 728.136,61.77 713,65 C660.023,76.309 621.544,87.729 584,94 C517.525,105.104 484.525,106.438 429,108 C379.49,106.484 342.823,104.484 319,102 C278.571,97.783 231.737,88.736 205,84 C154.629,75.076 86.296,57.743 0,32 L0,0 L1440,0 L1440,84 Z">
                        </path>
                    </g>
                    <g transform="translate(1.000000, 15.000000)" fill="#FFFFFF">
                        <g
                            transform="translate(719.500000, 68.500000) rotate(-180.000000) translate(-719.500000, -68.500000) ">
                            <path
                                d="M0,0 C90.7283404,0.927527913 147.912752,27.187927 291.910178,59.9119003 C387.908462,81.7278826 543.605069,89.334785 759,82.7326078 C469.336065,156.254352 216.336065,153.6679 0,74.9732496"
                                opacity="0.100000001"></path>
                            <path
                                d="M100,104.708498 C277.413333,72.2345949 426.147877,52.5246657 546.203633,45.5787101 C666.259389,38.6327546 810.524845,41.7979068 979,55.0741668 C931.069965,56.122511 810.303266,74.8455141 616.699903,111.243176 C423.096539,147.640838 250.863238,145.462612 100,104.708498 Z"
                                opacity="0.100000001"></path>
                            <path
                                d="M1046,51.6521276 C1130.83045,29.328812 1279.08318,17.607883 1439,40.1656806 L1439,120 C1271.17211,77.9435312 1140.17211,55.1609071 1046,51.6521276 Z"
                                opacity="0.200000003"></path>
                        </g>
                    </g>
                </g>
            </g>
        </svg>
        <div class="container text-white py-5">
            <div class="row">
                <div class="col-md-4 mb-4" data-aos="fade-left" data-aos-duration="3000">
                    <img src="{{ asset('assets/media/img/logo-helpdesk-1.png') }}" alt="Logo Helpdesk"
                        class="img-fluid mb-3" style="width: 300px; height: auto;">
                    <p>Sistem Layanan Bantuan dan Dukungan Pemerintah Kota Blitar</p>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-duration="3000">
                    <h5 class="fw-semibold mb-3">Link Terkait</h5>
                    <a href="https://blitarkota.go.id/" class="text-white text-decoration-none">Pemerintah Kota
                        Blitar</a> <br><br>
                    <a href="https://diskominfotik.blitarkota.go.id/"
                        class="text-white text-decoration-none">DISKOMINFOTIK Kota Blitar</a>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-right" data-aos-duration="3000">
                    <h5 class="fw-semibold mb-3">Kontak</h5>
                    <p>Jl. Dr. Moh.Hatta Nomor 05 - Kota Blitar</p>
                    <p>Telp. 0342807805</p>
                </div>
            </div>
            <div class="text-start border-top pt-3">
                <p class="mb-0 text-center">© 2025 Helpdesk Pemerintah Kota Blitar</p>
            </div>
        </div>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="{{ asset('assets/js/cdn.js') }}"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    {{--
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var navbar = document.getElementById("header");
            var logo = document.getElementById("nav-logo");

            window.addEventListener("scroll", function () {
                if (window.scrollY > 50) {
                    navbar.classList.add("scrolled");
                    logo.src = "{{ asset('assets/media/img/logo-helpdesk-black.png') }}";
                } else {
                    navbar.classList.remove("scrolled");
                    logo.src = "{{ asset('assets/media/img/logo-helpdesk-1.png') }}";
                }
            });
        })
    </script>
    <script>
        // Global selectService function
        function selectService(serviceId, unitId) {
            console.log('selectService called with serviceId:', serviceId, 'unitId:', unitId);
            Alpine.store('form').selectedService = serviceId;
            Alpine.store('form').selectedUnit = unitId;
            Alpine.store('form').showServices = false;

            const serviceList = document.querySelector('.service-list');
            const formSection = document.querySelector('.form-section');
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
            } else {
                console.error('Service list or form section not found in DOM');
            }

            const serviceName = document.querySelector(`button[onclick*="selectService(${serviceId}, ${unitId})"]`)?.textContent.trim();
            if (serviceName) {
                updateFormTitle(serviceName);
            }
        }

        // Global scrollToUnit function (for unit pills)
        function scrollToUnit(unitId) {
            console.log('scrollToUnit called with unitId:', unitId);
            const unitElement = document.getElementById(`unit-${unitId}`);
            if (unitElement) {
                unitElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            } else {
                console.error(`Unit element with ID unit-${unitId} not found`);
            }
        }

        // Alpine.js untuk map dan upload file
        document.addEventListener('alpine:init', () => {
            Alpine.store('form', {
                showServices: {{ isset($service) ? 'false' : 'true' }},
                selectedService: {{ isset($service) ? $service->id : 'null' }},
                selectedUnit: {{ isset($service) ? $service->unit_id : 'null' }}
    });

            Alpine.data('mapForm', () => ({
                latitude: null,
                longitude: null,
                mapInitialized: false,
                selectedFiles: [],

                init() {
                    // Initialize geolocation but don't setup map yet
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            (position) => {
                                this.latitude = position.coords.latitude;
                                this.longitude = position.coords.longitude;
                                document.getElementById('latitude').value = this.latitude.toFixed(6);
                                document.getElementById('longitude').value = this.longitude.toFixed(6);
                            },
                            (error) => {
                                console.warn('Geolocation error:', error);
                                this.latitude = -8.0916;
                                this.longitude = 112.1814;
                                document.getElementById('latitude').value = this.latitude.toFixed(6);
                                document.getElementById('longitude').value = this.longitude.toFixed(6);
                            }
                        );
                    } else {
                        this.latitude = -8.0916;
                        this.longitude = 112.1814;
                        document.getElementById('latitude').value = this.latitude.toFixed(6);
                        document.getElementById('longitude').value = this.longitude.toFixed(6);
                    }

                    // Watch for form visibility to initialize map
                    this.$watch('isFormVisible', (visible) => {
                        if (visible && !this.mapInitialized) {
                            this.setupMap();
                        }
                    });

                    // Check initial visibility
                    this.checkFormVisibility();
                },

                get isFormVisible() {
                    const formSection = this.$el.closest('.form-section');
                    return formSection && formSection.classList.contains('visible');
                },

                checkFormVisibility() {
                    if (this.isFormVisible && !this.mapInitialized) {
                        this.setupMap();
                    }
                },

                setupMap() {
                    const mapElement = document.getElementById('map');
                    if (!mapElement) {
                        console.error('Map container not found');
                        return;
                    }

                    // Ensure map container has dimensions
                    mapElement.style.height = '300px';
                    mapElement.style.width = '100%';

                    setTimeout(() => {
                        const map = L.map(mapElement).setView([this.latitude || -8.0916, this.longitude || 112.1814], 13);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© OpenStreetMap contributors',
                            maxZoom: 19
                        }).addTo(map);

                        const marker = L.marker([this.latitude || -8.0916, this.longitude || 112.1814], {
                            draggable: true
                        }).addTo(map);

                        marker.on('dragend', (e) => {
                            const pos = e.target.getLatLng();
                            this.latitude = pos.lat;
                            this.longitude = pos.lng;
                            document.getElementById('latitude').value = pos.lat.toFixed(6);
                            document.getElementById('longitude').value = pos.lng.toFixed(6);
                        });

                        setTimeout(() => {
                            map.invalidateSize();
                            setTimeout(() => map.invalidateSize(), 500);
                        }, 100);

                        this.mapInitialized = true;
                    }, 300);
                },

                updateFileNamesAndPreview(event) {
                    const files = event.target.files;
                    const fileNamesElement = document.getElementById('fileNames');
                    const previewContainer = document.getElementById('preview-container');
                    previewContainer.innerHTML = '';
                    this.selectedFiles = Array.from(files);

                    if (files.length > 0) {
                        fileNamesElement.textContent = files.length === 1 ?
                            files[0].name :
                            `${files.length} file dipilih`;

                        Array.from(files).forEach((file, index) => {
                            if (file.type.startsWith('image/')) {
                                const reader = new FileReader();
                                reader.onload = (e) => {
                                    const col = document.createElement('div');
                                    col.className = 'col-6 col-md-3 mb-3';
                                    col.innerHTML = `
                                <div class="position-relative">
                                    <img src="${e.target.result}" class="img-fluid rounded shadow-sm" style="max-height: 150px; object-fit: cover;">
                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0" 
                                            style="transform: translate(50%, -50%);"
                                            onclick="Alpine.store('mapForm').removeFile(${index})"
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
                    } else {
                        fileNamesElement.textContent = 'Tidak ada file dipilih';
                    }
                },

                removeFile(index) {
                    this.selectedFiles.splice(index, 1);
                    const input = document.getElementById('images');
                    const dataTransfer = new DataTransfer();
                    this.selectedFiles.forEach(file => dataTransfer.items.add(file));
                    input.files = dataTransfer.files;

                    const fileNamesElement = document.getElementById('fileNames');
                    fileNamesElement.textContent = this.selectedFiles.length === 0 ?
                        'Tidak ada file dipilih' :
                        this.selectedFiles.length === 1 ?
                            this.selectedFiles[0].name :
                            `${this.selectedFiles.length} file dipilih`;

                    const previewContainer = document.getElementById('preview-container');
                    previewContainer.innerHTML = '';
                    this.selectedFiles.forEach((file, newIndex) => {
                        if (file.type.startsWith('image/')) {
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                const col = document.createElement('div');
                                col.className = 'col-6 col-md-3 mb-3';
                                col.innerHTML = `
                            <div class="position-relative">
                                <img src="${e.target.result}" class="img-fluid rounded shadow-sm" style="max-height: 150px; object-fit: cover;">
                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0" 
                                        style="transform: translate(50%, -50%);"
                                        onclick="Alpine.store('mapForm').removeFile(${newIndex})"
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
            }));
        });

        // Map initialization and observer
        document.addEventListener('DOMContentLoaded', function () {
            let mapInstance = null;

            function initializeOrRefreshMap() {
                const mapContainer = document.getElementById('map');
                if (!mapContainer) {
                    console.error('Map container not found');
                    return;
                }

                mapContainer.style.height = '300px';
                mapContainer.style.width = '100%';

                const lat = parseFloat(document.getElementById('latitude').value) || -8.0916;
                const lng = parseFloat(document.getElementById('longitude').value) || 112.1814;

                if (mapInstance) {
                    mapInstance.remove();
                }

                setTimeout(() => {
                    mapInstance = L.map('map').setView([lat, lng], 13);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors',
                        maxZoom: 19
                    }).addTo(mapInstance);

                    const marker = L.marker([lat, lng], {
                        draggable: true
                    }).addTo(mapInstance);

                    marker.on('dragend', function (e) {
                        const position = marker.getLatLng();
                        document.getElementById('latitude').value = position.lat.toFixed(6);
                        document.getElementById('longitude').value = position.lng.toFixed(6);
                    });

                    setTimeout(() => {
                        mapInstance.invalidateSize();
                        setTimeout(() => mapInstance.invalidateSize(), 500);
                    }, 100);
                }, 300);
            }

            const observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    if (mutation.target.classList.contains('visible')) {
                        setTimeout(initializeOrRefreshMap, 250);
                    }
                });
            });

            const formSection = document.querySelector('.form-section');
            if (formSection) {
                observer.observe(formSection, {
                    attributes: true,
                    attributeFilter: ['class']
                });
            }

            // Initialize geolocation
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        document.getElementById('latitude').value = position.coords.latitude.toFixed(6);
                        document.getElementById('longitude').value = position.coords.longitude.toFixed(6);
                        if (mapInstance) {
                            mapInstance.setView([position.coords.latitude, position.coords.longitude], 13);
                            const markers = mapInstance.getPane('markerPane').getElementsByTagName('img');
                            if (markers.length > 0) {
                                mapInstance.eachLayer(layer => {
                                    if (layer instanceof L.Marker) {
                                        mapInstance.removeLayer(layer);
                                    }
                                });
                                L.marker([position.coords.latitude, position.coords.longitude], {
                                    draggable: true
                                }).addTo(mapInstance);
                            }
                        }
                    },
                    function (error) {
                        console.warn('Geolocation error:', error);
                    }
                );
            }

            // Drag-and-drop untuk upload file
            const dropzone = document.querySelector('.dropzone-container');
            const input = document.getElementById('images');

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
                input.files = e.dataTransfer.files;
                const event = new Event('change', {
                    bubbles: true
                });
                input.dispatchEvent(event);
            });
        });

        // AOS initialization
        AOS.init();
    </script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    let mapInstance = null;
    let selectedFiles = [];

    // Initialize map
    function initializeMap() {
        const mapContainer = document.getElementById('map');
        if (!mapContainer) {
            console.error('Map container not found');
            return;
        }

        // Ensure map container has dimensions
        mapContainer.style.height = '300px';
        mapContainer.style.width = '100%';

        // Get initial coordinates
        let lat = parseFloat(document.getElementById('latitude').value) || -8.0916;
        let lng = parseFloat(document.getElementById('longitude').value) || 112.1814;

        // Initialize geolocation
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    lat = position.coords.latitude;
                    lng = position.coords.longitude;
                    document.getElementById('latitude').value = lat.toFixed(6);
                    document.getElementById('longitude').value = lng.toFixed(6);
                    setupMap(lat, lng);
                },
                (error) => {
                    console.warn('Geolocation error:', error);
                    document.getElementById('latitude').value = lat.toFixed(6);
                    document.getElementById('longitude').value = lng.toFixed(6);
                    setupMap(lat, lng);
                }
            );
        } else {
            document.getElementById('latitude').value = lat.toFixed(6);
            document.getElementById('longitude').value = lng.toFixed(6);
            setupMap(lat, lng);
        }
    }

    function setupMap(lat, lng) {
        const mapContainer = document.getElementById('map');
        if (!mapContainer) return;

        // Remove existing map instance if any
        if (mapInstance) {
            mapInstance.remove();
        }

        // Initialize map with delay to ensure DOM is ready
        setTimeout(() => {
            mapInstance = L.map('map').setView([lat, lng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(mapInstance);

            const marker = L.marker([lat, lng], {
                draggable: true
            }).addTo(mapInstance);

            marker.on('dragend', function(e) {
                const position = marker.getLatLng();
                document.getElementById('latitude').value = position.lat.toFixed(6);
                document.getElementById('longitude').value = position.lng.toFixed(6);
            });

            // Ensure map renders correctly
            setTimeout(() => {
                mapInstance.invalidateSize();
                setTimeout(() => mapInstance.invalidateSize(), 500);
            }, 100);
        }, 300);
    }

    // Check form visibility and initialize map if form is visible
    function checkFormVisibility() {
        const formSection = document.getElementById('form-section');
        if (formSection && formSection.classList.contains('visible')) {
            initializeMap();
        }
    }

    // Service selection
    window.selectService = function(serviceId, unitId, serviceName) {
        console.log('selectService called with serviceId:', serviceId, 'unitId:', unitId, 'serviceName:', serviceName);
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
            initializeMap(); // Initialize map when form becomes visible
            if (typeof AOS !== 'undefined') {
                AOS.refresh();
            }
        } else {
            console.error('Service list or form section not found in DOM');
        }
    };

    // Show service list
    window.showServiceList = function() {
        const serviceList = document.getElementById('service-list');
        const formSection = document.getElementById('form-section');
        if (serviceList && formSection) {
            serviceList.classList.remove('hidden');
            formSection.classList.remove('visible');
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
        console.log('scrollToUnit called with unitId:', unitId);
        const unitElement = document.getElementById(`unit-${unitId}`);
        if (unitElement) {
            unitElement.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        } else {
            console.error(`Unit element with ID unit-${unitId} not found`);
        }
    };

    // File upload and preview
    const dropzone = document.getElementById('dropzone');
    const input = document.getElementById('images');
    const fileNamesElement = document.getElementById('fileNames');
    const previewContainer = document.getElementById('preview-container');

    function updateFileNamesAndPreview(event) {
        const newFiles = Array.from(event.target.files);
        // Append new files to selectedFiles
        selectedFiles = [...selectedFiles, ...newFiles];

        // Update the file input's files property
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));
        input.files = dataTransfer.files;

        // Update file names display
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
        const newFiles = Array.from(e.dataTransfer.files);
        // Append new files to selectedFiles
        selectedFiles = [...selectedFiles, ...newFiles];
        // Update the file input's files property
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));
        input.files = dataTransfer.files;
        // Trigger change event
        const event = new Event('change', { bubbles: true });
        input.dispatchEvent(event);
    });

    dropzone.addEventListener('click', () => input.click());
    input.addEventListener('change', updateFileNamesAndPreview);

    // Navbar scroll effect
    const navbar = document.getElementById("header");
    const logo = document.getElementById("nav-logo");
    window.addEventListener("scroll", function() {
        if (window.scrollY > 50) {
            navbar.classList.add("scrolled");
            logo.src = "{{ asset('assets/media/img/logo-helpdesk-black.png') }}";
        } else {
            navbar.classList.remove("scrolled");
            logo.src = "{{ asset('assets/media/img/logo-helpdesk-1.png') }}";
        }
    });

    // Initialize map if form is visible on page load
    checkFormVisibility();

    // Initialize AOS
    AOS.init();
});
</script>

</body>

</html>