@extends('layouts.app')

@section('title', 'Dashboard Warga')

@section('content')
<div id="page-container" class="sidebar-o sidebar-dark enable-page-overlay side-scroll page-header-fixed main-content-narrow">
    <main id="main-container">
        <div class="content">
            <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-center py-2 text-center text-md-start">
                <div class="flex-grow-1 mb-1 mb-md-0">
                    <h1 class="h3 fw-bold mb-2">Dashboard Warga</h1>
                    <h2 class="h6 fw-medium text-muted mb-0">
                        Selamat Datang, <a class="fw-semibold" href="javascript:void(0)">{{ Auth::user()->name }}</a>, 👋😊!
                    </h2>
                </div>
            </div>
        </div>

        <div class="content">
            <!-- Statistic Cards -->
            <div class="row items-push">
                <div class="col-sm-6 col-xl-3">
                    <div class="block block-rounded d-flex flex-column h-100 mb-0">
                        <div class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
                            <dl class="mb-0">
                                <dt class="fs-3 fw-bold" id="total-tickets">0</dt>
                                <dd class="fs-sm fw-medium text-muted mb-0">Total Aduan</dd>
                            </dl>
                            <div class="item item-rounded-lg bg-body-light">
                                <i class="fas fa-ticket-alt fs-3 text-primary"></i>
                            </div>
                        </div>
                        <div class="bg-body-light rounded-bottom"></div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="block block-rounded d-flex flex-column h-100 mb-0">
                        <div class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
                            <dl class="mb-0">
                                <dt class="fs-3 fw-bold" id="pending-tickets">0</dt>
                                <dd class="fs-sm fw-medium text-muted mb-0">Aduan Pending</dd>
                            </dl>
                            <div class="item item-rounded-lg bg-body-light">
                                <i class="fas fa-hourglass-start fs-3 text-warning"></i>
                            </div>
                        </div>
                        <div class="bg-body-light rounded-bottom"></div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="block block-rounded d-flex flex-column h-100 mb-0">
                        <div class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
                            <dl class="mb-0">
                                <dt class="fs-3 fw-bold" id="assigned-tickets">0</dt>
                                <dd class="fs-sm fw-medium text-muted mb-0">Aduan Ditugaskan</dd>
                            </dl>
                            <div class="item item-rounded-lg bg-body-light">
                                <i class="fas fa-user-clock fs-3 text-info"></i>
                            </div>
                        </div>
                        <div class="bg-body-light rounded-bottom"></div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="block block-rounded d-flex flex-column h-100 mb-0">
                        <div class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
                            <dl class="mb-0">
                                <dt class="fs-3 fw-bold" id="completed-tickets">0</dt>
                                <dd class="fs-sm fw-medium text-muted mb-0">Aduan Selesai</dd>
                            </dl>
                            <div class="item item-rounded-lg bg-body-light">
                                <i class="fas fa-check-circle fs-3 text-success"></i>
                            </div>
                        </div>
                        <div class="bg-body-light rounded-bottom"></div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="row">
                <!-- Ticket Stats Chart -->
                <div class="col-xl-8 col-xxl-8 d-flex flex-column">
                    <div class="block block-rounded flex-grow-1 d-flex flex-column">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Statistik Aduan</h3>
                            <div class="block-options">
                                <button type="button" class="btn-block-option" data-toggle="block-option" data-action="state_toggle" data-action-mode="demo">
                                    <i class="si si-refresh"></i>
                                </button>
                                <div class="dropdown d-inline-block" id="ticket-stats-time-range">
                                    <button type="button" class="btn-block-option dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-fw fa-calendar"></i>
                                        <span id="ticket-stats-time-label">Minggu</span>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="javascript:void(0)" data-time-range="day">Hari</a>
                                        <a class="dropdown-item active" href="javascript:void(0)" data-time-range="week">Minggu</a>
                                        <a class="dropdown-item" href="javascript:void(0)" data-time-range="month">Bulan</a>
                                        <a class="dropdown-item" href="javascript:void(0)" data-time-range="year">Tahun</a>
                                        <a class="dropdown-item" href="javascript:void(0)" data-time-range="10year">10 Tahun</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="javascript:void(0)" id="ticket-stats-custom-range">Kustom</a>
                                    </div>
                                </div>
                                <input type="text" id="ticket-stats-date-picker" class="d-none" placeholder="Pilih Rentang Tanggal">
                            </div>
                        </div>
                        <div class="block-content block-content-full flex-grow-1 d-flex align-items-center">
                            <canvas id="ticketStatsChart" style="width: 100%; height: 300px;"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Ticket Distribution Chart -->
                <div class="col-xl-4 col-xxl-4 d-flex flex-column">
                    <div class="block block-rounded flex-grow-1 d-flex flex-column">
                        <div class="block-header block-header-default">
                            <h3 class="block-title fs-sm text-uppercase">Distribusi Status Aduan</h3>
                            <div class="block-options">
                                <button type="button" class="btn-block-option" data-toggle="block-option" data-action="state_toggle" data-action-mode="demo">
                                    <i class="si si-refresh"></i>
                                </button>
                            </div>
                        </div>
                        <div class="block-content block-content-full flex-grow-1 d-flex align-items-center justify-content-center">
                            <div style="position: relative; width: 100%; max-width: 240px; height: 240px;">
                                <canvas id="ticketDistributionChart"></canvas>
                            </div>
                        </div>
                        <div class="block-content bg-body-light pt-2 pb-3">
                            <div class="row items-push text-center w-100">
                                <div class="col-4">
                                    <div class="fs-xs fw-semibold text-uppercase text-muted mb-1">Pending</div>
                                    <div class="fs-4 fw-bold text-warning" id="pending-percent">0%</div>
                                </div>
                                <div class="col-4">
                                    <div class="fs-xs fw-semibold text-uppercase text-muted mb-1">Ditugaskan</div>
                                    <div class="fs-4 fw-bold text-info" id="assigned-percent">0%</div>
                                </div>
                                <div class="col-4">
                                    <div class="fs-xs fw-semibold text-uppercase text-muted mb-1">Selesai</div>
                                    <div class="fs-4 fw-bold text-success" id="completed-percent">0%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ticket List -->
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Daftar Aduan</h3>
                    <div class="block-options space-x-1">
                        <div class="dropdown d-inline-block">
                            <button type="button" class="btn btn-sm btn-alt-secondary" id="dropdown-recent-orders-filters" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-fw fa-flask"></i> Filter Status
                                <i class="fa fa-angle-down ms-1"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-md dropdown-menu-end fs-sm" aria-labelledby="dropdown-recent-orders-filters">
                                <a class="dropdown-item fw-medium d-flex align-items-center justify-content-between" href="javascript:void(0)" data-status="belum">Pending</a>
                                <a class="dropdown-item fw-medium d-flex align-items-center justify-content-between" href="javascript:void(0)" data-status="direspon">Direspon</a>
                                <a class="dropdown-item fw-medium d-flex align-items-center justify-content-between" href="javascript:void(0)" data-status="selesai">Selesai</a>
                                <a class="dropdown-item fw-medium d-flex align-items-center justify-content-between" href="javascript:void(0)" data-status="semua">Tampilkan Semua</a>
                            </div>
                        </div>
                        <a class="btn btn-sm btn-alt-primary" href="{{ route('tickets.create') }}">
                            <i class="fa fa-plus opacity-50 me-1"></i> Buat Aduan
                        </a>
                    </div>
                </div>
                <div class="block-content block-content-full">
                    <div class="table-responsive">
                        <table class="table table-borderless table-striped table-vcenter fs-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Kode Tiket</th>
                                    <th>Layanan</th>
                                    <th>Judul</th>
                                    <th>Deskripsi</th>
                                    <th>Pengadu</th>
                                    <th style="text-align: center;">Status</th>
                                    <th style="text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="ticket-list-table">
                                <tr>
                                    <td colspan="8" class="text-center">
                                        <div class="alert alert-info m-0">
                                            Memuat data aduan...
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-4" id="paginationLinks"></div>
                </div>
                <div class="block-content block-content-full bg-body-light">
                    <div class="d-flex justify-content-between">
                        <a class="btn btn-sm btn-alt-secondary" href="{{ route('tickets.index') }}">
                            <i class="fa fa-eye opacity-50 me-1"></i> Lihat Semua
                        </a>
                    </div>
                </div>
            </div>

            <!-- Modal Template -->
            <template id="ticketDetailModalTemplate">
                <div class="modal fade bg-dark" id="detailModal-ID_PLACEHOLDER" tabindex="-1" aria-labelledby="detailModalLabel-ID_PLACEHOLDER" data-bs-backdrop="static" aria-hidden="true" style="font-size: 12px;">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="detailModalLabel-ID_PLACEHOLDER">Detail Tiket: <span id="modal-ticket-code-ID_PLACEHOLDER"></span></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Waktu Dibuat:</strong> <span id="modal-created-at-ID_PLACEHOLDER"></span></p>
                                <p><strong>Kode Tiket:</strong> <span id="modal-ticket-code-ID_PLACEHOLDER"></span></p>
                                <p><strong>Judul:</strong> <span id="modal-title-ID_PLACEHOLDER"></span></p>
                                <p><strong>Status:</strong>
                                    <span id="modal-status-ID_PLACEHOLDER" class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill"></span>
                                </p>
                                <p><strong>Layanan:</strong> <span id="modal-svc-name-ID_PLACEHOLDER"></span></p>
                                <p><strong>Deskripsi:</strong> <span id="modal-description-ID_PLACEHOLDER"></span></p>
                                <p><strong>Unit Saat Ini:</strong> <span id="modal-unit-name-ID_PLACEHOLDER"></span></p>
                                <div class="mt-3">
                                    <strong>Lokasi Aduan:</strong>
                                    <div class="d-flex flex-column gap-2 mt-2" id="modal-location-ID_PLACEHOLDER"></div>
                                </div>
                                <div class="mt-3">
                                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#chatModal-ID_PLACEHOLDER" title="Pesan">
                                        <i class="fas fa-comments"></i> Pesan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Chat Modal Template -->
            <template id="chatModalTemplate">
                <div class="modal fade" id="chatModal-ID_PLACEHOLDER" tabindex="-1" aria-labelledby="chatModalLabel-ID_PLACEHOLDER" data-bs-backdrop="static" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content shadow-lg" style="border-radius: 12px; overflow: hidden;">
                            <div class="modal-header d-flex justify-content-between align-items-center py-3 px-4" style="background-color: #ffffff; border-bottom: 1px solid #e5e7eb;">
                                <h5 class="modal-title d-flex align-items-center gap-2 m-0" id="chatModalLabel-ID_PLACEHOLDER">
                                    <i class="fas fa-ticket-alt" style="color: #2563eb;"></i>
                                    <span style="font-weight: 600; font-size: 1rem; color: #1f2937;">Tiket ID_PLACEHOLDER</span>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-0" style="display: flex; flex-direction: column; max-height: 70vh;">
                                <div class="chat-container" id="chat-container-ID_PLACEHOLDER" style="flex: 1; overflow-y: auto; padding: 1.25rem; background-color: #f9fafb;">
                                    <p class="text-muted text-center">Belum ada percakapan untuk tiket ini.</p>
                                </div>
                                <div class="reply-container" style="background-color: white; border-top: 1px solid #e5e7eb; padding: 1rem;">
                                    <form id="reply-form-ID_PLACEHOLDER" action="/tickets/reply/ID_PLACEHOLDER" method="POST" enctype="multipart/form-data" class="reply-form">
                                        @csrf
                                        <div class="reply-input-row d-flex gap-2 mb-2">
                                            <textarea class="form-control" name="message" placeholder="Ketik pesan Anda di sini..." required style="border-radius: 24px; border-color: #d1d5db; padding: 12px 16px; font-size: 0.9rem; resize: none;"></textarea>
                                            <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center" style="border-radius: 50%; width: 44px; height: 44px; padding: 0;">
                                                <i class="fas fa-paper-plane"></i>
                                            </button>
                                        </div>
                                        <div class="attachment-row d-flex align-items-center gap-2">
                                            <button class="btn btn-outline-primary" type="button" id="custom-button-ID_PLACEHOLDER" style="border-radius: 20px; padding: 6px 14px; font-size: 0.85rem; display: flex; align-items: center; gap: 6px;">
                                                <i class="fas fa-paperclip"></i>
                                                <span>Lampirkan File</span>
                                            </button>
                                            <span id="file-name-ID_PLACEHOLDER" class="text-muted" style="font-size: 0.85rem; flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Tidak ada file dipilih</span>
                                            <input type="file" name="images[]" id="images-ID_PLACEHOLDER" multiple class="form-control d-none">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </main>
</div>

<style>
    .block {
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    .block-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        border-radius: 8px 8px 0 0;
        padding: 1rem 1.5rem;
    }
    .block-title {
        color: #343a40;
        font-weight: 600;
        font-size: 1.1rem;
    }
    .block-content {
        padding: 1.5rem;
        background: #fff;
    }
    .block-content.bg-body-light {
        background: #f8f9fa;
        border-radius: 0 0 8px 8px;
    }
    .btn-alt-primary {
        background-color: #4361EE;
        color: #fff;
        border: none;
        transition: background-color 0.3s ease;
    }
    .btn-alt-primary:hover {
        background-color: #3347b0;
    }
    .btn-alt-secondary {
        background-color: #6c757d;
        color: #fff;
        border: none;
        transition: background-color 0.3s ease;
    }
    .btn-alt-secondary:hover {
        background-color: #5a6268;
    }
    canvas {
        max-height: 300px !important;
    }
    /* Modal styling */
    .modal-content {
        border-radius: 8px;
        font-size: 12px;
    }
    .modal-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
    }
    .modal-body {
        padding: 1.5rem;
    }
    .bg-warning-light { background-color: #fff3cd; }
    .bg-info-light { background-color: #cce5ff; }
    .bg-success-light { background-color: #d4edda; }
    .text-warning { color: #856404; }
    .text-info { color: #0c5460; }
    .text-success { color: #155724; }
    .bg-secondary-light {
        background-color: #e5e7eb;
    }
    .text-secondary {
        color: #6b7280;
    }
    .pagination .page-item .page-link {
        color: #007bff;
        background-color: #fff;
        border: 1px solid #dee2e6;
        margin: 0 2px;
    }
    .pagination .page-item.active .page-link {
        color: #fff;
        background-color: #007bff;
        border-color: #007bff;
    }
    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #fff;
        border-color: #dee2e6;
    }
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .block-content {
            padding: 1rem;
        }
        .block-title {
            font-size: 1rem;
        }
    }
    @media (max-width: 480px) {
        .block-content {
            padding: 0.75rem;
        }
        .block-title {
            font-size: 0.9rem;
        }
    }
</style>
@endsection

@section('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="{{ asset('assets/js/warga_dashboard.js') }}"></script>
@endsection