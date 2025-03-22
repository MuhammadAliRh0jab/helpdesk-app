@extends('layouts.app')

@section('title', 'Kelola Layanan')

@section('content')
    <div class="card-header shadow">
        <h1 class="h4 text-white fs-4">Kelola Layanan</h1>
        <p class="text-white fs-6">Helpdesk Pemerintah Kota Blitar</p>
    </div>
    @if (session('success'))
    <div class="alert alert-success p-4 mb-4 rounded">
        {{ session('success') }}
    </div>
    @endif

    <div class="table-responsive mb-4 mt-3">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th class="p-2 text-dark">Nama Layanan</th>
                    <th class="p-2 text-dark">Unit</th>
                    <th class="p-2 text-dark">Kategori</th>
                    <th class="p-2 text-dark">Status</th>
                    <th class="p-2 text-dark">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($services as $service)
                <tr>
                    <td class="p-2 text-dark">{{ $service->svc_name }}</td>
                    <td class="p-2 text-dark">{{ $service->unit->unit_name }}</td>
                    <td class="p-2 text-dark">
                        {{ $service->category_id == 1 ? 'Pemerintah' : 'Publik' }}
                    </td>
                    <td class="p-2 text-dark">
                        {{ ucfirst($service->status) }}
                    </td>
                    <td class="p-2">
                        <form action="{{ route('services.updateStatus', $service) }}" method="POST" class="d-flex align-items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="form-select form-select-sm">
                                <option value="active" {{ $service->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $service->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm">
                                Simpan
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
    @endsection