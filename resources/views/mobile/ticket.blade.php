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
                        <a class="btn rounded-pill btn-primary" href="{{ route('mobile.ticket.create') }}">Buat Aduan</a>
                    </div>
                    <hr>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered align-items-center align-middle text-center mb-0">
                            <thead>
                                <tr class="text-nowrap">
                                    <th scope="col">#</th>
                                    <th>No Tiket</th>
                                    <th>Judul Aduan</th>
                                    <th>Status</th>
                                    <th>Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="text-nowrap">
                                    <th>1</th>
                                    <td>TK12409572N0124P</td>
                                    <td>Air Kotor</td>
                                    <td>Pending</td>
                                    <td>17 Agustus 1945</td>
                                    <td>
                                        <a class="btn m-1 rounded-pill btn-primary" href="#">Lihat</a>
                                    </td>
                                </tr>
                                {{-- <tr>
                                    <td colspan="6">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="alert alert-info m-0">
                                                    Anda belum memiliki tiket. Silahkan buka halaman tiket dan klik Buat Aduan untuk membuat tiket.
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr> --}}
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
