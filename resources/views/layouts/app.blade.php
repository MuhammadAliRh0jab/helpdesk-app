<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helpdesk Pemerintah Kota Blitar - @yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous" />

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    {{--
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2Lw=="
        crossorigin="anonymous" /> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Local Leaflet CSS -->
    <link rel="stylesheet" href="{{ asset('assets/leaflet/leaflet.css') }}">

    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/media/img/logo.png') }}">
    <link rel="apple-touch-icon" sizes="180x180"
        href="{{ asset('assets2/media/favicons/apple-touch-icon-180x180.png') }}">

    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('assets2/js/plugins/datatables-bs5/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('assets2/js/plugins/datatables-buttons-bs5/css/buttons.bootstrap5.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('assets2/js/plugins/datatables-responsive-bs5/css/responsive.bootstrap5.min.css') }}">

    <!-- OneUI framework -->
    <link rel="stylesheet" id="css-main" href="{{ asset('assets2/css/oneui.min.css') }}">

    <!-- Yield Additional Styles -->
    @yield('styles')
</head>

<body class="app-default">
    @include('layouts.partials.sidebar-layout.header._navbar')
    <div>
        @yield('content')
    </div>

    <!-- jQuery -->
    <script src="{{ asset('assets2/js/lib/jquery.min.js') }}"></script>

    <!-- Bootstrap JS -->
    {{--
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> --}}

    <!-- Chart.js -->
    {{--
    <script src="{{ asset('assets/js/chart.js') }}"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.js"></script>


    <!-- Theme and Dark Mode -->
    {{--
    <script src="{{ asset('assets2/js/setTheme.js') }}"></script> --}}

    <!-- DataTables Plugins -->
    <script src="{{ asset('assets2/js/plugins/datatables/dataTables.min.js') }}"></script>
    <script src="{{ asset('assets2/js/plugins/datatables-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets2/js/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets2/js/plugins/datatables-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets2/js/plugins/datatables-buttons/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets2/js/plugins/datatables-buttons-bs5/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets2/js/plugins/datatables-buttons-jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('assets2/js/plugins/datatables-buttons-pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets2/js/plugins/datatables-buttons-pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets2/js/plugins/datatables-buttons/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets2/js/plugins/datatables-buttons/buttons.html5.min.js') }}"></script>

    <!-- Page JS Code -->
    <script src="{{ asset('assets2/js/pages/be_tables_datatables.min.js') }}"></script>
    <script src="{{ asset('assets2/js/chat.js') }}"></script>

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.0/dist/cdn.min.js" defer></script>

    <!-- Local Leaflet JS -->
    <script src="{{ asset('assets/leaflet/leaflet.js') }}"></script>

    <script src="{{ asset('assets/js/axios.min.js') }}"></script>

    <script src="{{ asset('assets/js/apexcharts.min.js') }}"></script>
    {{--
    <script src="{{ asset('assets/js/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/css/flatpickr.css') }}"></script> --}}

    <!-- Sidebar Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');

            if (sidebar && sidebarToggle) {
                sidebarToggle.addEventListener('click', function () {
                    sidebar.classList.toggle('active');
                });

                document.addEventListener('click', function (event) {
                    const isClickInsideSidebar = sidebar.contains(event.target);
                    const isClickToggle = sidebarToggle.contains(event.target);

                    if (!isClickInsideSidebar && !isClickToggle && window.innerWidth < 992) {
                        sidebar.classList.remove('active');
                    }
                });
            }
        });
    </script>

    <!-- Yield Additional Scripts -->
    @yield('scripts')

    <style>
        body {
            font-family: "Poppins", sans-serif;
            margin-bottom: 5rem;
            background-color: #ebeef2;
        }

        .flatpickr-calendar {
            z-index: 9999 !important;
        }

        .flatpickr-day.selected {
            background: #4361EE !important;
            border-color: #4361EE !important;
        }

        /* Styling untuk tabel Tiket Terbaru */
        /* General table styling */
#recent-tickets-table tr {
    border-bottom: 1px solid #e0e0e0;
}

#recent-tickets-table td, #recent-tickets-table th {
    padding: 12px 16px;
    font-size: 14px;
    vertical-align: middle;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Zebra stripes */
#recent-tickets-table tr:nth-child(even) {
    background-color: #f9f9f9;
}

/* Status badges */
.badge {
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    color: white;
}

.badge.bg-success {
    background-color: #28a745;
}

.badge.bg-warning {
    background-color: #fd7e14;
}

.badge.bg-info {
    background-color: #17a2b8;
}

.badge.bg-secondary {
    background-color: #6c757d;
}

/* Ellipsis styling */
.text-ellipsis {
    max-width: 160px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Table header */
#recent-tickets-table thead th {
    background-color: #f4f6f9;
    font-weight: bold;
}


        .chart-container-scrollable {
            max-height: 300px;
            /* Batasi tinggi container */
            overflow-y: auto;
            /* Aktifkan scroll vertikal */
            overflow-x: hidden;
            /* Nonaktifkan scroll horizontal */
            position: relative;
            /* Pastikan canvas tetap di dalam container */
        }

        /* Pastikan canvas mengikuti lebar container */
        .chart-container-scrollable canvas {
            width: 100% !important;
            /* Paksa lebar canvas mengikuti container */
            min-height: 300px;
            /* Tinggi minimum untuk memastikan chart terlihat */
        }

        .card-mini {
  background: #fff;
  border-radius: 10px;
  padding: 16px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
  max-width: 400px;
  margin: auto;
}

.card-header {
  border-bottom: 1px solid #f0f0f0;
  padding-bottom: 8px;
  margin-bottom: 12px;
}

.title {
  font-size: 14px;
  font-weight: 600;
  text-transform: uppercase;
}

.btn-icon {
  background: #f9f9f9;
  border: none;
  border-radius: 6px;
  padding: 6px 8px;
  cursor: pointer;
}

.chart-container {
  position: relative;
  height: 220px;
}

.label {
  font-size: 11px;
  color: #6c757d;
  text-transform: uppercase;
}

.value {
  font-size: 16px;
  font-weight: bold;
  color: #2b2e4a;
}

    </style>
</body>

</html>