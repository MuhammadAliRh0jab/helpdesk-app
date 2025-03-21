@extends('mobile.master.app')

@section('content')

  <div class="login-back-button">
    <a href="{{ route('login') }}">
      <i class="bi bi-arrow-left-short"></i>
    </a>
  </div>

  <div class="login-wrapper d-flex align-items-center justify-content-center">
    <div class="custom-container">
      <div class="text-center px-4">
        <img class="login-intro-img" src="{{asset('mobile/img/bg-img/36.png')}}" alt="">
      </div>

      <div class="register-form mt-4">
        <h6 class="mb-3 text-center">Silahkan Daftar terlebih dahulu untuk memulai pengaduan</h6>

        <form action ="otp.html">
          <div class="form-group text-start mb-3">
            <input class="form-control" type="text" placeholder="Buat email">
          </div>

          <div class="form-group text-start mb-3">
            <input class="form-control" type="text" placeholder="Buat username">
          </div>

          <div class="form-group text-start mb-3 position-relative">
            <input class="form-control" id="psw-input" type="password" placeholder="Buat password">
            <div class="position-absolute" id="password-visibility">
              <i class="bi bi-eye"></i>
              <i class="bi bi-eye-slash"></i>
            </div>
          </div>

          <div class="mb-3" id="pswmeter"></div>

          <div class="form-check mb-3">
            <input class="form-check-input" id="checkedCheckbox" type="checkbox" value="" checked>
            <label class="form-check-label text-muted fw-normal" for="checkedCheckbox">I agree with the terms &amp;
              policy.</label>
          </div>

          <button class="btn btn-primary w-100" type="submit">Daftar</button>
        </form>
      </div>

      <!-- Login Meta -->
      <div class="login-meta-data text-center">
        <p class="mt-3 mb-0">Sudah punya akun? <a class="stretched-link" href="{{ route('login') }}">Masuk Sekarang</a></p>
      </div>
    </div>
  </div>

@endsection
