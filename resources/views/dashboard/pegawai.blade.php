@extends('layouts.app')

@section('title', 'Dashboard Pegawai')

@section('content')
<div>
    <div class="card-header shadow">
        <h1 class="h4 text-white fs-4">Dashboard Pegawai</h1>
        <p class="text-white fs-6">Helpdesk Pemerintah Kota Blitar</p>
    </div>
    
    <div class="card shadow mb-4 mt-3" style="background: linear-gradient(180deg,rgba(21, 113, 232, 0.4) 0%, rgb(255, 255, 255) 100%);">
        <div class="card-body">
            <h5 class="card-title- mb-2">Selamat Datang, <strong>{{ Auth::user()->name }}</strong> 👋😊 !</h5>
            <hr>
            <p>Selamat datang di Helpdesk Pemerintah Kota Blitar! Sebagai pegawai, Anda dapat melihat tiket yang Anda ajukan dan tiket yang telah Anda selesaikan. Gunakan dashboard ini untuk memantau kontribusi Anda dalam penyelesaian aduan.</p>
        </div>
    </div>

    <h5 class="text-primary">Informasi Tiket Anda</h5>
    <hr class="mb-4 mt-2">

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <h5 class="card-title- mb-2">Tiket yang Anda Selesaikan</h5>
                    <hr>
                    <div class="w-100 mx-auto" style="height: 200px;">
                        <canvas id="personalChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <h5 class="card-title- mb-2">Tiket yang Anda Ajukan</h5>
                    <hr>
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
                    x: { 
                        beginAtZero: true,
                        grid: { display: false },
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                if (Number.isInteger(value)) {
                                    return value;
                                }
                            }
                        }
                    },
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