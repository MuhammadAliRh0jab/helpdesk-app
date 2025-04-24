@extends('layouts.app')

@section('title', 'Manajemen Akun Pribadi')

@section('content')


@if (session('success'))
<div class="alert alert-success p-4 mb-4 rounded">
    {{ session('success') }}
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger p-4 mb-4 rounded">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card mt-4">
    <div class="card-body">
        <h6><strong>Informasi Akun</strong></h6>
        <p>Anda dapat mengubah email, telepon dan kata sandi jika diperlukan</p>
        <hr>
    </div>
</div>

<div class="container mt-3">
    <div class="row profile-card bg-white">
        <div class="col-md-4 profile-left d-flex flex-column align-items-center text-center">
            <div class="profile-initial rounded-circle text-white d-flex align-items-center justify-content-center mt-4">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="ms-3 mt-3">
                <span class="text-white">{{ auth()->user()->name }}</span>
                <p class="text-primary small mb-0">Pengguna</p>
            </div>
        </div>
        <div class="col-md-8 profile-right">
            <div class="row" id="view-mode">
                <div class="col-12 mb-3">
                    <div class="row">
                        <div class="col-sm-6 info-item">
                            <strong>Nama</strong><br>
                            <span class="text-muted">{{ $user->name }}</span>
                        </div>
                        <div class="col-sm-6 info-item">
                            <strong>Username</strong><br>
                            <span class="text-muted">{{ $user->username }}</span>
                        </div>
                        <div class="col-sm-6 info-item">
                            <strong>Email</strong><br>
                            <span class="text-muted">{{ $user->email ?? 'Tidak ada' }}</span>
                        </div>
                        <div class="col-sm-6 info-item">
                            <strong>Telepon</strong><br>
                            <span class="text-muted">{{ $user->phone ?? 'Tidak ada' }}</span>
                        </div>
                        <div class="mt-6">
                            <button type="button" id="edit-btn" class="btn btn-primary"><i class="fas fa-pen-to-square"></i> Ubah</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="edit-mode" class="hidden mt-5">
    <form action="{{ route('profile.update') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama</label>
            <input type="text" name="name" id="name" value="{{ $user->name }}" class="form-control mt-1 p-2 border rounded w-full" disabled>
        </div>

        <div class="mb-4">
            <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Username</label>
            <input type="text" name="username" id="username" value="{{ $user->username }}" class="form-control mt-1 p-2 border rounded w-full" disabled>
        </div>

        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
            <input type="email" name="email" id="email" value="{{ $user->email }}" class="form-control mt-1 p-2 border rounded w-full">
            @error('email')
            <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-4">
            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor Telepon</label>
            <input type="text" name="phone" id="phone" value="{{ $user->phone }}" class="form-control mt-1 p-2 border rounded w-full">
            @error('phone')
            <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password Baru (kosongkan jika tidak ingin mengubah)</label>
            <input type="password" name="password" id="password" class="form-control mt-1 p-2 border rounded w-full">
            @error('password')
            <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control mt-1 p-2 border rounded w-full">
        </div>

        <div class="mt-6">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan Perubahan</button>
            <button type="button" id="cancel-btn" class="btn btn-dark ml-2"> <i class="fas fa-times me-2"></i>Batal</button>
        </div>
    </form>
</div>
</div>
</div>


<style>
    .hidden {
        display: none !important;
    }

    .profile-card {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .profile-left {
        background: linear-gradient(to bottom, rgb(86, 123, 244), rgb(16, 53, 146));
        color: white;
        padding: 30px;
        text-align: center;
    }

    .profile-left img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        margin-bottom: 15px;
    }

    .profile-left h5 {
        margin-bottom: 5px;
        font-weight: 600;
    }

    .profile-left p {
        margin-bottom: 15px;
        font-size: 14px;
    }

    .profile-right {
        padding: 30px;
    }

    .social-icons a {
        color: #3b3b3b;
        margin-right: 15px;
        font-size: 18px;
        transition: 0.3s;
    }

    .social-icons a:hover {
        color: #007bff;
    }

    .info-title {
        font-weight: 600;
        margin-bottom: 10px;
    }

    .info-item {
        margin-bottom: 15px;
    }

    .section-title {
        font-weight: 600;
        margin-top: 20px;
        border-top: 1px solid #dee2e6;
        padding-top: 15px;
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    console.log('Script loaded'); // Debugging: Pastikan script dimuat

    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM fully loaded'); // Debugging: Pastikan DOM dimuat

        const viewMode = document.getElementById('view-mode');
        const editMode = document.getElementById('edit-mode');
        const editBtn = document.getElementById('edit-btn');
        const cancelBtn = document.getElementById('cancel-btn');

        console.log('Elements found:', {
            viewMode,
            editMode,
            editBtn,
            cancelBtn
        }); // Debugging: Pastikan elemen ditemukan

        if (editBtn && cancelBtn && viewMode && editMode) {
            editBtn.addEventListener('click', function() {
                console.log('Edit button clicked'); // Debugging
                viewMode.classList.add('hidden');
                editMode.classList.remove('hidden');
            });

            cancelBtn.addEventListener('click', function() {
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
            console.error('One or more elements not found:', {
                viewMode,
                editMode,
                editBtn,
                cancelBtn
            });
        }
    });
</script>
@endsection