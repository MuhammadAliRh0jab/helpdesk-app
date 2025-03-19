@extends('layouts.app')

@section('title', 'Dashboard Pegawai')

@section('content')
    <h1 class="text-2xl font-bold mb-4 text-gray-800 dark:text-gray-200">Selamat Datang, {{ Auth::user()->name }}</h1>

    <div class="bg-white dark:bg-gray-800 p-6 rounded shadow dark:shadow-gray-700 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Tiket yang Anda Selesaikan</h3>
        <div class="w-full max-w-md mx-auto">
            <canvas id="personalChart" width="300" height="150"></canvas>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 p-6 rounded shadow dark:shadow-gray-700">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Tiket yang Anda Ajukan</h3>
        <div class="w-full max-w-md mx-auto">
            <canvas id="createdChart" width="300" height="150"></canvas>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('tickets.index') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 transition duration-200">
            Lihat Daftar Aduan
        </a>
        <a href="{{ route('tickets.assigned') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700 transition duration-200 ml-2">
            Lihat Aduan Ditugaskan
        </a>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let personalChartInstance = null;
        let createdChartInstance = null;

        function renderPersonalChart() {
            if (personalChartInstance) {
                personalChartInstance.destroy();
                console.log('Chart personal lama dihancurkan');
            }

            const ctxPersonal = document.getElementById('personalChart').getContext('2d');
            personalChartInstance = new Chart(ctxPersonal, {
                type: 'bar',
                data: {
                    labels: ['Tiket Diselesaikan'],
                    datasets: [{
                        label: 'Jumlah Tiket',
                        data: [{{ $personalStats['resolved'] }}],
                        backgroundColor: ['rgba(153, 102, 255, 0.8)'], // Ungu untuk Tiket Diselesaikan
                        borderColor: ['rgba(153, 102, 255, 1)'],
                        borderWidth: 1,
                        borderRadius: 3,
                        borderSkipped: false
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: { size: 12 },
                            bodyFont: { size: 10 },
                            cornerRadius: 4,
                            callbacks: { label: function(context) { return `${context.dataset.label}: ${context.raw}`; } }
                        }
                    },
                    scales: {
                        x: { beginAtZero: true, grid: { display: false }, ticks: { color: '#6b7280', font: { size: 10 }, padding: 5 } },
                        y: { grid: { display: false }, ticks: { color: '#6b7280', font: { size: 12 }, padding: 5 } }
                    },
                    layout: { padding: { left: 5, right: 5, top: 5, bottom: 5 } },
                    animation: { duration: 1500, easing: 'easeOutQuart' }
                }
            });
        }

        function renderCreatedChart() {
            if (createdChartInstance) {
                createdChartInstance.destroy();
                console.log('Chart created lama dihancurkan');
            }

            const ctxCreated = document.getElementById('createdChart').getContext('2d');
            createdChartInstance = new Chart(ctxCreated, {
                type: 'bar',
                data: {
                    labels: ['Tiket Dibuat'],
                    datasets: [{
                        label: 'Jumlah Tiket',
                        data: [{{ $createdStats['created'] }}],
                        backgroundColor: ['rgba(54, 162, 235, 0.8)'], // Biru untuk Tiket Dibuat
                        borderColor: ['rgba(54, 162, 235, 1)'],
                        borderWidth: 1,
                        borderRadius: 3,
                        borderSkipped: false
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: { size: 12 },
                            bodyFont: { size: 10 },
                            cornerRadius: 4,
                            callbacks: { label: function(context) { return `${context.dataset.label}: ${context.raw}`; } }
                        }
                    },
                    scales: {
                        x: { beginAtZero: true, grid: { display: false }, ticks: { color: '#6b7280', font: { size: 10 }, padding: 5 } },
                        y: { grid: { display: false }, ticks: { color: '#6b7280', font: { size: 12 }, padding: 5 } }
                    },
                    layout: { padding: { left: 5, right: 5, top: 5, bottom: 5 } },
                    animation: { duration: 1500, easing: 'easeOutQuart' }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            console.log('DOM selesai dimuat, merender chart');
            renderPersonalChart();
            renderCreatedChart();
        });

        document.addEventListener('alpine:init', () => {
            console.log('Alpine.js diinisialisasi');
        });
    </script>
@endsection