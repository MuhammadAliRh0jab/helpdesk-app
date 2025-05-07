<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class RegisteredUserController extends Controller
{
    /**
     * Tampilkan halaman registrasi.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Proses penyimpanan registrasi user baru.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:' . User::class,
                'email' => 'nullable|email|max:255|unique:' . User::class,
                'phone' => 'nullable|string|max:255',
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'role_id' => 'required|in:4',
            ]);            

            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role_id' => 4,       // Ditetapkan sebagai 4
                'unit_id' => null,    // Tidak memiliki unit
            ]);

            event(new Registered($user));
            Auth::login($user);

            session()->flash('success', 'Registrasi berhasil! Anda akan diarahkan ke halaman login.');
            return redirect()->route('register');

        } catch (\Illuminate\Validation\ValidationException $e) {
            session()->flash('error', 'Registrasi gagal. Silakan periksa kembali data Anda.');
            return redirect()->route('register')->withErrors($e)->withInput();

        } catch (QueryException $e) {
            Log::error('SQL Error during registration', [
                'message' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
                'exception' => $e,
                'request_data' => $request->except(['password', 'password_confirmation']),
            ]);

            session()->flash('error', 'Registrasi gagal karena kesalahan database.');
            return redirect()->route('register')->withInput();

        } catch (\Exception $e) {
            Log::error('Unexpected error during registration', [
                'message' => $e->getMessage(),
                'exception' => $e,
                'request_data' => $request->except(['password', 'password_confirmation']),
            ]);

            session()->flash('error', 'Terjadi kesalahan. Silakan coba lagi nanti.');
            return redirect()->route('register')->withInput();
        }
    }
}
