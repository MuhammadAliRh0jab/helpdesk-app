<x-layouts.auth>
    <form class="form w-100" novalidate="novalidate" id="kt_sign_in_form" data-kt-redirect-url="{{ route('landing') }}" action="{{ route('register') }}" method="POST">
        @csrf

        <div class="text-center mb-11">
            <h1 class="text-gray-900 fw-bolder mb-3">Buat Akun</h1>
            <div class="text-gray-500 fw-semibold fs-6">Helpdesk Pemerintah Kota Blitar</div>
        </div>

        {{-- Name --}}
        <div class="fv-row mb-8">
            <label class="form-label text-gray-700 dark:text-gray-300">Nama *</label>
            <input type="text" name="name" placeholder="Masukkan Nama" class="form-control bg-transparent" value="{{ old('name') }}" required />
            @error('name') <div class="text-danger fs-7 mt-2">{{ $message }}</div> @enderror
        </div>

        {{-- Username --}}
        <div class="fv-row mb-8">
            <label class="form-label text-gray-700 dark:text-gray-300">Username *</label>
            <input type="text" name="username" placeholder="Masukkan Username" class="form-control bg-transparent" value="{{ old('username') }}" required />
            @error('username') <div class="text-danger fs-7 mt-2">{{ $message }}</div> @enderror
        </div>

        {{-- Email --}}
        <div class="fv-row mb-8">
            <label class="form-label text-gray-700 dark:text-gray-300">Email (Opsional)</label>
            <input type="email" name="email" placeholder="Masukkan Email" class="form-control bg-transparent" value="{{ old('email') }}" />
            @error('email') <div class="text-danger fs-7 mt-2">{{ $message }}</div> @enderror
        </div>

        {{-- Phone --}}
        <div class="fv-row mb-8">
            <label class="form-label text-gray-700 dark:text-gray-300">Telepon (Opsional)</label>
            <input type="text" name="phone" placeholder="Masukkan Telepon" class="form-control bg-transparent" value="{{ old('phone') }}" />
            @error('phone') <div class="text-danger fs-7 mt-2">{{ $message }}</div> @enderror
        </div>

        {{-- Password --}}
        <div class="fv-row mb-3">
            <label class="form-label text-gray-700 dark:text-gray-300">Password *</label>
            <input type="password" name="password" placeholder="Masukkan Password" class="form-control bg-transparent" required />
            @error('password') <div class="text-danger fs-7 mt-2">{{ $message }}</div> @enderror
        </div>

        {{-- Konfirmasi Password --}}
        <div class="fv-row mb-3">
            <label class="form-label text-gray-700 dark:text-gray-300">Konfirmasi Password *</label>
            <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" class="form-control bg-transparent" required />
            @error('password_confirmation') <div class="text-danger fs-7 mt-2">{{ $message }}</div> @enderror
        </div>

        {{-- Hidden Role ID --}}
        <input type="hidden" name="role_id" value="4">

        <div class="d-grid mb-10">
            <button type="submit" class="btn btn-primary">
                <span class="indicator-label">Daftar</span>
                <span class="indicator-progress">Mohon Tunggu...
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                </span>
            </button>
        </div>

        <div class="text-gray-500 text-center fw-semibold fs-6">
            Sudah Punya Akun? <a href="{{ route('login') }}" class="link-primary">Masuk</a>
        </div>
    </form>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if (session('success'))
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: '{{ session('success') }}', timer: 2000, showConfirmButton: false }).then(() => {
                    window.location.href = '{{ route('login') }}';
                });
            @endif
            @if (session('error'))
                Swal.fire({ icon: 'error', title: 'Gagal!', html: '{{ nl2br(session('error')) }}', confirmButtonText: 'Coba Lagi' });
            @endif
        });
    </script>
</x-layouts.auth>
