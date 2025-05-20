@extends('layouts.app')

@section('title', 'Daftar Pengguna')

@section('content')
<div id="page-container" class="sidebar-o sidebar-dark enable-page-overlay side-scroll page-header-fixed main-content-narrow">
    <main id="main-container">
        <div class="content">
            <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-center py-2 text-center text-md-start">
                <div class="flex-grow-1 mb-1 mb-md-0">
                    <h1 class="h3 fw-bold mb-2">
                        Kelola Pengguna
                    </h1>
                    <h2 class="h6 fw-medium fw-medium text-muted mb-0">
                        Lihat data, reset password dan menghapus akun pengguna
                    </h2>
                </div>
            </div>
        </div>
        <div>
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
                <div class="row">
                    <div class="col-xl-12">
                        <!-- Default Table -->
                        <div class="block block-rounded">
                            <div class="block-header block-header-default">
                                <h3 class="block-title">Daftar Akun Pengguna</h3>
                            </div>
                            <div class="block-content">
                                <div class="table-responsive">
                                    <table class="table table-vcenter">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Nama</th>
                                                <th>Username</th>
                                                <th>Email</th>
                                                <th>Fungsi</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($users as $user)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td class="p-2 text-dark">{{ $user->name }}</td>
                                                <td class="p-2 text-dark">{{ $user->username }}</td>
                                                <td class="p-2 text-dark">{{ $user->email ?? 'Tidak ada' }}</td>
                                                <td class="p-2 text-dark">{{ $user->getUserFunction() }}</td>
                                                <td class="p-2 text-center">
                                                    <div class="mb-2">
                                                        <!-- Tombol Detail -->
                                                        <button type="button" class="btn btn-primary btn-sm detail-btn"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#userDetailModal"
                                                            data-id="{{ $user->id }}"
                                                            data-name="{{ $user->name }}"
                                                            data-username="{{ $user->username }}"
                                                            data-email="{{ $user->email ?? 'Tidak ada' }}"
                                                            data-phone="{{ $user->phone ?? 'Tidak ada' }}"
                                                            data-unit="{{ $user->unit ? $user->unit->unit_name : 'Tidak ada' }}"
                                                            data-role="{{ $user->role ? $user->role->role_name : 'Tidak ada' }}"
                                                            data-function="{{ $user->getUserFunction() }}"
                                                            title="Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </div>

                                                    <div class="btn-group">
                                                        <!-- Tombol Reset Password -->
                                                        <form action="{{ route('users.resetPassword', $user->id) }}" method="POST" class="d-inline me-1">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-warning"
                                                                onclick="return confirm('Apakah Anda yakin ingin mereset password pengguna ini? Password akan disetel menjadi username mereka.')"
                                                                data-bs-toggle="tooltip">
                                                                <i class="fa fa-fw fa-key" title="Reset Password"></i>
                                                            </button>
                                                        </form>

                                                        @if ($user->role_id != 1)
                                                        <!-- Tombol Hapus -->
                                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline me-1">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger"
                                                                onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')"
                                                                data-bs-toggle="tooltip">
                                                                <i class="fa fa-fw fa-trash-can" title="Hapus Pengguna"></i>
                                                            </button>
                                                        </form>
                                                        @endif
                                                    </div>

                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- Modal -->
                        <div class="modal fade bg-dark" id="userDetailModal" tabindex="-1" aria-labelledby="userDetailModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="userDetailModalLabel">Detail Pengguna</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Nama:</strong> <span id="modal-name"></span></p>
                                        <p><strong>Username:</strong> <span id="modal-username"></span></p>
                                        <p><strong>Email:</strong> <span id="modal-email"></span></p>
                                        <p><strong>Telepon:</strong> <span id="modal-phone"></span></p>
                                        <p><strong>Unit Kerja:</strong> <span id="modal-unit"></span></p>
                                        <p><strong>Role:</strong> <span id="modal-role"></span></p>
                                        <p><strong>Fungsi:</strong> <span id="modal-function"></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
<style>
    .table-responsive {
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border: none;
    }

    .table th,
    .table td,
    .table tr {
        font-size: 14px !important;
        padding: 20px !important;
    }

    .table-responsive,
    .block-content {
        box-shadow: none !important;
        /* border: none !important; */
    }
</style>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const detailButtons = document.querySelectorAll('.detail-btn');

        detailButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Ambil data dari atribut data-*
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const username = this.getAttribute('data-username');
                const email = this.getAttribute('data-email');
                const phone = this.getAttribute('data-phone');
                const unit = this.getAttribute('data-unit');
                const role = this.getAttribute('data-role');
                const func = this.getAttribute('data-function');

                // Isi data ke dalam modal
                document.getElementById('userDetailModalLabel').textContent = `Detail Pengguna: ${name}`;
                document.getElementById('modal-name').textContent = name;
                document.getElementById('modal-username').textContent = username;
                document.getElementById('modal-email').textContent = email;
                document.getElementById('modal-phone').textContent = phone;
                document.getElementById('modal-unit').textContent = unit;
                document.getElementById('modal-role').textContent = role;
                document.getElementById('modal-function').textContent = func;
            });
        });
    });
</script>
@endsection