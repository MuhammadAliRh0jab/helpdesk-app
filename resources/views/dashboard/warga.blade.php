@extends('layouts.app')

@section('title', 'Dashboard Warga')

@section('content')
<div>
    <div class="card-header shadow">
        <h1 class="h4 text-white fs-4">Dashboard</h1>
        <p class="text-white fs-6">Helpdesk Pemerintah Kota Blitar</p>
    </div>
    <div class="card shadow mb-4 mt-3" style="background: linear-gradient(180deg,rgba(21, 113, 232, 0.4) 0%, rgb(255, 255, 255) 100%);">
        <div class="card-body">
            <h5 class="card-title- mb-2">Selamat Datang, <strong>{{ Auth::user()->name }}</strong> &#128075;&#128522; !</h5>
            <hr>
            <p>Selamat datang di Helpdesk Pemerintah Kota Blitar! Kami siap membantu Anda melaporkan aduan, mengelola tiket, dan mendapatkan solusi cepat untuk berbagai permasalahan. Gunakan dashboard ini untuk melihat status tiket Anda, membuat aduan baru, atau melacak progres penyelesaian. </p>
        </div>
    </div>
    <h5 class="text-primary"> Informasi Tiket Anda</h5>
    <hr class="mb-4 mt-2">

    <div class="row row-cols-1 row-cols-md-4 g-4 mb-4 text-center">
        <div class="col">
            <div class="card h-100 shadow">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <i class="fa-solid fa-ticket" style="color:rgb(96, 98, 103);"></i>
                    <div class="d-flex flex-column text-end">
                        <h5 class="card-title">Total Tiket</h5>
                        <p class="fs-2">{{ $ticketStats['completed'] + $ticketStats['pending'] + $ticketStats['assigned'] }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 shadow">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <i class="fa-solid fa-briefcase" style="color: #3674B5;"></i>
                    <div class="d-flex flex-column text-end">
                        <h5 class="card-title">Tiket Ditugaskan</h5>
                        <p class="fs-2">{{ $ticketStats['assigned'] }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 shadow">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <i class="fa-solid fa-hourglass-half" style="color: #578FCA ;"></i>
                    <div class="d-flex flex-column text-end">
                        <h5 class="card-title">Tiket Pending</h5>
                        <p class="fs-2">{{ $ticketStats['pending'] }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 shadow">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <i class="fa-solid fa-circle-check" style="color: #A1E3F9;"></i>
                    <div class="d-flex flex-column text-end">
                        <h5 class="card-title">Tiket Selesai</h5>
                        <p class="fs-2">{{ $ticketStats['completed'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow">
        <div class="card-body">
            <h5 class="card-title- mb-2">Distribusi Tiket Anda</h5>
            <hr>
            <div class="w-100 mx-auto">
                <canvas id="ticketChart"></canvas>
            </div>
        </div>
    </div>
    @endsection
        
    @section('scripts')
    <script>
    const ticketStats = {
        assigned: {{ $ticketStats['assigned'] ?? 0 }},
        pending: {{ $ticketStats['pending'] ?? 0 }},
        completed: {{ $ticketStats['completed'] ?? 0 }}
    };

    const chartOptions = {
        type: 'bar',
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { display: false } 
            },
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
                y: { 
                    grid: { display: false } 
                }
            }
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        const ticketChartCtx = document.getElementById('ticketChart');
        if (ticketChartCtx) {
            new Chart(ticketChartCtx, {
                ...chartOptions,
                data: {
                    labels: [  'Ditugaskan','Pending','Selesai'],
                    datasets: [{
                        label: 'Jumlah Tiket',
                        data: [ticketStats.assigned, ticketStats.pending, ticketStats.completed],
                        backgroundColor: [ '#3674B5','#578FCA', '#A1E3F9'],
                        borderWidth: 1
                    }]
                }
            });
        }
    });
</script>
@endsection
