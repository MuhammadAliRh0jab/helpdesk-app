@extends('mobile.master.app')

@section('title', 'Dashboard')

@section('header')
    @include('mobile.master.header')
@endsection

@section('sidenav')
    @include('mobile.master.sidenav')
@endsection

@section('content')
    <div class="page-content-wrapper py-3">

        <div class="container">
            <div class="card bg-primary my-3 bg-img shadow"
                style="background-image: url('{{ asset('mobile/img/core-img/1.png') }}')">
                <div class="card-body p-4">
                    <h2 class="text-white">Dashboard</h2>
                    <h5 class="my-2 text-white">Selamat Datang, {{ Auth::user()->name }}!</h5>
                    <small class="text-white">Helpdesk Kota Blitar | Sistem Pengaduan Kota Blitar</small>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="card shadow mb-3">
                <div class="card-body p-4">
                    <!-- Judul -->
                    <h5 class="card-title mb-3">Semua Laporan Saya</h5>

                    <hr>

                    <!-- Daftar Status -->
                    <div class="list-group">
                        <!-- Semua Status -->
                        <a href="#"
                            class="list-group-item list-group-item-action d-flex align-items-center justify-content-between bg-danger text-white mb-2 rounded">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-ticket fs-1 me-3"></i> <!-- Ikon tiket untuk Semua -->
                                <div>
                                    <h6 class="mb-0 text-white">Semua</h6>
                                </div>
                            </div>
                            <span
                                class="badge bg-white text-danger p-2 fs-6">{{ $ticketStats['completed'] + $ticketStats['pending'] + $ticketStats['assigned'] }}</span>
                        </a>

                        <!-- Belum Direspon Status -->
                        <a href="#"
                            class="list-group-item list-group-item-action d-flex align-items-center justify-content-between bg-warning text-white mb-2 rounded">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-chat fs-1 me-3"></i> <!-- Ikon chat untuk Belum Direspon -->
                                <div>
                                    <h6 class="mb-0 text-white">Belum Direspon</h6>
                                </div>
                            </div>
                            <span class="badge bg-white text-warning p-2 fs-6">{{ $ticketStats['pending'] }}</span>
                        </a>

                        <!-- Direspon Status -->
                        <a href="#"
                            class="list-group-item list-group-item-action d-flex align-items-center justify-content-between bg-primary text-white mb-2 rounded">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle fs-1 me-3"></i> <!-- Ikon centang untuk Direspon -->
                                <div>
                                    <h6 class="mb-0 text-white">Direspon</h6>
                                </div>
                            </div>
                            <span class="badge bg-white text-primary p-2 fs-6">{{ $ticketStats['assigned'] }}</span>
                        </a>

                        <!-- Selesai Status -->
                        <a href="#"
                            class="list-group-item list-group-item-action d-flex align-items-center justify-content-between bg-success text-white rounded">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-hand-thumbs-up fs-1 me-3"></i> <!-- Ikon jempol untuk Selesai -->
                                <div>
                                    <h6 class="mb-0 text-white">Selesai</h6>
                                </div>
                            </div>
                            <span class="badge bg-white text-success p-2 fs-6">{{ $ticketStats['completed'] }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="card shadow mb-3">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0">Tiket Terbaru</h5>
                        <form action="#">
                            <select class="pe-4 form-select form-select-sm form-control-clicked" id="statusFilter"
                                name="statusFilter" aria-label="Filter by status">
                                <option value="semua">Tampilkan Semua</option>
                                <option value="belum">Belum Direspon</option>
                                <option value="direspon">Direspon</option>
                                <option value="selesai">Selesai</option>
                            </select>
                        </form>
                    </div>
                    <hr>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered align-items-center align-middle text-center mb-0">
                            <thead>
                                <tr class="text-nowrap">
                                    <th>No</th>
                                    <th>No Tiket</th>
                                    <th>Judul</th>
                                    <th>Layanan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($tickets->count() > 0)
                                    @foreach ($tickets as $index => $ticket)
                                        <tr class="text-nowrap"
                                            data-status="{{ $ticket->status == 0 ? 'belum' : ($ticket->status == 1 ? 'direspon' : 'selesai') }}">
                                            <td>{{ ($tickets->currentPage() - 1) * $tickets->perPage() + $index + 1 }}</td>
                                            <td>{{ $ticket->ticket_code }}</td>
                                            <td>{{ $ticket->title }}</td>
                                            <td>{{ $ticket->service->svc_name ?? 'Tidak ditentukan' }}</td>
                                            <td>
                                                @if ($ticket->status == 0)
                                                    <span class="badge bg-warning">Belum Direspon</span>
                                                @elseif($ticket->status == 1)
                                                    <span class="badge bg-info">Direspon</span>
                                                @else
                                                    <span class="badge bg-success">Selesai</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="alert alert-info m-0">
                                                        Anda belum memiliki tiket. Silakan buka halaman tiket dan klik Buat
                                                        Aduan untuk membuat tiket.
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <hr>
                </div>
            </div>
        </div>

    </div>

@endsection

@section('scripts')
    <script>
        // Set data-status-row attribute for table rows
        document.querySelectorAll('tr[data-status]').forEach(el => {
            el.setAttribute('data-status-row', el.getAttribute('data-status'));
        });

        // Handle select change event for filtering
        document.getElementById('statusFilter').addEventListener('change', function() {
            const filter = this.value;
            const rows = document.querySelectorAll('tr[data-status-row]');

            rows.forEach(row => {
                if (filter === 'semua' || row.getAttribute('data-status-row') === filter) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            // Renumber visible rows
            const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');
            visibleRows.forEach((row, index) => {
                const numberCell = row.querySelector('td:first-child'); // First column (No)
                if (numberCell) {
                    numberCell.textContent = index + 1; // Set new row number starting from 1
                }
            });
        });
    </script>
@endsection

@section('footer')
    @include('mobile.master.footer')
@endsection
