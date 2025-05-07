@extends('layouts.app')

@section('title', 'Dashboard Warga')

@section('content')
<div id="page-container" class="sidebar-o sidebar-dark enable-page-overlay side-scroll page-header-fixed main-content-narrow">
    <main id="main-container">
        <div class="content">
            <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-center py-2 text-center text-md-start">
                <div class="flex-grow-1 mb-1 mb-md-0">
                    <h1 class="h3 fw-bold mb-2">
                        Dashboard
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
                                <i class="far fa-gem fs-3 text-primary"></i>
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
                                <i class="far fa-paper-plane fs-3 text-primary"></i>
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
                                <i class="fa fa-chart-bar fs-3 text-primary"></i>
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
                <div class="col-xl-12 col-xxl-12 d-flex flex-column">
                    <div class="block block-rounded flex-grow-1 d-flex flex-column">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Statistik Aduan</h3>
                            <div class="block-options">
                                <button type="button" class="btn-block-option" data-toggle="block-option"
                                    data-action="state_toggle" data-action-mode="demo">
                                    <i class="si si-refresh"></i>
                                </button>
                            </div>
                        </div>
                        <div class="block-content block-content-full flex-grow-1 d-flex align-items-center">
                            <canvas id="statistic"></canvas>
                        </div>
                        <div class="block-content bg-body-light">
                            <div class="row items-push text-center w-100">
                                <div class="col-sm-6">
                                    <dl class="mb-0">
                                        @php
                                        $total = ($ticketStats['completed'] ?? 0) + ($ticketStats['pending'] ?? 0) + ($ticketStats['assigned'] ?? 0);
                                        @endphp
                                        <dt class="fs-3 fw-bold d-inline-flex align-items-center space-x-2">
                                            {{ $total }}
                                        </dt>
                                        <dd class="fs-sm fw-medium text-muted mb-0">Jumlah Aduan</dd>
                                    </dl>
                                </div>
                                <div class="col-sm-6">
                                    @php
                                    $completed = $ticketStats['completed'] ?? 0;
                                    $percentage = $total > 0 ? round(($completed / $total) * 100, 1) : 0;
                                    @endphp
                                    <dl class="mb-0">
                                        <dt class="fs-3 fw-bold d-inline-flex align-items-center space-x-2">
                                            {{ $percentage }}%
                                        </dt>
                                        <dd class="fs-sm fw-medium text-muted mb-0">Aduan Selesai</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Daftar Aduan</h3>
                        <div class="block-options space-x-1">
                            <!-- <button type="button" class="btn btn-sm btn-alt-secondary" data-toggle="class-toggle"
                                data-target="#one-dashboard-search-orders" data-class="d-none">
                                <i class="fa fa-search"></i>
                            </button> -->
                            <div class="dropdown d-inline-block">
                                <button type="button" class="btn btn-sm btn-alt-secondary"
                                    id="dropdown-recent-orders-filters" data-bs-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    <i class="fa fa-fw fa-flask"></i>
                                    Filters
                                    <i class="fa fa-angle-down ms-1"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-md dropdown-menu-end fs-sm"
                                    aria-labelledby="dropdown-recent-orders-filters">
                                    <a class="dropdown-item fw-medium d-flex align-items-center justify-content-between"
                                        href="javascript:void(0)" data-status="belum">
                                        Belum Direspon
                                    </a>
                                    <a class="dropdown-item fw-medium d-flex align-items-center justify-content-between"
                                        href="javascript:void(0)" data-status="direspon">
                                        Direspon
                                    </a>
                                    <a class="dropdown-item fw-medium d-flex align-items-center justify-content-between"
                                        href="javascript:void(0)" data-status="selesai">
                                        Selesai
                                    </a>
                                    <a class="dropdown-item fw-medium d-flex align-items-center justify-content-between"
                                        href="javascript:void(0)" data-status="semua">
                                        Tampilkan Semua
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div id="one-dashboard-search-orders" class="block-content border-bottom d-none">
                        <!-- Search Form -->
                        <form action="be_pages_dashboard.html" method="POST" onsubmit="return false;">
                            <div class="push">
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-alt" id="one-ecom-orders-search"
                                        name="one-ecom-orders-search" placeholder="Cari berdasarkan kode">
                                    <span class="input-group-text bg-body border-0">
                                        <i class="fa fa-search"></i>
                                    </span>
                                </div>
                            </div>
                        </form>
                        <!-- END Search Form -->
                    </div>
                    <div class="block-content block-content-full">
                        <!-- Recent Orders Table -->
                        <div class="table-responsive">
                            <table class="table table-hover table-vcenter">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Kode Tiket</th>
                                        <th>Status</th>
                                        <th class="d-none d-xl-table-cell">Judul</th>
                                        <th class="d-none d-sm-table-cell text-center">Deskripsi</th>
                                    </tr>
                                </thead>
                                <tbody class="fs-sm">
                                    @if ($tickets->count() > 0)
                                    @foreach ($tickets as $index => $ticket)
                                    <tr class="text-nowrap" data-status="{{ $ticket->status == 0 ? 'belum' : ($ticket->status == 1 ? 'direspon' : 'selesai') }}">
                                        <td>{{ ($tickets->currentPage() - 1) * $tickets->perPage() + $index + 1 }}</td>
                                        <td>{{ $ticket->ticket_code }}</td>
                                        <td>
                                            @if($ticket->status == 0)
                                            <span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-warning-light text-warning">Belum Direspon</span>
                                            @elseif($ticket->status == 1)
                                            <span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-info-light text-info">Direspon</span>
                                            @else
                                            <span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-success-light text-success">Selesai</span>
                                            @endif
                                        </td>
                                        <td class="d-none d-xl-table-cell">{{ $ticket->title }}</td>
                                        <td class="d-none d-sm-table-cell text-center">{{ $ticket->description }}</td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="5">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="alert alert-info m-0">
                                                        Anda belum memiliki tiket. Silakan buka halaman tiket dan klik Buat Aduan untuk membuat tiket.
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
    </main>
</div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const total = {{ 
            ($ticketStats['assigned'] ?? 0) + 
            ($ticketStats['pending'] ?? 0) + 
            ($ticketStats['completed'] ?? 0) 
        }};
        const completed = {{ 
            $ticketStats['completed'] ?? 0 
        }};
        const percentageCompleted = total > 0 ? ((completed / total) * 100).toFixed(1) : 0;

        const ctx = document.getElementById('statistic').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Selesai', 'Dalam Proses'],
                datasets: [{
                    label: 'Persentase Aduan',
                    data: [percentageCompleted, 100 - percentageCompleted],
                    backgroundColor: ['#9e9e9e', '#2d4f7a'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });
    });

    document.querySelectorAll('[data-status]').forEach(el => {el.setAttribute('data-status-row', el.getAttribute('data-status'));
    });

    document.querySelectorAll('.dropdown-item').forEach(item => {
        item.addEventListener('click', function () {
            const filter = this.getAttribute('data-status');
            const rows = document.querySelectorAll('tr[data-status-row]');

            rows.forEach(row => {
                if (filter === 'semua' || row.getAttribute('data-status-row') === filter) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
</script>


@endsection