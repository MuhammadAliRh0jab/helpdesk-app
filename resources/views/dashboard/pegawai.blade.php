@extends('layouts.app')

@section('title', 'Dashboard Pegawai')

@section('content')
<div id="page-container" class="sidebar-o sidebar-dark enable-page-overlay side-scroll page-header-fixed main-content-narrow">
    <main id="main-container">
        <div class="content">
            <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-center py-2 text-center text-md-start">
                <div class="flex-grow-1 mb-1 mb-md-0">
                    <h1 class="h3 fw-bold mb-2">
                        Dashboard Pegawai
                    </h1>
                    <h2 class="h6 fw-medium text-muted mb-0">
                        Selamat Datang, <a class="fw-semibold" href="javascript:void(0)">{{ Auth::user()->name }}</a>, 👋😊!
                    </h2>
                </div>
            </div>
        </div>

        <div class="content">
            <!-- Statistics Cards -->
            <div class="row items-push">
                <div class="col-sm-6 col-xl-4">
                    <div class="block block-rounded d-flex flex-column h-100 mb-0">
                        <div class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
                            <dl class="mb-0">
                                <dt class="fs-3 fw-bold" id="resolved-tickets">
                                    {{ $ticketStats['resolved'] ?? 0 }}
                                </dt>
                                <dd class="fs-sm fw-medium text-muted mb-0">Tiket Diselesaikan</dd>
                            </dl>
                            <div class="item item-rounded-lg bg-body-light">
                                <i class="fas fa-check-circle fs-3 text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-4">
                    <div class="block block-rounded d-flex flex-column h-100 mb-0">
                        <div class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
                            <dl class="mb-0">
                                <dt class="fs-3 fw-bold" id="created-tickets">
                                    {{ $ticketStats['created'] ?? 0 }}
                                </dt>
                                <dd class="fs-sm fw-medium text-muted mb-0">Tiket Diajukan</dd>
                            </dl>
                            <div class="item item-rounded-lg bg-body-light">
                                <i class="fas fa-ticket-alt fs-3 text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-4">
                    <div class="block block-rounded d-flex flex-column h-100 mb-0">
                        <div class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
                            <dl class="mb-0">
                                <dt class="fs-3 fw-bold" id="assigned-tickets">
                                    {{ $ticketStats['assigned'] ?? 0 }}
                                </dt>
                                <dd class="fs-sm fw-medium text-muted mb-0">Tiket Ditugaskan</dd>
                            </dl>
                            <div class="item item-rounded-lg bg-body-light">
                                <i class="fas fa-user-clock fs-3 text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Ticket Stats Chart -->
                <div class="col-xl-8 col-xxl-8 d-flex flex-column">
                    <div class="block block-rounded flex-grow-1 d-flex flex-column">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Statistik Tiket</h3>
                            <div class="block-options">
                                <button type="button" class="btn-block-option" data-toggle="block-option" data-action="state_toggle" data-action-mode="demo">
                                    <i class="si si-refresh"></i>
                                </button>
                            </div>
                        </div>
                        <div class="block-content block-content-full flex-grow-1 d-flex align-items-center">
                            <canvas id="ticketStatsChart" style="width: 100%; height: 300px;"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Ticket Distribution By Status -->
                <div class="col-xl-4 col-xxl-4 d-flex flex-column">
                    <div class="block block-rounded flex-grow-1 d-flex flex-column">
                        <div class="block-header block-header-default">
                            <h3 class="block-title fs-sm text-uppercase">Distribusi Status Tiket</h3>
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
                                    <div class="fs-4 fw-bold text-primary" id="assigned-percent">0%</div>
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

            <div class="row">
                <!-- Resolution Time Chart -->
                <div class="col-xl-6 col-xxl-6 d-flex flex-column">
                    <div class="block block-rounded flex-grow-1 d-flex flex-column">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Waktu Penyelesaian</h3>
                            <div class="block-options">
                                <button type="button" class="btn-block-option" data-toggle="block-option" data-action="state_toggle" data-action-mode="demo">
                                    <i class="si si-refresh"></i>
                                </button>
                            </div>
                        </div>
                        <div class="block-content block-content-full flex-grow-1 d-flex align-items-center">
                            <canvas id="resolutionTimeChart" style="width: 100%; height: 300px;"></canvas>
                        </div>
                        <div class="block-content bg-body-light text-center">
                            <p>Rata-rata: <span id="avg-resolution-time">{{ $averageResolutionTime }} hari</span></p>
                        </div>
                    </div>
                </div>

                <!-- Distribusi Tiket per Layanan -->
                <div class="col-xl-6 col-xxl-6 d-flex flex-column">
                    <div class="block block-rounded flex-grow-1 d-flex flex-column">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Distribusi Tiket per Layanan</h3>
                            <div class="block-options">
                                <button type="button" class="btn-block-option" data-toggle="block-option" data-action="state_toggle" data-action-mode="demo">
                                    <i class="si si-refresh"></i>
                                </button>
                            </div>
                        </div>
                        <div class="block-content block-content-full flex-grow-1 d-flex align-items-center">
                            <canvas id="serviceDistributionChart" style="width: 100%; height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Tickets -->
            <div class="row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="block block-rounded">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Tiket Terbaru</h3>
                            <div class="block-options">
                                <button type="button" class="btn-block-option" data-toggle="block-option" data-action="state_toggle" data-action-mode="demo">
                                    <i class="si si-refresh"></i>
                                </button>
                            </div>
                        </div>
                        <div class="block-content">
                            <table class="table table-borderless table-striped table-vcenter fs-sm">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Judul</th>
                                        <th>Tanggal Dibuat</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="recent-tickets-table">
                                    <!-- Will be populated by JS -->
                                </tbody>
                            </table>
                        </div>
                        <div class="block-content block-content-full bg-body-light">
                            <div class="d-flex justify-content-between">
                                <a class="btn btn-sm btn-alt-secondary" href="{{ route('tickets.index') }}">
                                    <i class="fa fa-eye opacity-50 me-1"></i> Lihat Semua
                                </a>
                                <a class="btn btn-sm btn-alt-primary" href="{{ route('tickets.create') }}">
                                    <i class="fa fa-plus opacity-50 me-1"></i> Buat Tiket
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.js"></script>
    <script src="{{ asset('assets/js/pegawai_dashboard.js') }}"></script>
    <script>
        window.initialTicketStats = @json($ticketStats);
        window.initialAvgResolutionTime = {{ $averageResolutionTime }};
    </script>
@endsection