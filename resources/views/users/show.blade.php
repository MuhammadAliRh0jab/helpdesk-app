@extends('layouts.app')

@section('title', 'Detail Pengguna')

@section('content')
    <h1 class="text-2xl font-bold mb-4 text-gray-800 dark:text-gray-200">Detail Pengguna</h1>

    <div class="bg-white dark:bg-gray-800 p-6 rounded shadow dark:shadow-gray-700">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">ID</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ $user->id }}</p>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Username</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ $user->username }}</p>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Nama</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ $user->name }}</p>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Role</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ $user->role ? $user->role->name : 'Tidak ada role' }}</p>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Unit</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ $user->unit ? $user->unit->name : 'Tidak ada unit' }}</p>
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('users.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 transition duration-200">
                Kembali
            </a>
        </div>
    </div>
@endsection