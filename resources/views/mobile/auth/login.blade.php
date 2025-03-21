@extends('mobile.master.app')

@section('content')

<div class="login-back-button">
    <a href="{{ route('landing') }}">
        <i class="bi bi-arrow-left-short"></i>
    </a>
</div>

<div class="login-wrapper d-flex align-items-center justify-content-center">
    <div class="custom-container">
        <div class="text-center px-4">
            <img class="login-intro-img" src="{{asset('mobile/img/bg-img/36.png')}}" alt="">
        </div>

        <div class="register-form mt-4">
            <h6 class="mb-3 text-center">Silahkan Masuk untuk melanjutkan pengaduan</h6>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <input class="form-control form-control-clicked" type="username" id="username" name="username" placeholder="Masukkan username" required>
                </div>

                <div class="form-group position-relative">
                    <input class="form-control form-control-clicked" id="psw-input" name="password" type="password" placeholder="Masukkan password" required>
                    <div class="position-absolute" id="password-visibility">
                        <i class="bi bi-eye"></i>
                        <i class="bi bi-eye-slash"></i>
                    </div>
                </div>

                <button class="btn btn-primary w-100" type="submit">Masuk</button>
            </form>
        </div>

        <div class="login-meta-data text-center">
            <a class="stretched-link forgot-password d-block mt-3 mb-1" href="#">Lupa Password?</a>
            <p class="mb-0">Belum Punya Akun? <a class="stretched-link" href="{{ route('register') }}">Daftar Sekarang</a></p>
        </div>
    </div>
</div>

@endsection
