@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
<style>
    .main-content {
        margin-left: 250px;
        padding-top: 80px;
        padding-left: 20px;
        padding-right: 20px;
        min-height: calc(100vh - 80px);
        background-color: #f1f5f9;
        transition: margin-left 0.3s ease;
    }

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

    .btn-primary {
        background-color: #0287ff;
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

    /* Pesan error */
    .text-danger {
        font-size: 0.875rem;
        color: #dc3545;
        margin-top: 5px;
    }


    @media (max-width: 991px) {
        .main-content {
            margin-left: 0;
            padding-top: 100px;
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

    * {
        box-sizing: border-box;
    }
</style>
<div id="page-container" class="sidebar-o sidebar-dark enable-page-overlay side-scroll page-header-fixed main-content-narrow">
    <main id="main-container">
        <div class="content">
            <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-center py-2 text-center text-md-start">
                <div class="flex-grow-1 mb-1 mb-md-0">
                    <h1 class="h3 fw-bold mb-2">
                        Pengaturan Sistem
                    </h1>
                    <h2 class="h6 fw-medium fw-medium text-muted mb-0">
                        Pengaturan sistem
                    </h2>
                </div>
            </div>
        </div>
        <div>
            <div class="content">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="block block-rounded">
                            <div class="block-header block-header-default">
                                <h3 class="block-title">Batas Pesan Pengadu (Global untuk Semua Layanan dan Unit)</h3>
                            </div>
                            <div class="chat-limit p-4 bg-white">
                                <form action="{{ route('settings.updateMessageLimit') }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="form-group">
                                        <label for="pengadu_message_limit" class="form-label">Atur batas limit</label>
                                        <input type="number" name="pengadu_message_limit" id="pengadu_message_limit" class="form-control" value="{{ $messageLimit->value }}" min="1" required>
                                        @error('pengadu_message_limit')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group text-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i> Simpan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection