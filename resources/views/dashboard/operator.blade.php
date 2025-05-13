@extends('layouts.app')

@section('title', 'Dashboard Operator')

@section('content')
<div id="page-container" class="sidebar-o sidebar-dark enable-page-overlay side-scroll page-header-fixed main-content-narrow">
    <main id="main-container">
        <div class="content">
            <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-center py-2 text-center text-md-start">
                <div class="flex-grow-1 mb-1 mb-md-0">
                    <h1 class="h3 fw-bold mb-2">
                        Dashboard Operator
                    </h1>
                    <h2 class="h6 fw-medium fw-medium text-muted mb-0">
                        Selamat Datang <a class="fw-semibold" href="be_pages_generic_profile.html">{{ Auth::user()->name }}</a>, 👋😊 !
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
                <!-- Bar Chart: Ticket Completion Status -->
                <div class="col-xl-6 col-xxl-6 d-flex flex-column">
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

                <!-- Pie Chart: Ticket Status Distribution -->
                <div class="col-xl-6 col-xxl-6 d-flex flex-column">
                    <div class="block block-rounded flex-grow-1 d-flex flex-column">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Distribusi Status Tiket</h3>
                            <div class="block-options">
                                <button type="button" class="btn-block-option" data-toggle="block-option"
                                    data-action="state_toggle" data-action-mode="demo">
                                    <i class="si si-refresh"></i>
                                </button>
                            </div>
                        </div>
                        <div class="block-content block-content-full flex-grow-1 d-flex align-items-center">
                            <canvas id="ticketDistribution"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Line Chart: Ticket Trends Over Time -->
                <div class="col-xl-6 col-xxl-6 d-flex flex-column">
                    <div class="block block-rounded flex-grow-1 d-flex flex-column">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Tren Tiket Bulanan</h3>
                            <div class="block-options">
                                <button type="button" class="btn-block-option" data-toggle="block-option"
                                    data-action="state_toggle" data-action-mode="demo">
                                    <i class="si si-refresh"></i>
                                </button>
                            </div>
                        </div>
                        <div class="block-content block-content-full flex-grow-1 d-flex align-items-center">
                            <canvas id="ticketTrend"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Doughnut Chart: Ticket Categories -->
                <div class="col-xl-6 col-xxl-6 d-flex flex-column">
                    <div class="block block-rounded flex-grow-1 d-flex flex-column">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Kategori Tiket</h3>
                            <div class="block-options">
                                <button type="button" class="btn-block-option" data-toggle="block-option"
                                    data-action="state_toggle" data-action-mode="demo">
                                    <i class="si si-refresh"></i>
                                </button>
                            </div>
                        </div>
                        <div class="block-content block-content-full flex-grow-1 d-flex align-items-center">
                            <canvas id="ticketCategory"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Include Chart.js locally -->
<script src="{{ asset('js/chart.min.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Data for charts
        const ticketStats = {
            completed: {{ $ticketStats['completed'] ?? 0 }},
            pending: {{ $ticketStats['pending'] ?? 0 }},
            assigned: {{ $ticketStats['assigned'] ?? 0 }}
        };
        const total = ticketStats.completed + ticketStats.pending + ticketStats.assigned;
        const percentageCompleted = total > 0 ? ((ticketStats.completed / total) * 100).toFixed(1) : 0;

        // Bar Chart: Ticket Completion Status
        const ctxBar = document.getElementById('statistic').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: ['Selesai', 'Dalam Proses'],
                datasets: [{
                    label: 'Persentase Aduan',
                    data: [percentageCompleted, 100 - percentageCompleted],
                    backgroundColor: ['#1d4ed8', '#487fff'],
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

        // Pie Chart: Ticket Status Distribution
        const ctxPie = document.getElementById('ticketDistribution').getContext('2d');
        new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: ['Selesai', 'Pending', 'Ditugaskan'],
                datasets: [{
                    label: 'Jumlah Tiket',
                    data: [ticketStats.completed, ticketStats.pending, ticketStats.assigned],
                    backgroundColor: ['#1d4ed8', '#facc15', '#22c55e'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.raw || 0;
                                let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Line Chart: Ticket Trends Over Time (Mock Data)
        // Replace with actual data from your backend, e.g., $ticketTrends = ['Jan' => 10, 'Feb' => 15, ...]
        const ticketTrends = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
            data: [10, 15, 12, 20, 18, 25] // Example data
        };
        const ctxLine = document.getElementById('ticketTrend').getContext('2d');
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: ticketTrends.labels,
                datasets: [{
                    label: 'Jumlah Tiket',
                    data: ticketTrends.data,
                    borderColor: '#1d4ed8',
                    backgroundColor: 'rgba(29, 78, 216, 0.2)',
                    fill: true,
                    tension: 0.4
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
                        beginAtZero: true
                    }
                }
            }
        });

        // Doughnut Chart: Ticket Categories (Mock Data)
        // Replace with actual data, e.g., $ticketCategories = ['Infrastruktur' => 5, 'Keamanan' => 3, ...]
        const ticketCategories = {
            labels: ['Infrastruktur', 'Keamanan', 'Kebersihan', 'Lainnya'],
            data: [5, 3, 4, 2] // Example data
        };
        const ctxDoughnut = document.getElementById('ticketCategory').getContext('2d');
        new Chart(ctxDoughnut, {
            type: 'doughnut',
            data: {
                labels: ticketCategories.labels,
                datasets: [{
                    label: 'Jumlah Tiket',
                    data: ticketCategories.data,
                    backgroundColor: ['#1d4ed8', '#facc15', `#22c55e`, '#ef4444'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.raw || 0;
                                let totalCategories = ticketCategories.data.reduce((a, b) => a + b, 0);
                                let percentage = totalCategories > 0 ? ((value / totalCategories) * 100).toFixed(1) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Existing filter logic
        document.querySelectorAll('[data-status]').forEach(el => {
            el.setAttribute('data-status-row', el.getAttribute('data-status'));
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
    });
</script>

@endsection