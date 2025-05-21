@extends('layouts.app')

@section('title', 'Dashboard Warga')

@section('content')
<div id="page-container" class="sidebar-o sidebar-dark enable-page-overlay side-scroll page-header-fixed main-content-narrow">
  <main id="main-container">
    <div class="content">
      <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-center py-2 text-center text-md-start">
        <div class="flex-grow-1 mb-1 mb-md-0">
          <h1 class="h3 fw-bold mb-2">
            Dashboard Admin
          </h1>
          <h2 class="h6 fw-medium fw-medium text-muted mb-0">
            Selamat Datang <a class="fw-semibold" href="be_pages_generic_profile.html">{{ Auth::user()->name }}</a>, &#128075;&#128522; !
          </h2>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="row items-push">
        <div class="col-sm-6 col-xxl-3">
          <div class="block block-rounded d-flex flex-column h-100 mb-0">
            <div class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
              <dl class="mb-0">
                <dt class="fs-3 fw-bold">
                  {{ $ticketStats['completed'] + $ticketStats['pending'] + $ticketStats['assigned'] }}
                </dt>
                <dd class="fs-sm fw-medium text-muted mb-0">Total Tiket</dd>
              </dl>
              <div class="item item-rounded-lg bg-body-light">
                <i class="fas fa-ticket fs-3 text-primary"></i>
              </div>
            </div>
            <div class="bg-body-light rounded-bottom">
              <a class="block-content block-content-full block-content-sm fs-sm fw-medium d-flex align-items-center justify-content-between"
                href="javascript:void(0)">
              </a>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-xxl-3">
          <div class="block block-rounded d-flex flex-column h-100 mb-0">
            <div class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
              <dl class="mb-0">
                <dt class="fs-3 fw-bold">
                  {{ $ticketStats['pending'] ?? 0 }}
                </dt>
                <dd class="fs-sm fw-medium text-muted mb-0">Tiket Pending</dd>
              </dl>
              <div class="item item-rounded-lg bg-body-light">
                <i class="fas fa-clock fs-3 text-warning"></i>
              </div>
            </div>
            <div class="bg-body-light rounded-bottom">
              <a class="block-content block-content-full block-content-sm fs-sm fw-medium d-flex align-items-center justify-content-between"
                href="javascript:void(0)">
              </a>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-xxl-3">
          <div class="block block-rounded d-flex flex-column h-100 mb-0">
            <div class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
              <dl class="mb-0">
                <dt class="fs-3 fw-bold">
                  {{ $ticketStats['assigned'] ?? 0 }}
                </dt>
                <dd class="fs-sm fw-medium text-muted mb-0">Tiket Ditugaskan</dd>
              </dl>
              <div class="item item-rounded-lg bg-body-light">
                <i class="fas fa-user-clock fs-3 text-info"></i>
              </div>
            </div>
            <div class="bg-body-light rounded-bottom">
              <a class="block-content block-content-full block-content-sm fs-sm fw-medium d-flex align-items-center justify-content-between"
                href="javascript:void(0)">
              </a>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-xxl-3">
          <div class="block block-rounded d-flex flex-column h-100 mb-0">
            <div class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
              <dl class="mb-0">
                <dt class="fs-3 fw-bold">
                  {{ $ticketStats['completed'] ?? 0 }}
                </dt>
                <dd class="fs-sm fw-medium text-muted mb-0">Tiket Selesai</dd>
              </dl>
              <div class="item item-rounded-lg bg-body-light">
                <i class="fas fa-check-circle fs-3 text-success"></i>
              </div>
            </div>
            <div class="bg-body-light rounded-bottom">
              <a class="block-content block-content-full block-content-sm fs-sm fw-medium d-flex align-items-center justify-content-between"
                href="javascript:void(0)">
              </a>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <!-- Service Distribution Chart (New) -->
        <div class="col-xl-12 col-xxl-12 d-flex flex-column">
          <div class="block block-rounded flex-grow-1 d-flex flex-column">
            <div class="block-header block-header-default">
              <h3 class="block-title">Distribusi Tiket per Layanan</h3>
              <div class="block-options">
                <button type="button" class="btn-block-option" data-toggle="block-option"
                  data-action="state_toggle" data-action-mode="demo">
                  <i class="si si-refresh"></i>
                </button>
              </div>
            </div>
            <div
              class="block-content block-content-full flex-grow-1 d-flex align-items-center chart-container-scrollable">
              <canvas id="serviceDistributionChart" style="width: 100%;"></canvas>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-xl-12 col-xxl-12 d-flex flex-column">
          <div class="block block-rounded flex-grow-1 d-flex flex-column">
            <div class="block-header block-header-default">
              <h3 class="block-title">Distribusi Pengaduan per Unit</h3>
              <div class="block-options">
                <button type="button" class="btn-block-option" data-toggle="block-option" data-action="state_toggle" data-action-mode="demo">
                  <i class="si si-refresh"></i>
                </button>
              </div>
            </div>
            <div class="block-content block-content-full flex-grow-1 d-flex align-items-center justify-content-center">
              <div style="width: 100%; max-height: 300px; overflow-y: auto;">
                <table id="unitDistributionChart" class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>Nama Dinas/Unit</th>
                      <th>Jumlah Pengaduan</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- Data akan diisi oleh JavaScript -->
                  </tbody>
                </table>
              </div>
            </div>
            <!-- <div class="block-content block-content-full flex-grow-1 d-flex align-items-center justify-content-center">
              <div style="position: relative; width: 100%; height: 230px;">
                <canvas id="unitDistributionChart"></canvas>
              </div>
            </div> -->
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-xl-8 col-xxl-8 d-flex flex-column">
          <div class="block block-rounded flex-grow-1 d-flex flex-column">
            <div class="block-header block-header-default">
              <h3 class="block-title">Performa Tiket</h3>
              <div class="block-options">
                <button type="button" class="btn-block-option" data-toggle="block-option" data-action="state_toggle" data-action-mode="demo">
                  <i class="si si-refresh"></i>
                </button>
                <div class="dropdown d-inline-block">
                  <button type="button" class="btn-block-option dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-fw fa-calendar"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="javascript:void(0)" data-time-range="day">Hari</a>
                    <a class="dropdown-item active" href="javascript:void(0)" data-time-range="week">Minggu</a>
                    <a class="dropdown-item" href="javascript:void(0)" data-time-range="month">Bulan</a>
                    <a class="dropdown-item" href="javascript:void(0)" data-time-range="year">Tahun</a>
                    <a class="dropdown-item" href="javascript:void(0)" data-time-range="10year">10 Tahun</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="javascript:void(0)" id="custom-range">Kustom</a>
                  </div>
                </div>
                <input type="text" id="custom-date-picker" class="d-none" placeholder="Pilih Rentang Tanggal">
              </div>
            </div>
            <div class="block-content block-content-full flex-grow-1 d-flex align-items-center">
              <canvas id="ticketPerformanceChart" style="width: 100%; height: 300px;"></canvas>
            </div>
          </div>
        </div>

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
        <div class="col-xl-6 col-xxl-6 d-flex flex-column">
          <div class="block block-rounded flex-grow-1 d-flex flex-column">
            <div class="block-header block-header-default">
              <h3 class="block-title">Distribusi Pelapor</h3>
              <div class="block-options">
                <button type="button" class="btn-block-option" data-toggle="block-option" data-action="state_toggle" data-action-mode="demo">
                  <i class="si si-refresh"></i>
                </button>
              </div>
            </div>
            <div class="block-content block-content-full flex-grow-1 d-flex align-items-center justify-content-center">
              <div style="position: relative; width: 100%; height: 230px;">
                <canvas id="ticketCategoryChart"></canvas>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-6 col-xxl-6 d-flex flex-column">
          <div class="block block-rounded flex-grow-1 d-flex flex-column">
            <div class="block-header block-header-default">
              <h3 class="block-title">Performa per Layanan</h3>
              <div class="block-options">
                <button type="button" class="btn-block-option" id="prev-service">
                  <i class="fa fa-arrow-left"></i>
                </button>
                <button type="button" class="btn-block-option" id="next-service">
                  <i class="fa fa-arrow-right"></i>
                </button>
                <button type="button" class="btn-block-option" data-toggle="block-option" data-action="state_toggle" data-action-mode="demo">
                  <i class="si si-refresh"></i>
                </button>
              </div>
            </div>
            <div class="block-content block-content-full flex-grow-1 d-flex align-items-center justify-content-center">
              <div style="position: relative; width: 100%; height: 230px;">
                <canvas id="perServiceChart"></canvas>
              </div>
            </div>
            <div class="block-content bg-body-light text-center">
              <span id="current-service-name">Memuat...</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Tickets -->
      <!-- Recent Tickets -->
      <div class="row">
        <div class="col-xl-12 col-xxl-12">
          <div></div>
          <div class="block block-rounded">
            <div class="block-header block-header-default">
              <h3 class="block-title">Tiket Terbaru</h3>
              <div class="block-options">
                <button type="button" class="btn-block-option" data-toggle="block-option"
                data-action="state_toggle" data-action-mode="demo">
                <i class="si si-refresh"></i>
              </button>
            </div>
          </div>
          <div class="table-responsive">
            <div class="block-content">
              <table class="table table-borderless table-striped table-vcenter fs-sm">
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
    </div>
</div>
</main>
</div>

@section('scripts')
<!-- ApexCharts (dashboard-specific) -->
<script src="{{ asset('assets/js/apexcharts.min.js') }}"></script>

<!-- Axios (dashboard-specific) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="{{ asset('assets/js/superadmin_dashboard.js') }}"></script>

<!-- Dashboard JavaScript -->
<script>
  // Pass PHP data to JavaScript
  window.currentOperatorUnitId = "{{ Auth::user()->unit_id ?? 'null' }}";

  window.initialTicketStats = {
      completed: {{ $ticketStats['completed'] ?? 0 }},
      pending: {{ $ticketStats['pending'] ?? 0 }},
      assigned: {{ $ticketStats['assigned'] ?? 0 }}
  };
</script>
@endsection