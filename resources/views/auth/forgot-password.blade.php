<x-layouts.auth>

    <form class="form w-100" novalidate="novalidate" id="kt_password_reset_form" data-kt-redirect-url="{{ route('login') }}" action="{{ route('password.email') }}">
        @csrf
        <div class="text-center mb-10">
            <h1 class="text-gray-900 fw-bolder mb-3">Lupa Kata Sandi?</h1>
            <div class="text-gray-500 fw-semibold fs-6">Masukkan email Anda untuk mengatur ulang kata sandi.</div>
        </div>

        <div class="fv-row mb-8">
            <input type="text" placeholder="Email" name="email" autocomplete="off" class="form-control bg-transparent" required/>
        </div>

        <div class="d-flex flex-wrap justify-content-center pb-lg-0">
            <button type="submit" id="kt_password_reset_submit" class="btn btn-danger me-4">
                @include('layouts/partials/_button-indicator', ['label' => 'Kirim'])
            </button>
            <a href="{{ route('login') }}" class="btn btn-dark">Batal</a>
        </div>
    </form>

</x-layouts.auth>