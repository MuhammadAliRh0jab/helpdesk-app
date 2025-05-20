@extends('mobile.master.app')

@section('title', 'Selamat Datang')

@section('content')
    <div x-data="{ showForm: false }">
        <!-- Hero Block Wrapper -->
        <div class="hero-block-wrapper bg-primary" x-show="!showForm" x-transition>
            <!-- Styles -->
            <div class="hero-block-styles">
                <div class="hb-styles1" style="background-image: url('{{ asset('mobile/img/core-img/dot.png') }}')"></div>
                <div class="hb-styles2"></div>
                <div class="hb-styles3"></div>
            </div>

            <div class="custom-container">
                <div class="carousel slide carousel-fade" id="bootstrapCarouselFade" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <button class="active" type="button" data-bs-target="#bootstrapCarouselFade" data-bs-slide-to="0"
                            aria-current="true" aria-label="Slide 1"></button>
                        <button type="button" data-bs-target="#bootstrapCarouselFade" data-bs-slide-to="1"
                            aria-label="Slide 2"></button>
                        <button type="button" data-bs-target="#bootstrapCarouselFade" data-bs-slide-to="2"
                            aria-label="Slide 3"></button>
                    </div>

                    <!-- Carousel Inner -->
                    <div class="carousel-inner text-center">
                        <div class="carousel-item active">
                            <div class="hero-block-content">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <img class="d-block" src="{{ asset('mobile/img/bg-img/img-1.png') }}" alt="">
                                    <h2 class="display-4 text-white my-3">Apa itu Helpdesk Kota Blitar?</h2>
                                    <p class="text-white">Helpdesk Kota Blitar adalah layanan bantuan untuk menjawab
                                        pertanyaan dan menyelesaikan permasalahan masyarakat maupun ASN terkait layanan
                                        kota. Pengguna dapat mengajukan pertanyaan dengan akun untuk mendapatkan akses.</p>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="hero-block-content">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <img class="d-block" src="{{ asset('mobile/img/bg-img/img-2.png') }}" alt="">
                                    <h2 class="display-4 text-white my-3">Mengapa Helpdesk Kota Blitar?</h2>
                                    <p class="text-white">Kami hadir untuk memberikan solusi cepat, mudah, dan terpercaya
                                        guna memenuhi kebutuhan informasi serta mendukung kelancaran layanan bagi seluruh
                                        masyarakat dan ASN Kota Blitar.</p>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="hero-block-content">
                                <h2 class="display-4 text-white mb-3">Satu Langkah Lagi!</h2>
                                <p class="text-white">Segera mulai dengan Helpdesk Kota Blitar dan nikmati kemudahan akses
                                    layanan serta jawaban cepat untuk setiap pertanyaan Anda - solusi terbaik hanya dalam
                                    satu klik!</p>
                                <a class="btn btn-warning btn-lg w-100 mb-3" href="{{ route('dashboard.warga') }}">Mulai
                                    Sekarang</a>
                                <button class="btn btn-warning btn-lg w-100" @click="showForm = true">Lapor Tanpa
                                    Login</button>
                            </div>
                        </div>
                    </div>

                    <!-- Carousel Control Prev -->
                    <button class="carousel-control-prev" data-bs-target="#bootstrapCarouselFade" hidden type="button"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>

                    <!-- Carousel Control Next -->
                    <button class="carousel-control-next" data-bs-target="#bootstrapCarouselFade" hidden type="button"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Lapor Tanpa Login Form -->
        <div class="hero-block-wrapper bg-white" x-show="showForm" x-transition>
            <div class="container py-4">
                <h2 class="h3 fw-bold text-center text-dark mb-3">Lapor Tanpa Login</h2>
                <p class="text-center text-muted mb-4">Laporkan masalah Anda tanpa perlu login untuk layanan publik
                    tertentu.</p>

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
                                <div class="mb-3">
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="fas fa-search text-primary"></i>
                                        </span>
                                        <input x-model="search" type="text" class="form-control border-start-0"
                                            placeholder="Cari layanan..." aria-label="Cari layanan">
                                    </div>
                                    <div class="form-text" x-show="search !== ''">
                                        <i class="fas fa-info-circle me-1"></i> Pencarian akan menampilkan hasil dari semua
                                        unit
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
                                                                    <h5 class="m-0">{{ $service->svc_name }}</h5>
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
                                                    <h6 class="mb-2 border-bottom d-flex align-items-center">
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
                                                                            <h6 class="mb-1">{{ $service->svc_name }}
                                                                            </h6>
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
                                            name="guest_email" placeholder="Masukkan email"
                                            value="{{ old('guest_email') }}" required>
                                        @error('guest_email')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Judul Aduan</label>
                                        <input type="text" class="form-control form-control-clicked" id="title"
                                            name="title" placeholder="Masukkan judul aduan"
                                            value="{{ old('title') }}" required>
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
                                        <label for="imageBase64Input" class="form-label fw-bold">Unggah Gambar Pendukung
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
                                            class="btn btn-outline-secondary" aria-label="Kembali ke daftar layanan">
                                            <i class="fas fa-arrow-left me-2"></i>Kembali
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-paper-plane me-2"></i>Kirim Laporan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light text-center py-3">
                        <p class="text-muted mb-0 small">
                            <i class="fas fa-info-circle me-2"></i>Laporan Anda akan diverifikasi oleh tim kami sebelum
                            diproses lebih lanjut
                        </p>
                    </div>
                </div>
                <button type="button" @click="showForm = false" class="btn btn-outline-secondary w-100 mt-3">
                    <i class="bi bi-arrow-left me-2"></i>Kembali ke Beranda</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.0/dist/cdn.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
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
                        // Show success alert and hide form
                        $('.alert-success').removeClass('d-none').find('strong').text(
                            'Berhasil! Laporan Anda telah dikirim.');
                        setTimeout(() => {
                            window.location.reload(); // Refresh to show carousel
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
