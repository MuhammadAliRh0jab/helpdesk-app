@extends('layouts.app')

@section('title', 'Kelola Layanan')

@section('content')
<div id="page-container" class="sidebar-o sidebar-dark enable-page-overlay side-scroll page-header-fixed main-content-narrow">
    <main id="main-container">
        <div class="content">
            <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-center py-2 text-center text-md-start">
                <div class="flex-grow-1 mb-1 mb-md-0">
                    <h1 class="h3 fw-bold mb-2">
                        Kelola Layanan
                    </h1>
                    <h2 class="h6 fw-medium fw-medium text-muted mb-0">
                        Menambahkan serta aktivasi layanan
                    </h2>
                </div>
            </div>
        </div>

        @if (session('success'))
        <div class="alert alert-success p-4 mb-4 rounded">
            {{ session('success') }}
        </div>
        @endif
        <div class="content">
            <div class="row">
                <div class="col-xl-12">
                    <!-- Default Table -->
                    <div class="block block-rounded">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Daftar Layanan</h3>
                        </div>
                        <div class="block-content">
                            <table class="table table-vcenter">
                                <thead>
                                    <tr>
                                        <th>Nama Layanan</th>
                                        <th>Unit</th>
                                        <th>Kategori</th>
                                        <th>Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($services as $service)
                                    <tr>
                                        <td class="p-3 text-dark">{{ $service->svc_name }}</td>
                                        <td class="p-3 text-dark">{{ $service->unit->unit_name }}</td>
                                        <td class="p-3 text-dark">
                                            {{ $service->category_id == 1 ? 'Pemerintah' : 'Publik' }}
                                        </td>
                                        <td class="p-3 text-dark">
                                            {{ ucfirst($service->status) }}
                                        </td>
                                        <td class="p-1">
                                            <form action="{{ route('services.updateStatus', $service) }}" method="POST" class="d-flex align-items-center gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <select name="status" class="form-select form-select-sm">
                                                    <option value="active" {{ $service->status == 'active' ? 'selected' : '' }}>Aktif</option>
                                                    <option value="inactive" {{ $service->status == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                                                </select>
                                                <button type="submit" class="btn btn-primary btn-sm" title="Simpan">
                                                    <i class="far fa-floppy-disk"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="p-2 text-dark text-center">
                                            Tidak ada layanan untuk unit Anda.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection