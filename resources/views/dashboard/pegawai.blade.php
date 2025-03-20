@extends('layouts.app')

@section('title', 'Dashboard Pegawai')

@section('content')
    <div>
        <h1 class="h4 mb-4 text-dark fs-2">Selamat Datang, {{ Auth::user()->name }}</h1>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title">Tiket yang Anda Selesaikan</h5>
                        <div class="w-100 mx-auto" style="height: 200px;">
                            <canvas id="personalChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title">Tiket yang Anda Ajukan</h5>
                        <div class="w-100 mx-auto" style="height: 200px;">
                            <canvas id="createdChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>          
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        const personalStats = {
            resolved: {{ $personalStats['resolved'] ?? 0 }}
        };
        const createdStats = {
            created: {{ $createdStats['created'] ?? 0 }}
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
            const personalChartCtx = document.getElementById('personalChart');
            if (personalChartCtx) {
                new Chart(personalChartCtx, {
                    ...chartOptions,
                    data: {
                        labels: ['Tiket Diselesaikan'],
                        datasets: [{
                            label: 'Jumlah Tiket',
                            data: [personalStats.resolved],
                            backgroundColor: ['#A1E3F9'],
                            borderWidth: 1
                        }]
                    }
                });
            }

            const createdChartCtx = document.getElementById('createdChart');
            if (createdChartCtx) {
                new Chart(createdChartCtx, {
                    ...chartOptions,
                    data: {
                        labels: ['Tiket Dibuat'],
                        datasets: [{
                            label: 'Jumlah Tiket',
                            data: [createdStats.created],
                            backgroundColor: ['#3674B5'],
                            borderWidth: 1
                        }]
                    }
                });
            }
        });
    </script>
@endsection