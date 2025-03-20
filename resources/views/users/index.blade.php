@extends('layouts.app')

@section('title', 'Daftar Pengguna')

@section('content')
<div>
    <h1 class="h4 mb-4 text-dark fs-2">Daftar Pengguna</h1>
    @if (session('success'))
    <div class="alert alert-success p-4 mb-4 rounded">
        {{ session('success') }}
    </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th class="p-2 text-dark">Nama</th>
                    <th class="p-2 text-dark">Username</th>
                    <th class="p-2 text-dark">Email</th>
                    <th class="p-2 text-dark">Fungsi</th>
                    <th class="p-2 text-dark">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td class="p-2 text-dark">{{ $user->name }}</td>
                    <td class="p-2 text-dark">{{ $user->username }}</td>
                    <td class="p-2 text-dark">{{ $user->email ?? 'Tidak ada' }}</td>
                    <td class="p-2 text-dark">{{ $user->getUserFunction() }}</td>
                    <td class="p-2">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#userDetailModal">
                            Detail
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="modal fade" id="userDetailModal" tabindex="-1" aria-labelledby="userDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userDetailModalLabel">Detail Pengguna: {{ $user->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Nama:</strong> {{ $user->name }}</p>
                <p><strong>Username:</strong> {{ $user->username }}</p>
                <p><strong>Email:</strong> {{ $user->email ?? 'Tidak ada' }}</p>
                <p><strong>Telepon:</strong> {{ $user->phone ?? 'Tidak ada' }}</p>
                <p><strong>Unit Kerja:</strong> {{ $user->unit ? $user->unit->unit_name : 'Tidak ada' }}</p>
                <p><strong>Role:</strong> {{ $user->role ? $user->role->role_name : 'Tidak ada' }}</p>
                <p><strong>Fungsi:</strong> {{ $user->getUserFunction() }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
    </div>
</div>
@endsection