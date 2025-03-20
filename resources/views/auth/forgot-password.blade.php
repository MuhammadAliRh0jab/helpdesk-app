<x-layouts.auth>
    <form class="w-100" id="kt_password_reset_form" data-kt-redirect-url="{{ route('login') }}" action="{{ route('password.email') }}" method="POST">
        @csrf
        <div class="text-center mb-4">
            <h1 class="h4 fw-bold mb-3 text-dark">Lupa Kata Sandi?</h1>
            <div class="text-muted small">Masukkan email Anda untuk mengatur ulang kata sandi.</div>
        </div>

        <div class="mb-3">
            <input type="email" placeholder="Email" name="email" autocomplete="off" class="form-control" required />
        </div>

        <div class="d-flex justify-content-center gap-3">
            <button type="submit" id="kt_password_reset_submit" class="btn btn-danger">
                @include('layouts/partials/_button-indicator', ['label' => 'Kirim'])
            </button>
            <a href="{{ route('login') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</x-layouts.auth>