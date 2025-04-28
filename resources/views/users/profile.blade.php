@extends('layouts.app')

@section('title', 'Manajemen Akun Pribadi')

@section('content')
<div>
    <div class="card-header shadow">
        <h1 class="h4 text-white fs-4">Manajemen Akun Pribadi</h1>
        <p class="text-white fs-6">Helpdesk Pemerintah Kota Blitar</p>
    </div>

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

    <div class="bg-white dark:bg-gray-800 p-6 rounded shadow dark:shadow-gray-700 mt-4">
        <!-- View Mode -->
        <div id="view-mode">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama</label>
                <p class="mt-1 p-2 text-gray-900 dark:text-gray-100">{{ $user->name }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Username</label>
                <p class="mt-1 p-2 text-gray-900 dark:text-gray-100">{{ $user->username }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                <p class="mt-1 p-2 text-gray-900 dark:text-gray-100">{{ $user->email ?? 'Tidak ada' }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor Telepon</label>
                <p class="mt-1 p-2 text-gray-900 dark:text-gray-100">{{ $user->phone ?? 'Tidak ada' }}</p>
            </div>

            <div class="mt-6">
                <button type="button" id="edit-btn" class="btn btn-primary">Edit</button>
            </div>
        </div>

        <!-- Edit Mode -->
        <div id="edit-mode" class="hidden">
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
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <button type="button" id="cancel-btn" class="btn btn-secondary ml-2">Batal</button>
                </div>
            </form>
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
