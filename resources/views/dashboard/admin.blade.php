@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    <div>
        <h1 class="h4 mb-4 text-dark fs-2">Selamat Datang, {{ Auth::user()->name }}</h1>

        <div class="row mb-4 text-center">
            <div class="col-md-3">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title">Total Tiket</h5>
                        <p class="fs-2" style="color: #003092;">{{ $ticketStats['completed'] + $ticketStats['pending'] + $ticketStats['assigned'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title">Tiket Selesai</h5>
                        <p class="fs-2" style="color: #A1E3F9;">{{ $ticketStats['completed'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title">Tiket Pending</h5>
                        <p class="fs-2" style="color: #3674B5;">{{ $ticketStats['pending'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title">Tiket Ditugaskan</h5>
                        <p class="fs-2" style="color: #578FCA;">{{ $ticketStats['assigned'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title">Distribusi Tiket Global</h5>
                        <div class="w-100" style="height: 200px;">
                            <canvas id="ticketChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
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