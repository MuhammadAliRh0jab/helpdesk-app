@extends('layouts.app')

@section('title', 'Kelola Layanan')

@section('content')
<div class="card mt-4">
    <div class="card-body">
        <h6><strong>Kelola Layanan</strong></h6>
        <p>Layanan dapat diaktifkan atau dinonaktifkan sesuai dengan kebutuhan</p>
        <hr>
        <div class="d-flex justify-content-end mb-3">
            @if (auth()->user()->role_id == 2)
                <a href="{{ route('services.create') }}" class="btn btn-primary">
                    Tambah Layanan
                </a>
            @endif
        </div>
    </div>
</div>
@if (session('success'))
<div class="alert alert-success p-4 mb-4 rounded">
    {{ session('success') }}
</div>
@endif
@if (session('error'))
<div class="alert alert-danger p-4 mb-4 rounded">
    {{ session('error') }}
</div>
@endif

<div class="table-responsive mb-4 mt-3">
    <table class="table text-center rounded">
        <thead class="table-light">
            <tr>
                <th class="p-3 text-secondary">Nama Layanan</th>
                <th class="p-3 text-secondary">Unit</th>
                <th class="p-3 text-secondary">Kategori</th>
                <th class="p-3 text-secondary">Status</th>
                <th class="p-3 text-secondary">Izinkan Tamu</th>
                <th class="p-3 text-secondary">Aksi</th>
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
                <td class="p-3 text-dark">
                    {{ $service->allow_guest ? 'Ya' : 'Tidak' }}
                </td>
                <td class="p-3">
                    @if (auth()->user()->role_id == 2)
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <!-- Form untuk Kategori -->
                            <form action="{{ route('services.updateCategory', $service) }}" method="POST" class="d-flex align-items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="category_id" class="form-select form-select-sm">
                                    <option value="1" {{ $service->category_id == 1 ? 'selected' : '' }}>Pemerintah</option>
                                    <option value="2" {{ $service->category_id == 2 ? 'selected' : '' }}>Publik</option>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    Simpan Kategori
                                </button>
                            </form>
                            <!-- Form untuk Status -->
                            <form action="{{ route('services.updateStatus', $service) }}" method="POST" class="d-flex align-items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="form-select form-select-sm">
                                    <option value="active" {{ $service->status == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $service->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    Simpan Status
                                </button>
                            </form>
                            <!-- Form untuk Allow Guest -->
                            <form action="{{ route('services.updateAllowGuest', $service) }}" method="POST" class="d-flex align-items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="allow_guest" class="form-select form-select-sm" {{ $service->category_id != 2 ? 'disabled' : '' }}>
                                    <option value="1" {{ $service->allow_guest == 1 ? 'selected' : '' }}>Ya</option>
                                    <option value="0" {{ $service->allow_guest == 0 ? 'selected' : '' }}>Tidak</option>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm" {{ $service->category_id != 2 ? 'disabled' : '' }}>
                                    Simpan Akses Tamu
                                </button>
                            </form>
                        </div>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="p-2 text-dark text-center">
                    Tidak ada layanan untuk unit Anda.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection