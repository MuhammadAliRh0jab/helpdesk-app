@extends('mobile.master.app')

@section('title', 'Register')

@section('content')
    <div class="login-back-button">
        <a href="{{ route('login') }}">
            <i class="bi bi-arrow-left-short"></i>
        </a>
    </div>

    <div class="login-wrapper d-flex align-items-center justify-content-center">
        <div class="custom-container">
            <div class="text-center px-4">
                <img class="login-intro-img" src="{{ asset('mobile/img/bg-img/36.png') }}" alt="">
            </div>

            <div class="register-form mt-4">
                <h6 class="mb-3 text-center">Silahkan Daftar terlebih dahulu untuk memulai pengaduan</h6>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show p-3 mb-3" role="alert">
                        <strong>Berhasil!</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show p-3 mb-3" role="alert">
                        <strong>Gagal!</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('register') }}" method="POST" id="registerForm">
                    @csrf
                    <input type="hidden" name="role_id" value="4">

                    <div class="form-group text-start mb-3">
                        <input class="form-control" type="text" name="name" placeholder="Nama"
                            value="{{ old('name') }}" required>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group text-start mb-3">
                        <input class="form-control" type="text" name="username" placeholder="Username"
                            value="{{ old('username') }}" required>
                        @error('username')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group text-start mb-3">
                        <input class="form-control" type="email" name="email" placeholder="Email (Opsional)"
                            value="{{ old('email') }}">
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group text-start mb-3">
                        <input class="form-control" type="text" name="phone" placeholder="Telepon (Opsional)"
                            value="{{ old('phone') }}">
                        @error('phone')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group text-start mb-3 position-relative">
                        <input class="form-control" id="psw-input" type="password" name="password" placeholder="Password"
                            required>
                        <div class="position-absolute" id="password-visibility">
                            <i class="bi bi-eye"></i>
                            <i class="bi bi-eye-slash"></i>
                        </div>
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group text-start mb-3 position-relative">
                        <input class="form-control" id="psw-input-confirm" type="password" name="password_confirmation"
                            placeholder="Konfirmasi Password" required>
                        <div class="position-absolute" id="password-visibility-confirm">
                            <i class="bi bi-eye"></i>
                            <i class="bi bi-eye-slash"></i>
                        </div>
                        @error('password_confirmation')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <button class="btn btn-primary w-100" type="submit">Daftar</button>
                </form>
            </div>

            <!-- Login Meta -->
            <div class="login-meta-data text-center">
                <p class="mt-3 mb-0">Sudah punya akun? <a class="stretched-link" href="{{ route('login') }}">Masuk
                        Sekarang</a></p>
            </div>
        </div>
    </div>
@endsection
