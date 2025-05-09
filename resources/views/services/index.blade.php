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
                        Layanan dapat diaktifkan atau dinonaktifkan sesuai dengan kebutuhan
                    </h2>
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

        <div class="content">
            <!-- Dynamic Table Full -->
            <div class="block block-rounded">
                <div class="block-content block-content-full overflow-x-auto">
                    <table class="table table-bordered table-striped table-vcenter js-dataTable-full">
                        <thead>
                            <tr>
                                <th class="p-3 text-secondary">Nama Layanan</th>
                                <th class="p-3 text-secondary">Unit</th>
                                <th class="p-3 text-secondary">Kategori</th>
                                <th class="p-3 text-secondary">Status</th>
                                <th class="p-3 text-secondary">Izinkan Tamu</th>
                                <th class="p-3 text-secondary text-center">Aksi</th>
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
                                            <button type="submit" class="btn btn-primary btn-sm" title="Simpan">
                                                <i class="fas fa-save"></i>
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
                                            <button type="submit" class="btn btn-primary btn-sm" title="Simpan">
                                                <i class="fas fa-save"></i>
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
                                            <button type="submit" class="btn btn-primary btn-sm" title="Simpan" {{ $service->category_id != 2 ? 'disabled' : '' }}>
                                                <i class="fas fa-save"></i>
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
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>
<style>
    .form-select.form-select-sm {
        width: 120px;
        font-size: 14px;
        border: 1px solid #ced4da;
        border-radius: 4px;
    }
</style>
@endsection