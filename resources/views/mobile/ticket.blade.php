@extends('mobile.master.app')

@section('title', 'Semua Tiket')

@section('header')
  @include('mobile.master.header')
@endsection

@section('sidenav')
  @include('mobile.master.sidenav')
@endsection

@section('content')

<div class="page-content-wrapper py-3">
    <div class="container">
        <div class="affan-element-item">
            <div class="element-heading-wrapper">
                <i class="bi bi-ticket"></i>
                <div class="heading-text">
                    <h5 class="mb-1">Manajemen Tiket</h5>
                    <span>Semua status tiket ditampilkan dibawah</span>
                </div>
            </div>
        </div>

        <div class="py-2">
            <div class="card shadow">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0">Semua Tiket</h5>
                        <a class="btn rounded-pill btn-primary" href="{{ route('tickets.create') }}">Buat Aduan</a>
                    </div>
                    <hr>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered align-items-center align-middle text-center mb-0">
                            <thead>
                                <tr class="text-nowrap">
                                    <th scope="col">#</th>
                                    <th>No Tiket</th>
                                    <th>Judul Aduan</th>
                                    <th>Unit</th>
                                    <th>Layanan</th>
                                    <th>Status</th>
                                    <th>Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($tickets)
                                    @foreach ($tickets as $ticket)
                                        <tr class="text-nowrap">
                                            <th>1</th>
                                            <td>{{ $ticket->ticket_code }}</td>
                                            <td>{{ $ticket->title }}</td>
                                            <td>{{ $ticket->original_unit_id ? \App\Models\Unit::find($ticket->original_unit_id)->unit_name : ($ticket->unit->unit_name ?? 'Tidak ditentukan') }}</td>
                                            <td>{{ $ticket->service->svc_name ?? 'Tidak ditentukan' }}</td>
                                            <td>
                                                @if($ticket->status == 0) Belum Direspon
                                                @elseif($ticket->status == 1) Direspon
                                                @else Selesai
                                                @endif
                                            </td>
                                            <td>{{ $ticket->created_at->format('d-m-Y H:i') }}</td>
                                            <td>
                                                <a class="btn m-1 rounded-pill btn-primary" href="#">Lihat</a>
                                            </td>
                                        </tr>
                                    @endforeach
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
                </div>
            </div>
        </div>
    </div>

    </div>
</div>

@endsection

@section('footer')
  @include('mobile.master.footer')
@endsection
