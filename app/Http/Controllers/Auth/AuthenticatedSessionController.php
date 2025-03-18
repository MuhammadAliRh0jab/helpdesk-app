<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
    $user = auth()->user();
    $isPicActive = Pic::where('user_id', $user->id)
    ->where('pic_stats', 'active')
    ->exists();

    Log::info('User logged in: ' . $user->id . ', Role: ' . $user->role_id . ', Is PIC Active: ' . ($isPicActive ? 'Yes' : 'No') . ', Redirect to: ' . url()->current());

    if ($user->role_id == 4) { // Warga Kota (role_id = 4)
        return redirect()->intended(route('dashboard.warga'));
    } elseif ($user->role_id == 3) { // Pegawai (PIC, role_id = 3)
        {return redirect()->intended(route('dashboard.pegawai'));}
    } elseif ($user->role_id == 2) { // Operator (role_id = 2)
        return redirect()->intended(route('dashboard.operator'));
    } elseif ($user->role_id == 1) { // Super_admin (role_id = 1)
        return redirect()->intended(route('dashboard.admin'));
    }

    return redirect()->intended(route('dashboard.admin'));
}
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
