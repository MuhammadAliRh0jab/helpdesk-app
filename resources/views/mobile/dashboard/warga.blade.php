@extends('mobile.master.app')

@section('title', 'Dashboard')

@section('header')
    @include('mobile.master.header')
@endsection

@section('sidenav')
    @include('mobile.master.sidenav')
@endsection

@section('content')
    <div class="page-content-wrapper py-3">
        <div class="container">
            <div class="card bg-primary my-3 bg-img shadow"
                style="background-image: url('{{ asset('mobile/img/core-img/1.png') }}')">
                <div class="card-body p-4">
                    <h2 class="text-white">Dashboard Warga</h2>
                    <h5 class="my-2 text-white">Selamat Datang, {{ Auth::user()->name }}! 👋😊</h5>
                    <small class="text-white">Helpdesk Kota Blitar | Sistem Pengaduan Kota Blitar</small>
                </div>
            </div>
        </div>

        <!-- Statistic Cards -->
        <div class="container">
            <div class="row g-3">
                <div class="col-6">
                    <div class="card shadow h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0 text-muted">Jumlah Aduan</h6>
                                <h4 class="fw-bold mb-0" id="total-tickets">0</h4>
                            </div>
                            <div class="ps-3">
                                <i class="fa fa-ticket-alt fs-3 text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card shadow h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0 text-muted">Aduan Pending</h6>
                                <h4 class="fw-bold mb-0" id="pending-tickets">0</h4>
                            </div>
                            <div class="ps-3">
                                <i class="fa fa-hourglass-start fs-3 text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card shadow h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0 text-muted">Aduan Ditugaskan</h6>
                                <h4 class="fw-bold mb-0" id="assigned-tickets">0</h4>
                            </div>
                            <div class="ps-3">
                                <i class="fa fa-user-clock fs-3 text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card shadow h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0 text-muted">Aduan Selesai</h6>
                                <h4 class="fw-bold mb-0" id="completed-tickets">0</h4>
                            </div>
                            <div class="ps-3">
                                <i class="fa fa-check-circle fs-3 text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="container mt-4">
            <div class="row g-3">
                <!-- Ticket Stats Chart -->
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title m-0">Statistik Aduan</h5>
                                <div class="dropdown" id="ticket-stats-time-range">
                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-calendar me-1"></i> Rentang
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="javascript:void(0)"
                                                data-time-range="day">Hari</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0)"
                                                data-time-range="week">Minggu</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0)"
                                                data-time-range="month">Bulan</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0)"
                                                data-time-range="year">Tahun</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0)" data-time-range="10year">10
                                                Tahun</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="javascript:void(0)"
                                                id="ticket-stats-custom-range">Kustom</a></li>
                                    </ul>
                                </div>
                            </div>
                            <input type="text" id="ticket-stats-date-picker" class="d-none"
                                placeholder="Pilih Rentang Tanggal">
                            <canvas id="ticketStatsChart" style="width: 100%; height: 200px;"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Ticket Distribution Chart -->
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-body">
                            <h5 class="card-title text-center mb-4">Distribusi Status Aduan</h5>
                            <div class="d-flex justify-content-center">
                                <div style="position: relative; width: 100%; max-width: 200px; height: 200px;">
                                    <canvas id="ticketDistributionChart"></canvas>
                                </div>
                            </div>
                            <div class="row text-center mt-3">
                                <div class="col-4 px-1">
                                    <div class="fs-6 fw-semibold text-uppercase text-muted mb-1">Pending</div>
                                    <div class="fs-4 fw-bold text-warning" id="pending-percent">0%</div>
                                </div>
                                <div class="col-4 px-1">
                                    <div class="fs-6 fw-semibold text-uppercase text-muted mb-1">Ditugaskan</div>
                                    <div class="fs-4 fw-bold text-info" id="assigned-percent">0%</div>
                                </div>
                                <div class="col-4 px-1">
                                    <div class="fs-6 fw-semibold text-uppercase text-muted mb-1">Selesai</div>
                                    <div class="fs-4 fw-bold text-success" id="completed-percent">0%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ticket List -->
        <div class="container mt-4">
            <div class="card shadow">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="card-title m-0">Tiket Terbaru</h5>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-filter me-1"></i>
                                Filter Status
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="javascript:void(0)" data-status="semua">Tampilkan
                                        Semua</a></li>
                                <li><a class="dropdown-item" href="javascript:void(0)" data-status="belum">Pending</a>
                                </li>
                                <li><a class="dropdown-item" href="javascript:void(0)"
                                        data-status="direspon">Direspon</a></li>
                                <li><a class="dropdown-item" href="javascript:void(0)" data-status="selesai">Selesai</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle text-center mb-0"
                            id="ticket-list-table">
                            <thead>
                                <tr class="text-nowrap">
                                    <th>No</th>
                                    <th>Kode Tiket</th>
                                    <th>Judul</th>
                                    <th>Layanan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" class="text-center">
                                        <div class="alert alert-info m-0">
                                            Memuat data aduan...
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <a class="btn btn-sm btn-outline-info" href="{{ route('tickets.index') }}">
                            <i class="fa fa-eye me-1"></i> Lihat Semua
                        </a>
                        <a class="btn btn-sm btn-primary" href="{{ route('tickets.create') }}">
                            <i class="fa fa-plus me-1"></i> Buat Aduan
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Template -->
        <template id="ticketDetailModalTemplate">
            <div class="modal fade" id="detailModal-ID_PLACEHOLDER" tabindex="-1"
                aria-labelledby="detailModalLabel-ID_PLACEHOLDER" data-bs-backdrop="static" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="detailModalLabel-ID_PLACEHOLDER">Detail Tiket: <span
                                    id="modal-ticket-code-ID_PLACEHOLDER"></span></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p><strong>Waktu Dibuat:</strong> <span id="modal-created-at-ID_PLACEHOLDER"></span></p>
                            <p><strong>Kode Tiket:</strong> <span id="modal-ticket-code-ID_PLACEHOLDER"></span></p>
                            <p><strong>Judul:</strong> <span id="modal-title-ID_PLACEHOLDER"></span></p>
                            <p><strong>Status:</strong>
                                <span id="modal-status-ID_PLACEHOLDER"
                                    class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill"></span>
                            </p>
                            <p><strong>Layanan:</strong> <span id="modal-svc-name-ID_PLACEHOLDER"></span></p>
                            <p><strong>Deskripsi:</strong> <span id="modal-description-ID_PLACEHOLDER"></span></p>
                            <p><strong>Unit Asal:</strong> <span id="modal-original-unit-ID_PLACEHOLDER"></span></p>
                            <p><strong>Unit Saat Ini:</strong> <span id="modal-unit-name-ID_PLACEHOLDER"></span></p>
                            <div class="mt-3">
                                <strong>Lokasi Aduan:</strong>
                                <div class="d-flex flex-column gap-2 mt-2" id="modal-location-ID_PLACEHOLDER"></div>
                            </div>
                            <div class="mt-3">
                                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#chatModal-ID_PLACEHOLDER" title="Pesan">
                                    <i class="fas fa-comments"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- Chat Modal Template -->
        <template id="chatModalTemplate">
            <div class="modal fade" id="chatModal-ID_PLACEHOLDER" tabindex="-1"
                aria-labelledby="chatModalLabel-ID_PLACEHOLDER" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="chatModalLabel-ID_PLACEHOLDER">Pesan untuk Tiket: <span
                                    id="chat-ticket-code-ID_PLACEHOLDER"></span></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Chat functionality to be implemented.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
@endsection

@section('footer')
    @include('mobile.master.footer')
@endsection
