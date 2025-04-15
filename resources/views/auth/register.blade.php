<x-layouts.auth>

    <form class="form w-100" novalidate="novalidate" id="kt_sign_in_form" data-kt-redirect-url="{{ route('landing') }}" action="{{ route('register') }}" method="POST">
        @csrf

        <div class="text-center mb-11">
            <h1 class="text-gray-900 fw-bolder mb-3">Buat Akun</h1>
            <div class="text-gray-500 fw-semibold fs-6">Helpdesk Pemerintah Kota Blitar</div>
        </div>
        @if (session('success'))
        <div class="alert alert-success bg-light-success text-success p-4 mb-4">
            {{ session('success') }}
        </div>
        @endif

        <div class="fv-row mb-8">
            <label>Nama *</label>
            <input type="text" placeholder="Masukkan Nama" name="name" autocomplete="off" class="form-control bg-transparent" value="{{ old('name') }}" />
            @error('name')
            <div class="text-danger fs-7 mt-2">{{ $message }}</div>
            @endif
        </div>
        <div class="fv-row mb-8">
            <label>Username *</label>
            <input type="text" placeholder="Masukkan Username" name="username" autocomplete="off" class="form-control bg-transparent" value="{{ old('username') }}" />
            @error('username')
            <div class="text-danger fs-7 mt-2">{{ $message }}</div>
            @endif
        </div>
        <div class="fv-row mb-8">
            <label>Email</label>
            <input type="email" placeholder="Masukkan Email (Opsional)" name="email" autocomplete="off" class="form-control bg-transparent" value="{{ old('email') }}" />
            @error('email')
            <div class="text-danger fs-7 mt-2">{{ $message }}</div>
            @endif
        </div>

        <div class="fv-row mb-8">
            <label>Telp</label>
            <input type="text" placeholder="Masukkan Telepon (Opsional)" name="phone" autocomplete="off" class="form-control bg-transparent" value="{{ old('phone') }}" />
            @error('phone')
            <div class="text-danger fs-7 mt-2">{{ $message }}</div>
            @endif
        </div>

        <div class="fv-row mb-3">
            <label>Password *</label>
            <input type="password" placeholder="Masukkan Password" name="password" autocomplete="off" class="form-control bg-transparent" />
            @error('password')
            <div class="text-danger fs-7 mt-2">{{ $message }}</div>
            @endif
        </div>

        <div class="fv-row mb-3">
            <Label>Konfirmasi Password *</Label>
            <input type="password" placeholder="Konfirmasi Password" name="password_confirmation" autocomplete="off" class="form-control bg-transparent" />
            @error('password')
            <div class="text-danger fs-7 mt-2">{{ $message }}</div>
            @endif
        </div>

        <div class="fv-row mb-3">
            <label for="role_id" autocomplete="off" class="form-label text-gray-700 dark:text-gray-300">Role Pengguna</label>
            <select id="role_id" name="role_id" class="form-control bg-transparent" required>
                <option value="" disabled selected>Pilih Role</option>
                <option value="4" {{ old('role_id') == 4 ? 'selected' : '' }}>Warga Kota</option>
                <option value="3" {{ old('role_id') == 3 ? 'selected' : '' }}>Pegawai</option>
                <option value="2" {{ old('role_id') == 2 ? 'selected' : '' }}>Operator</option>
                <option value="1" {{ old('role_id') == 1 ? 'selected' : '' }}>Super Admin</option>
            </select>
            @error('role_id')
            <div class="text-danger fs-7 mt-2">{{ $message }}</div>
            @enderror
        </div>
        <style>
            #role_id:invalid {
                color: rgba(125, 125, 125, 0.6);
            }
        </style>


        <div class="d-grid mb-10">
            <button type="submit" id="kt_sign_in_submit" class="btn btn-primary">
                <span class="indicator-label">Daftar</span>
                <span class="indicator-progress">Mohon Tunggu...
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                </span>
            </button>
        </div>

        <div class="text-gray-500 text-center fw-semibold fs-6">
            Sudah Punya Akun?
            <a href="{{ route('login') }}" class="link-primary">Masuk</a>
        </div>
    </form>
</x-layouts.auth>