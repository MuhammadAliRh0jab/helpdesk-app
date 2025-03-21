@extends('layouts.app')

@section('title', 'Dashboard Warga')

@section('content')
    <div class="p-4">
        <h1 class="h4 mb-4 text-dark">Selamat Datang, {{ Auth::user()->name }}</h1>

        <div class="row row-cols-1 row-cols-md-4 g-4 mb-4 text-center">
            <div class="col">
                <div class="card h-100 shadow">
                    <div class="card-body">
                        <h5 class="card-title">Tiket Selesai</h5>
                        <p class="fs-2 text-purple">{{ $ticketStats['completed'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 shadow">
                    <div class="card-body">
                        <h5 class="card-title">Tiket Pending</h5>
                        <p class="fs-2 text-danger">{{ $ticketStats['pending'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 shadow">
                    <div class="card-body">
                        <h5 class="card-title">Tiket Ditugaskan</h5>
                        <p class="fs-2 text-primary">{{ $ticketStats['assigned'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 shadow">
                    <div class="card-body">
                        <h5 class="card-title">Total Tiket</h5>
                        <p class="fs-2 text-secondary">{{ $ticketStats['completed'] + $ticketStats['pending'] + $ticketStats['assigned'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <h5 class="card-title mb-4">Distribusi Tiket Anda</h5>
                <div class="w-100 mx-auto" style="max-width: 480px; height: 200px;">
                    <canvas id="ticketChart"></canvas>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('tickets.index') }}" class="btn btn-primary px-4 py-2">Lihat Riwayat Aduan</a>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        const ticketStats = {
            completed: {{ $ticketStats['completed'] ?? 0 }},
            pending: {{ $ticketStats['pending'] ?? 0 }},
            assigned: {{ $ticketStats['assigned'] ?? 0 }}
        };

        const chartOptions = {
            type: 'bar',
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, grid: { display: false } },
                    y: { grid: { display: false } }
                }
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            const ticketChartCtx = document.getElementById('ticketChart');
            if (ticketChartCtx) {
                new Chart(ticketChartCtx, {
                    ...chartOptions,
                    data: {
                        labels: ['Selesai', 'Pending', 'Ditugaskan'],
                        datasets: [{
                            label: 'Jumlah Tiket',
                            data: [ticketStats.completed, ticketStats.pending, ticketStats.assigned],
                            backgroundColor: ['#A1E3F9', '#3674B5', '#578FCA'],
                            borderWidth: 1
                        }]
                    }
                });
            }
        });
    </script>
@endsection
