<x-layouts.auth>
    <form class="form w-100" novalidate="novalidate" id="kt_sign_in_form" data-kt-redirect-url="{{ route('landing') }}" action="{{ route('login') }}" method="POST">
        @csrf

        <div class="text-center mb-11">
            <h1 class="text-gray-900 fw-bolder mb-3">Masuk Akun</h1>
            <div class="text-gray-500 fw-semibold fs-6">Helpdesk Pemerintah Kota Blitar</div>
        </div>
        @if (session('success'))
        <div class="alert alert-success bg-light-success text-success p-4 mb-4">
            {{ session('success') }}
        </div>
        @endif

        <div class="fv-row mb-8">
            <label>Username</label>
            <input type="text" placeholder="Username" name="username" autocomplete="off" class="form-control bg-transparent" value="{{ old('email') }}" required />
            @error('username')
            <div class="text-danger fs-7 mt-2">{{ $message }}</div>
            @endif
        </div>

        <div class="fv-row mb-3">
            <label>Password</label>
            <div class="position-relative">
                <input type="password" placeholder="Password" name="password" autocomplete="off" class="form-control bg-transparent pe-12" id="password" />
                <span class="position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer" onclick="togglePassword()">
                    <i class="bi bi-eye-slash" id="togglePasswordIcon"></i>
                </span>
            </div>
            @error('password')
            <div class="text-danger fs-7 mt-2">{{ $message }}</div>
            @endif
        </div>

        <div class="d-grid mb-10">
            <button type="submit" id="kt_sign_in_submit" class="btn btn-primary">
                <span class="indicator-label">Masuk</span>
                <span class="indicator-progress">Mohon Tunggu...
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                </span>
            </button>
        </div>

        <div class="text-gray-500 text-center fw-semibold fs-6">
            Belum Punya Akun?
            <a href="{{ route('register') }}" class="link-primary">Daftar</a>
        </div>
    </form>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePasswordIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            }
        }
    </script>
</x-layouts.auth>