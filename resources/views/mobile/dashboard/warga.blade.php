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
        <div class="card bg-primary my-3 bg-img shadow" style="background-image: url('{{ asset('mobile/img/core-img/1.png') }}')">
            <div class="card-body p-4">
                <h2 class="text-white">Dashboard</h2>
                <h5 class="my-2 text-white">Selamat Datang, {{ Auth::user()->name }}!</h5>
                <small class="text-white">Helpdesk DISKOMINFOTIK Kota Blitar | Sistem Pengaduan Dinas Komunikasi, Informatika, dan Statistik Kota Blitar</small>
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
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between bg-danger text-white mb-2 rounded">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-ticket fs-1 me-3"></i> <!-- Ikon tiket untuk Semua -->
                            <div>
                                <h6 class="mb-0 text-white">Semua</h6>
                            </div>
                        </div>
                        <span class="badge bg-white text-danger p-2 fs-6">{{ $ticketStats['completed'] + $ticketStats['pending'] + $ticketStats['assigned'] }}</span>
                    </a>

                    <!-- Belum Direspon Status -->
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between bg-warning text-white mb-2 rounded">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-chat fs-1 me-3"></i> <!-- Ikon chat untuk Belum Direspon -->
                            <div>
                                <h6 class="mb-0 text-white">Belum Direspon</h6>
                            </div>
                        </div>
                        <span class="badge bg-white text-warning p-2 fs-6">{{ $ticketStats['pending'] }}</span>
                    </a>

                    <!-- Direspon Status -->
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between bg-primary text-white mb-2 rounded">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle fs-1 me-3"></i> <!-- Ikon centang untuk Direspon -->
                            <div>
                                <h6 class="mb-0 text-white">Direspon</h6>
                            </div>
                        </div>
                        <span class="badge bg-white text-primary p-2 fs-6">{{ $ticketStats['assigned'] }}</span>
                    </a>

                    <!-- Selesai Status -->
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between bg-success text-white rounded">
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
                    <a class="btn m-1 rounded-pill btn-primary" href="{{ route('tickets.index')}}">Lihat Semua</a>
                </div>
                <hr>
                <div class="table-responsive mt-3">
                    <table class="table table-bordered align-items-center align-middle text-center mb-0">
                        <thead>
                            <tr class="text-nowrap">
                                <th>No Tiket</th>
                                <th>Judul Aduan</th>
                                <th>Unit</th>
                                <th>Layanan</th>
                                <th>Status</th>
                                <th>Dibuat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($latestTicket)
                                <tr class="text-nowrap">
                                    <td>{{ $latestTicket->ticket_code }}</td>
                                    <td>{{ $latestTicket->title }}</td>
                                    <td>{{ $latestTicket->original_unit_id ? \App\Models\Unit::find($latestTicket->original_unit_id)->unit_name : ($latestTicket->unit->unit_name ?? 'Tidak ditentukan') }}</td>
                                    <td>{{ $latestTicket->service->svc_name ?? 'Tidak ditentukan' }}</td>
                                    <td>
                                        @if($latestTicket->status == 0) Belum Direspon
                                        @elseif($latestTicket->status == 1) Direspon
                                        @else Selesai
                                        @endif
                                    </td>
                                    <td>{{ $latestTicket->created_at->format('d-m-Y H:i') }}</td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="6">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="alert alert-info m-0">
                                                    Anda belum memiliki tiket. Silahkan buka halaman tiket dan klik Buat Aduan untuk membuat tiket.
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

@section('footer')
  @include('mobile.master.footer')
@endsection
