@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
<style>
    /* Pastikan konten utama tidak bertabrakan dengan sidebar dan header */
    .main-content {
        margin-left: 250px; /* Sesuaikan dengan lebar sidebar */
        padding-top: 80px; /* Sesuaikan dengan tinggi header */
        padding-left: 20px;
        padding-right: 20px;
        min-height: calc(100vh - 80px); /* Mengisi sisa tinggi layar */
        background-color: #f1f5f9;
        transition: margin-left 0.3s ease;
    }

    /* Styling untuk block konten */
    .block {
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        padding: 0;
    }

    .block-header {
        padding: 15px 20px;
        background-color: #f8f9fa;
        border-bottom: 1px solid #e0e0e0;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
    }

    .block-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #333;
        margin: 0;
    }

    .block-content {
        padding: 20px;
    }

    /* Styling untuk form */
    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        font-weight: 500;
        color: #4B5563;
        margin-bottom: 8px;
        display: block;
    }

    .form-control {
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: 10px 15px;
        font-size: 1rem;
        color: #333;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .form-control:focus {
        border-color: #487FFF;
        box-shadow: 0 0 5px rgba(72, 127, 255, 0.3);
        outline: none;
    }

    /* Styling untuk tombol */
    .btn {
        padding: 10px 20px;
        border-radius: 4px;
        font-weight: 500;
        transition: background-color 0.3s ease, transform 0.1s ease;
    }

    .btn-primary {
        background-color: #487FFF;
        color: #ffffff;
        border: none;
    }

    .btn-primary:hover {
        background-color: #3267D6;
        transform: translateY(-1px);
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    .btn-secondary {
        background-color: #6B7280;
        color: #ffffff;
        border: none;
        margin-left: 10px;
    }

    .btn-secondary:hover {
        background-color: #4B5563;
        transform: translateY(-1px);
    }

    .btn-secondary:active {
        transform: translateY(0);
    }

    /* Pesan error */
    .text-danger {
        font-size: 0.875rem;
        color: #dc3545;
        margin-top: 5px;
    }

    /* Responsivitas */
    @media (max-width: 991px) {
        .main-content {
            margin-left: 0;
            padding-top: 100px; /* Sesuaikan jika header lebih tinggi di mobile */
        }

        .block {
            margin-left: 10px;
            margin-right: 10px;
        }

        .block-title {
            font-size: 1.25rem;
        }

        .form-control {
            font-size: 0.95rem;
            padding: 8px 12px;
        }

        .btn {
            padding: 8px 16px;
            font-size: 0.95rem;
        }
    }

    /* Pastikan elemen tidak bertabrakan */
    * {
        box-sizing: border-box;
    }
</style>

<main id="main-container">
    <div class="main-content">
        <div class="block block-rounded">
            <div class="block-header">
                <h1 class="block-title">Pengaturan Sistem</h1>
            </div>
            <div class="block-content">
                <form action="{{ route('settings.updateMessageLimit') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="form-group">
                        <label for="pengadu_message_limit" class="form-label">Batas Pesan Pengadu (Global untuk Semua Layanan dan Unit)</label>
                        <input type="number" name="pengadu_message_limit" id="pengadu_message_limit" class="form-control" value="{{ $messageLimit->value }}" min="1" required>
                        @error('pengadu_message_limit')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('dashboard.admin') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection