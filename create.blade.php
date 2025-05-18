<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Layanan</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --light-bg: #f8f9fa;
            --dark-bg: #212529;
        }
        
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08);
        }
        
        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.5rem;
            font-weight: 600;
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        .form-control, .form-select {
            padding: 0.75rem;
            border-radius: 6px;
            border: 1px solid #dee2e6;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        
        .input-group-text {
            background-color: #e9ecef;
            border: 1px solid #dee2e6;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
        }
        
        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
            transform: translateY(-1px);
        }
        
        .btn-outline-secondary {
            color: var(--secondary-color);
            border-color: var(--secondary-color);
            padding: 0.75rem 1.5rem;
            font-weight: 500;
        }
        
        .btn-outline-secondary:hover {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .form-text {
            color: #6c757d;
            font-size: 0.875rem;
        }
        
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .alert {
            border-radius: 6px;
            font-weight: 500;
        }
        
        .file-upload {
            position: relative;
            overflow: hidden;
            margin: 10px 0;
            border: 2px dashed #ddd;
            border-radius: 5px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .file-upload:hover {
            border-color: var(--primary-color);
        }
        
        .file-upload input[type=file] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .file-upload-label {
            display: block;
            color: #6c757d;
            font-weight: 500;
            margin-bottom: 0;
        }
        
        .file-upload i {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Tambah Layanan</h4>
                    </div>
                    <div class="card-body p-4">
                        
                        <!-- Alert for errors -->
                        @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <h5 class="alert-heading"><i class="bi bi-exclamation-triangle-fill me-2"></i>Terjadi Kesalahan!</h5>
                            <ul class="list-unstyled mb-0">
                                @foreach ($errors->all() as $error)
                                    <li><i class="bi bi-dot me-2"></i>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        
                        <form action="{{ route('services.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <!-- Unit Kerja -->
                                <div class="col-md-6 mb-4">
                                    <label for="unit_id" class="form-label">Unit Kerja <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-building"></i></span>
                                        <select id="unit_id" name="unit_id" class="form-select" required>
                                            @foreach($units as $unit)
                                                <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Status -->
                                <div class="col-md-6 mb-4">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-toggle-on"></i></span>
                                        <select id="status" name="status" class="form-select" required>
                                            <option value="active" selected>Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Nama Layanan -->
                            <div class="mb-4">
                                <label for="svc_name" class="form-label">Nama Layanan <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                    <input type="text" id="svc_name" name="svc_name" class="form-control" placeholder="Masukkan nama layanan" required>
                                </div>
                            </div>
                            
                            <!-- Kategori -->
                            <div class="mb-4">
                                <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-grid"></i></span>
                                    <select id="category_id" name="category_id" class="form-select" required>
                                        <option value="1" selected>Pemerintah</option>
                                        <option value="2">Publik</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Deskripsi -->
                            <div class="mb-4">
                                <label for="svc_desc" class="form-label">Deskripsi</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                    <textarea id="svc_desc" name="svc_desc" class="form-control" rows="3" placeholder="Deskripsi singkat tentang layanan ini (opsional)"></textarea>
                                </div>
                            </div>
                            
                            <!-- Ikon Layanan -->
                            <div class="mb-4">
                                <label for="svc_icon" class="form-label">Ikon Layanan</label>
                                <div class="file-upload">
                                    <input type="file" id="svc_icon" name="svc_icon" accept="image/*" class="file-input">
                                    <i class="bi bi-cloud-arrow-up"></i>
                                    <p class="file-upload-label">Pilih file atau tarik & letakkan di sini</p>
                                    <p class="form-text">Format gambar yang didukung: JPG, PNG, SVG (Maks. 2MB)</p>
                                </div>
                            </div>
                            
                            <!-- Izinkan Tamu -->
                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="allow_guest" name="allow_guest" value="1">
                                    <label class="form-check-label" for="allow_guest">Izinkan Tamu Membuat Laporan</label>
                                </div>
                                <div class="form-text ms-4">Aktifkan jika kategori Publik membutuhkan akses tamu untuk membuat laporan</div>
                                <div class="form-text ms-4">QR Code akan tersedia setelah menyimpan layanan</div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <!-- Form Actions -->
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('services.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-2"></i>Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Display filename when file is selected
        document.getElementById('svc_icon').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'Pilih file atau tarik & letakkan di sini';
            const label = this.closest('.file-upload').querySelector('.file-upload-label');
            label.textContent = fileName;
        });
        
        // Show/hide allow_guest option based on category selection
        document.getElementById('category_id').addEventListener('change', function() {
            const allowGuestContainer = document.querySelector('.form-switch');
            if (this.value === '2') { // Public category
                allowGuestContainer.classList.add('opacity-100');
                allowGuestContainer.classList.remove('opacity-50');
            } else {
                allowGuestContainer.classList.add('opacity-50');
                allowGuestContainer.classList.remove('opacity-100');
                document.getElementById('allow_guest').checked = false;
            }
        });
    </script>
</body>
</html>