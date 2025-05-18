@extends('layouts.app')

@section('title', 'Dashboard Pegawai')

@section('content')
    <div id="page-container"
        class="sidebar-o sidebar-dark enable-page-overlay side-scroll page-header-fixed main-content-narrow">
        <main id="main-container">
            <div class="content">
                <div
                    class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-center py-2 text-center text-md-start">
                    <div class="flex-grow-1 mb-1 mb-md-0">
                        <h1 class="h3 fw-bold mb-2">Dashboard Pegawai</h1>
                        <h2 class="h6 fw-medium text-muted mb-0">Selamat Datang, <a class="fw-semibold"
                                href="javascript:void(0)">{{ Auth::user()->name }}</a>, 👋😊!</h2>
                    </div>
                </div>
            </div>

            <div class="content">
                <!-- Statistics Cards -->
                <div class="row items-push">
                    <div class="col-sm-6 col-xl-2">
                        <div class="block block-rounded d-flex flex-column h-100 mb-0">
                            <div
                                class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
                                <dl class="mb-0">
                                    <dt class="fs-3 fw-bold" id="resolved-as-handler-tickets">0</dt>
                                    <dd class="fs-sm fw-medium text-muted mb-0">Diselesaikan (Pelaksana)</dd>
                                </dl>
                                <div class="item item-rounded-lg bg-body-light">
                                    <i class="fas fa-check-circle fs-3 text-success"></i>
                                </div>
                            </div>
                            <div class="bg-body-light rounded-bottom"></div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-2">
                        <div class="block block-rounded d-flex flex-column h-100 mb-0">
                            <div
                                class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
                                <dl class="mb-0">
                                    <dt class="fs-3 fw-bold" id="to-be-completed-as-handler-tickets">0</dt>
                                    <dd class="fs-sm fw-medium text-muted mb-0">Harus Diselesaikan (Pelaksana)</dd>
                                </dl>
                                <div class="item item-rounded-lg bg-body-light">
                                    <i class="fas fa-user-clock fs-3 text-info"></i>
                                </div>
                            </div>
                            <div class="bg-body-light rounded-bottom"></div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-2">
                        <div class="block block-rounded d-flex flex-column h-100 mb-0">
                            <div
                                class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
                                <dl class="mb-0">
                                    <dt class="fs-3 fw-bold" id="pending-as-creator-tickets">0</dt>
                                    <dd class="fs-sm fw-medium text-muted mb-0">Pending (Pengadu)</dd>
                                </dl>
                                <div class="item item-rounded-lg bg-body-light">
                                    <i class="fas fa-hourglass-start fs-3 text-warning"></i>
                                </div>
                            </div>
                            <div class="bg-body-light rounded-bottom"></div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-2">
                        <div class="block block-rounded d-flex flex-column h-100 mb-0">
                            <div
                                class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
                                <dl class="mb-0">
                                    <dt class="fs-3 fw-bold" id="assigned-as-creator-tickets">0</dt>
                                    <dd class="fs-sm fw-medium text-muted mb-0">Ditugaskan (Pengadu)</dd>
                                </dl>
                                <div class="item item-rounded-lg bg-body-light">
                                    <i class="fas fa-ticket-alt fs-3 text-primary"></i>
                                </div>
                            </div>
                            <div class="bg-body-light rounded-bottom"></div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-2">
                        <div class="block block-rounded d-flex flex-column h-100 mb-0">
                            <div
                                class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
                                <dl class="mb-0">
                                    <dt class="fs-3 fw-bold" id="completed-as-creator-tickets">0</dt>
                                    <dd class="fs-sm fw-medium text-muted mb-0">Selesai (Pengadu)</dd>
                                </dl>
                                <div class="item item-rounded-lg bg-body-light">
                                    <i class="fas fa-check-double fs-3 text-success"></i>
                                </div>
                            </div>
                            <div class="bg-body-light rounded-bottom"></div>
                        </div>
                    </div>
                </div>

                <!-- Tabbed Dashboard -->
                <div class="row">
                    <div class="col-12">
                        <div class="block block-rounded">
                            <div class="block-header block-header-default">
                                <ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="creator-tab" data-bs-toggle="tab" href="#creator"
                                            role="tab">Sebagai Pembuat Tiket</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="handler-tab" data-bs-toggle="tab" href="#handler"
                                            role="tab">Sebagai Pelaksana Tiket</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="block-content tab-content" id="dashboardTabContent">
                                <!-- Tab 1: Sebagai Pembuat Tiket -->
                                <div class="tab-pane fade show active" id="creator" role="tabpanel">
                                    <div class="row">
                                        <!-- Ticket Stats Chart -->
                                        <div class="col-xl-8 col-xxl-8 d-flex flex-column">
                                            <div class="block block-rounded flex-grow-1 d-flex flex-column">
                                                <div class="block-header block-header-default">
                                                    <h3 class="block-title">Statistik Tiket yang Dibuat</h3>
                                                    <div class="block-options">
                                                        <button type="button" class="btn-block-option"
                                                            data-toggle="block-option" data-action="state_toggle"
                                                            data-action-mode="demo">
                                                            <i class="si si-refresh"></i>
                                                        </button>
                                                        <div class="dropdown d-inline-block" id="ticket-stats-time-range">
                                                            <button type="button"
                                                                class="btn-block-option dropdown-toggle"
                                                                data-bs-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">
                                                                <i class="fa fa-fw fa-calendar"></i>
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-end">
                                                                <a class="dropdown-item" href="javascript:void(0)"
                                                                    data-time-range="day">Hari</a>
                                                                <a class="dropdown-item active" href="javascript:void(0)"
                                                                    data-time-range="week">Minggu</a>
                                                                <a class="dropdown-item" href="javascript:void(0)"
                                                                    data-time-range="month">Bulan</a>
                                                                <a class="dropdown-item" href="javascript:void(0)"
                                                                    data-time-range="year">Tahun</a>
                                                                <a class="dropdown-item" href="javascript:void(0)"
                                                                    data-time-range="10year">10 Tahun</a>
                                                                <div class="dropdown-divider"></div>
                                                                <a class="dropdown-item" href="javascript:void(0)"
                                                                    id="ticket-stats-custom-range">Kustom</a>
                                                            </div>
                                                        </div>
                                                        <input type="text" id="ticket-stats-date-picker"
                                                            class="d-none" placeholder="Pilih Rentang Tanggal">
                                                    </div>
                                                </div>
                                                <div
                                                    class="block-content block-content-full flex-grow-1 d-flex align-items-center">
                                                    <canvas id="ticketStatsChart"
                                                        style="width: 100%; height: 300px;"></canvas>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Ticket Distribution By Status -->
                                        <div class="col-xl-4 col-xxl-4 d-flex flex-column">
                                            <div class="block block-rounded flex-grow-1 d-flex flex-column">
                                                <div class="block-header block-header-default">
                                                    <h3 class="block-title fs-sm text-uppercase">Distribusi Status Tiket
                                                        (Pembuat)</h3>
                                                    <div class="block-options">
                                                        <button type="button" class="btn-block-option"
                                                            data-toggle="block-option" data-action="state_toggle"
                                                            data-action-mode="demo">
                                                            <i class="si si-refresh"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div
                                                    class="block-content block-content-full flex-grow-1 d-flex align-items-center justify-content-center">
                                                    <div
                                                        style="position: relative; width: 100%; max-width: 240px; height: 240px;">
                                                        <canvas id="ticketDistributionChart"></canvas>
                                                    </div>
                                                </div>
                                                <div class="block-content bg-body-light pt-2 pb-3">
                                                    <div class="row items-push text-center w-100">
                                                        <div class="col-4">
                                                            <div class="fs-xs fw-semibold text-uppercase text-muted mb-1">
                                                                Pending</div>
                                                            <div class="fs-4 fw-bold text-warning" id="pending-percent">0%
                                                            </div>
                                                        </div>
                                                        <div class="col-4">
                                                            <div class="fs-xs fw-semibold text-uppercase text-muted mb-1">
                                                                Ditugaskan</div>
                                                            <div class="fs-4 fw-bold text-primary" id="assigned-percent">
                                                                0%</div>
                                                        </div>
                                                        <div class="col-4">
                                                            <div class="fs-xs fw-semibold text-uppercase text-muted mb-1">
                                                                Selesai</div>
                                                            <div class="fs-4 fw-bold text-success" id="completed-percent">
                                                                0%</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Recent Tickets Created -->
                                    <div class="row">
                                        <div class="col-xl-12 col-xxl-12">
                                            <div class="block block-rounded">
                                                <div class="block-header block-header-default">
                                                    <h3 class="block-title">Tiket Terbaru</h3>
                                                    <div class="block-options">
                                                        <button type="button" class="btn-block-option"
                                                            data-toggle="block-option" data-action="state_toggle"
                                                            data-action-mode="demo" onclick="loadRecentTickets()">
                                                            <i class="si si-refresh"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="block-content">
                                                    <table
                                                        class="table table-borderless table-striped table-vcenter fs-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>Kode</th>
                                                                <th>Judul</th>
                                                                <th>Tanggal Dibuat</th>
                                                                <th>Unit</th>
                                                                <th class="text-center" style="width: 100px;">Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="recent-tickets-table">
                                                            <!-- Will be populated by JS -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="block-content block-content-full bg-body-light">
                                                    <div class="d-flex justify-content-between">
                                                        <a class="btn btn-sm btn-alt-secondary"
                                                            href="{{ route('tickets.index') }}">
                                                            <i class="fa fa-eye opacity-50 me-1"></i> Lihat Semua
                                                        </a>
                                                        <a class="btn btn-sm btn-alt-primary"
                                                            href="{{ route('tickets.create') }}">
                                                            <i class="fa fa-plus opacity-50 me-1"></i> Buat Tiket
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab 2: Sebagai Pelaksana Tiket -->
                                <div class="tab-pane fade" id="handler" role="tabpanel">
                                    <div class="row">
                                        <!-- Top Left: Pie Chart (Tickets to be Completed and Completed) -->
                                        <div class="col-md-6">
                                            <div class="block block-rounded">
                                                <div class="block-header block-header-default">
                                                    <h3 class="block-title">Distribusi Status Tiket</h3>
                                                    <div class="block-options">
                                                        <button type="button" class="btn-block-option"
                                                            data-toggle="block-option" data-action="state_toggle"
                                                            data-action-mode="demo"
                                                            onclick="setupHandlerTicketDistributionChart()">
                                                            <i class="si si-refresh"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="block-content block-content-full">
                                                    <div
                                                        style="position: relative; width: 100%; max-width: 240px; height: 240px; margin: 0 auto;">
                                                        <canvas id="handlerTicketDistributionChart"></canvas>
                                                    </div>
                                                </div>
                                                <div class="block-content bg-body-light pt-2 pb-3">
                                                    <div class="row items-push text-center">
                                                        <div class="col-6">
                                                            <div class="fs-xs fw-semibold text-uppercase text-muted mb-1">
                                                                Belum Selesai</div>
                                                            <div class="fs-4 fw-bold text-warning"
                                                                id="handler-pending-percent">0%</div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="fs-xs fw-semibold text-uppercase text-muted mb-1">
                                                                Selesai</div>
                                                            <div class="fs-4 fw-bold text-success"
                                                                id="handler-completed-percent">0%</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Top Right: Average Resolution Time per Service -->
                                        <div class="col-md-6">
                                            <div class="block block-rounded">
                                                <div class="block-header block-header-default">
                                                    <h3 class="block-title">Rata-rata Waktu Penyelesaian per Service</h3>
                                                    <div class="block-options">
                                                        <button type="button" class="btn-block-option"
                                                            data-toggle="block-option" data-action="state_toggle"
                                                            data-action-mode="demo"
                                                            onclick="setupResolutionByServiceChart()">
                                                            <i class="si si-refresh"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="block-content block-content-full">
                                                    <canvas id="resolutionByServiceChart" style="height: 240px;"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <!-- Middle Long: Time Series of Assignment and Completion -->
                                        <div class="col-12">
                                            <div class="block block-rounded">
                                                <div class="block-header block-header-default">
                                                    <h3 class="block-title">Riwayat Penugasan dan Penyelesaian</h3>
                                                    <div class="block-options">
                                                        <button type="button" class="btn-block-option"
                                                            data-toggle="block-option" data-action="state_toggle"
                                                            data-action-mode="demo">
                                                            <i class="si si-refresh"></i>
                                                        </button>
                                                        <div class="dropdown d-inline-block"
                                                            id="assignment-completion-time-range">
                                                            <button type="button"
                                                                class="btn-block-option dropdown-toggle"
                                                                data-bs-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">
                                                                <i class="fa fa-fw fa-calendar"></i>
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-end">
                                                                <a class="dropdown-item" href="javascript:void(0)"
                                                                    data-time-range="day">Hari</a>
                                                                <a class="dropdown-item active" href="javascript:void(0)"
                                                                    data-time-range="week">Minggu</a>
                                                                <a class="dropdown-item" href="javascript:void(0)"
                                                                    data-time-range="month">Bulan</a>
                                                                <a class="dropdown-item" href="javascript:void(0)"
                                                                    data-time-range="year">Tahun</a>
                                                                <a class="dropdown-item" href="javascript:void(0)"
                                                                    data-time-range="10year">10 Tahun</a>
                                                                <div class="dropdown-divider"></div>
                                                                <a class="dropdown-item" href="javascript:void(0)"
                                                                    id="assignment-completion-custom-range">Kustom</a>
                                                            </div>
                                                        </div>
                                                        <input type="text" id="assignment-completion-date-picker"
                                                            class="d-none" placeholder="Pilih Rentang Tanggal">
                                                    </div>
                                                </div>
                                                <div class="block-content block-content-full">
                                                    <canvas id="assignmentCompletionChart"
                                                        style="height: 300px;"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <!-- Bottom: Table of Tickets to be Completed and Completed -->
                                        <div class="col-12">
                                            <div class="block block-rounded">
                                                <div class="block-header block-header-default">
                                                    <h3 class="block-title">Daftar Tiket</h3>
                                                    <div class="block-options">
                                                        <button type="button" class="btn-block-option"
                                                            data-toggle="block-option" data-action="state_toggle"
                                                            data-action-mode="demo" onclick="loadTicketList()">
                                                            <i class="si si-refresh"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="block-content">
                                                    <table
                                                        class="table table-borderless table-striped table-vcenter fs-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>Kode</th>
                                                                <th>Judul</th>
                                                                <th>Status</th>
                                                                <th>Tanggal Dibuat</th>
                                                                <th>Tanggal Selesai</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="ticket-list-table">
                                                            <!-- Will be populated by JS -->
                                                        </tbody>
                                                    </table>
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

        /* Specific styling for Resolution by Service Chart */
        #resolutionByServiceChart {
            width: 100% !important;
            height: 240px !important;
            /* Match the inline style in the template */
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .block-content {
                padding: 1rem;
            }

            .block-title {
                font-size: 1rem;
            }

            #resolutionByServiceChart {
                height: 200px !important;
            }
        }

        @media (max-width: 480px) {
            .block-content {
                padding: 0.75rem;
            }

            .block-title {
                font-size: 0.9rem;
            }

            #resolutionByServiceChart {
                height: 180px !important;
            }
        }
    </style>
@endsection

@section('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="{{ asset('assets/js/pegawai_dashboard.js') }}"></script>
    </body>

    </html>
@endsection
