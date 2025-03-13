@extends('layouts.app')

@section('title', 'Dashboard Laporan Perangkat Kerja')

@section('content')
    <h1 class="text-2xl font-bold mb-4 text-gray-800 dark:text-gray-200">Dashboard Laporan Perangkat Kerja</h1>

    @if (session('success'))
        <div class="bg-green-100 text-green-800 p-4 mb-4 rounded dark:bg-green-900 dark:text-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 p-4 rounded shadow dark:shadow-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Jumlah Laporan Total</h3>
            <p class="text-2xl text-blue-600 dark:text-blue-400">{{ $totalReports }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded shadow dark:shadow-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Jumlah Laporan Direspon</h3>
            <p class="text-2xl text-green-600 dark:text-green-400">{{ $respondedReports }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded shadow dark:shadow-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Jumlah Laporan Selesai</h3>
            <p class="text-2xl text-purple-600 dark:text-purple-400">{{ $completedReports }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded shadow dark:shadow-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Jumlah Laporan Belum Direspon</h3>
            <p class="text-2xl text-red-600 dark:text-red-400">{{ $unrespondedReports }}</p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 p-6 rounded shadow dark:shadow-gray-700">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Distribusi Laporan</h3>
        <div class="w-full max-w-md mx-auto">
            <canvas id="reportChart" width="300" height="150"></canvas>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Variabel global untuk menyimpan instance chart
        let reportChartInstance = null;

        function renderChart() {
            // Hancurkan chart lama jika ada
            if (reportChartInstance) {
                reportChartInstance.destroy();
                console.log('Chart lama dihancurkan');
            }

            const ctx = document.getElementById('reportChart').getContext('2d');
            reportChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [{
                        label: 'Jumlah Laporan',
                        data: @json($chartData['data']),
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.8)', // Total: Biru
                            'rgba(75, 192, 192, 0.8)', // Direspon: Hijau
                            'rgba(153, 102, 255, 0.8)', // Selesai: Ungu
                            'rgba(255, 99, 132, 0.8)'  // Belum Direspon: Merah
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 99, 132, 1)'
                        ],
                        borderWidth: 1, // Kurangi border agar lebih proporsional
                        borderRadius: 3, // Kurangi border radius untuk ukuran kecil
                        borderSkipped: false
                    }]
                },
                options: {
                    indexAxis: 'y', // Horizontal bar chart
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: { size: 12 }, // Kecilkan font tooltip
                            bodyFont: { size: 10 },
                            cornerRadius: 4,
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.raw}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#6b7280',
                                font: { size: 10 }, // Kecilkan font sumbu x
                                padding: 5 // Kurangi padding
                            }
                        },
                        y: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#6b7280',
                                font: { size: 12 }, // Kecilkan font sumbu y
                                padding: 5 // Kurangi padding
                            }
                        }
                    },
                    layout: {
                        padding: {
                            left: 5,
                            right: 5,
                            top: 5,
                            bottom: 5
                        }
                    },
                    animation: {
                        duration: 1500,
                        easing: 'easeOutQuart',
                        onComplete: function() {
                            console.log('Animasi selesai');
                        }
                    }
                }
            });
        }

        // Render chart saat DOM selesai dimuat
        document.addEventListener('DOMContentLoaded', function () {
            console.log('DOM selesai dimuat, merender chart');
            renderChart();
        });

        // Jika menggunakan Alpine.js, pastikan chart tidak dirender ulang
        document.addEventListener('alpine:init', () => {
            console.log('Alpine.js diinisialisasi');
        });
    </script>
@endsection