@extends('layouts.app')

@section('title', 'Daftar Pengguna')

@section('content')
<div>
    <div class="card-header shadow">
        <h1 class="h4 text-white fs-4">Daftar Pengguna</h1>
        <p class="text-white fs-6">Helpdesk Pemerintah Kota Blitar</p>
    </div>
    @if (session('success'))
    <div class="alert alert-success p-4 mb-4 rounded">
        {{ session('success') }}
    </div>
    @endif

    <div class="table-responsive mb-4 mt-4">
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
                                data-function="{{ $user->getUserFunction() }}">
                            Detail
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Modal -->
        <div class="modal fade" id="userDetailModal" tabindex="-1" aria-labelledby="userDetailModalLabel" aria-hidden="true">
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
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const detailButtons = document.querySelectorAll('.detail-btn');

        detailButtons.forEach(button => {
            button.addEventListener('click', function () {
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