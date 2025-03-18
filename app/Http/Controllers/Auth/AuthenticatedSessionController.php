<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pic; // Impor model Pic

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('mobile.auth.login'); // Mengembalikan view login kustom
    }

    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // Gunakan query langsung ke tabel pics untuk memeriksa status PIC
        $isPicActive = Pic::where('user_id', $user->id)
                        ->where('pic_stats', 'active')
                        ->exists();

        // Tentukan redirect berdasarkan role_id
        if ($user->role_id == 4) { // Warga Kota (role_id = 4)
            return redirect()->intended(route('tickets.index'));
        } elseif ($user->role_id == 3) { // Pegawai (PIC, role_id = 3)
            if ($isPicActive) {
                // Jika sudah ditugaskan, redirect ke view khusus
                return redirect()->intended(route('tickets.assigned'));
            }
            // Jika belum ditugaskan, redirect ke tickets.index
            return redirect()->intended(route('tickets.index'));
        } elseif ($user->role_id == 2) { // Operator (role_id = 2)
            return redirect()->intended(route('tickets.index'));
        } elseif ($user->role_id == 1) { // Super_admin (role_id = 1)
            return redirect()->intended(route('users.index'));
        }

        // Default redirect ke menu aduan jika role_id tidak dikenali
        return redirect()->intended(route('tickets.index'));
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
