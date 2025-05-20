@extends('mobile.master.app')

@section('title', 'Dashboard')

@section('header')
    @include('mobile.master.header')
@endsection

@section('sidenav')
    @include('mobile.master.sidenav')
@endsection

@section('content')

@if ($errors->any())
<div class="alert alert-danger p-4 mb-4 rounded">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="page-content-wrapper py-3">
    <div class="container">
        <div class="affan-element-item">
            <div class="element-heading-wrapper">
                <i class="bi bi-person"></i>
                <div class="heading-text">
                    <h5 class="mb-1">Manajemen Akun Pribadi</h5>
                    <span>Data akun ditampilkan di bawah</span>
                </div>
            </div>
        </div>

        <div class="container">
            <div id="view-mode">
                <div class="card user-info-card mb-3">
                    <div class="card-body d-flex align-items-center">
                        <div class="user-profile me-3">
                            <img src="{{asset('mobile/img/bg-img/2.jpg')}}" alt="">
                        </div>
                        <div class="user-info">
                            <div class="d-flex align-items-center">
                                <h5 class="mb-1">{{ $user->name }}</h5>
                            </div>
                            <p class="mb-0">
                                @if ($user->role_id == 4)
                                    Warga
                                @else
                                    Bukan Warga
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card user-data-card">
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label class="form-label" for="name">Nama</label>
                            <input class="form-control" id="name" type="text" value="{{ $user->name }}" placeholder="Nama"
                                readonly>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label" for="username">Username</label>
                            <input class="form-control" id="username" type="text" value="{{ $user->username }}" placeholder="Username"
                                readonly>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label" for="email">Email</label>
                            <input class="form-control" id="email" type="text" value="{{ $user->email ?? 'Tidak ada' }}" placeholder="Email"
                                readonly>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label" for="phone">Nomor Telepon</label>
                            <input class="form-control" id="phone" type="text" value="{{ $user->phone ?? 'Tidak ada' }}" placeholder="Nomor Telepon"
                                readonly>
                        </div>

                        <button class="btn btn-primary w-100 mt-3" id="edit-btn" type="button">Edit Data</button>

                        @if (session('success'))
                            <div class="alert custom-alert-three alert-primary alert-dismissible fade show m-4" role="alert">
                                <i class="bi bi-check-circle"></i>
                                <div class="alert-text">
                                    <h6>Berhasil Mengubah Data!</h6>
                                    <span>{{ session('success') }}</span>
                                </div>
                                <button class="btn btn-close position-relative p-1 ms-auto" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div id="edit-mode" class="hidden">
                <div class="card user-data-card">
                    <div class="card-body">
                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <label class="form-label" for="name">Nama</label>
                                <input class="form-control form-control-clicked" name="name" id="name" type="text" value="{{ $user->name }}" disabled>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label" for="username">Username</label>
                                <input class="form-control form-control-clicked" name="username" id="username" type="text" value="{{ $user->username }}" disabled>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label" for="email">Email</label>
                                <input class="form-control form-control-clicked" name="email" id="email" type="email" value="{{ $user->email }}">
                                @error('email')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label" for="phone">Nomor Telepon</label>
                                <input class="form-control form-control-clicked" name="phone" id="phone" type="number" value="{{ $user->phone }}">
                                @error('email')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label" for="password">Password Baru (kosongkan jika tidak ingin mengubah)</label>
                                <input class="form-control form-control-clicked" name="password" id="password" type="password">
                                @error('email')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label" for="password_confirmation">Konfirmasi Password Baru</label>
                                <input class="form-control form-control-clicked" name="password_confirmation" id="password_confirmation" type="password">
                            </div>

                            <button class="btn btn-primary w-100 mt-3" type="submit">Simpan Perubahan</button>

                            <button class="btn btn-danger w-100 mt-3" id="cancel-btn" type="button">Batal</button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Fallback CSS jika Bootstrap tidak dimuat */
    .hidden {
        display: none !important;
    }
</style>

@endsection

@section('scripts')
<script>
    console.log('Script loaded'); // Debugging: Pastikan script dimuat

    document.addEventListener('DOMContentLoaded', function () {
        console.log('DOM fully loaded'); // Debugging: Pastikan DOM dimuat

        const viewMode = document.getElementById('view-mode');
        const editMode = document.getElementById('edit-mode');
        const editBtn = document.getElementById('edit-btn');
        const cancelBtn = document.getElementById('cancel-btn');

        console.log('Elements found:', { viewMode, editMode, editBtn, cancelBtn }); // Debugging: Pastikan elemen ditemukan

        if (editBtn && cancelBtn && viewMode && editMode) {
            editBtn.addEventListener('click', function () {
                console.log('Edit button clicked'); // Debugging
                viewMode.classList.add('hidden');
                editMode.classList.remove('hidden');
            });

            cancelBtn.addEventListener('click', function () {
                console.log('Cancel button clicked'); // Debugging
                editMode.classList.add('hidden');
                viewMode.classList.remove('hidden');
                // Reset form jika dibatalkan
                const form = editMode.querySelector('form');
                if (form) {
                    form.reset();
                    console.log('Form reset'); // Debugging
                }
            });
        } else {
            console.error('One or more elements not found:', { viewMode, editMode, editBtn, cancelBtn });
        }
    });
</script>
@endsection

@section('footer')
  @include('mobile.master.footer')
@endsection
