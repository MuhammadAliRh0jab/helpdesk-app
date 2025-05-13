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
                    @if (auth()->user()->role_id == 2)
                    <a href="{{ route('services.create') }}" class="btn btn-primary btn-sm mt-2">
                        <i class="fas fa-plus"></i> Tambah Layanan
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @if (session('success'))
        <div id="success-toast" style="position: fixed; top: 4rem; right: 1rem; background-color: #16a34a; color: white; padding: 0.5rem 1rem; border-radius: 1rem; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); display: flex; align-items: center; gap: 0.5rem; z-index: 1000; max-width: 320px; opacity: 1; visibility: visible;" class="animate-slide-in">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
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
                                <td class="p-3 text-center">
                                    @if (auth()->user()->role_id == 2)
                                    <a href="{{ route('services.edit', $service->id) }}" class="btn btn-primary btn-sm" title="Edit Layanan">
                                        <i class="fas fa-edit"></i>
                                    </a>
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

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
            transform: translateX(100%);
        }
    }

    .animate-slide-in {
        animation: slideIn 0.5s ease-out forwards;
    }

    .animate-fade-out {
        animation: fadeOut 0.5s ease-in forwards;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const successToast = document.getElementById('success-toast');
        if (successToast) {
            setTimeout(() => {
                successToast.classList.remove('animate-slide-in');
                successToast.classList.add('animate-fade-out');
            }, 2000); // Auto-dismiss after 3 seconds
        }
    });
</script>

@endsection