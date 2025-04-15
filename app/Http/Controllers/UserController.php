<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:1')->only(['index', 'show', 'resetPassword', 'destroy']); // Hanya Super_admin
    }

    public function index()
    {
        $users = User::with('role', 'unit')->get();
        return view('users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::with('role', 'unit')->findOrFail($id);
        return view('users.show', compact('user'));
    }

    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $newPassword = $user->username;
        $user->password = Hash::make($newPassword);
        $user->save();

        Log::info('Password reset for user: ' . $user->id . ' by Super_admin: ' . auth()->user()->id);

        return redirect()->route('users.index')->with('success', 'Password pengguna telah direset menjadi username mereka.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->role_id == 1) {
            return redirect()->route('users.index')->with('error', 'Tidak dapat menghapus akun Super_admin.');
        }

        $user->delete();

        Log::info('User deleted: ' . $id . ' by Super_admin: ' . auth()->user()->id);

        return redirect()->route('users.index')->with('success', 'Pengguna telah dihapus.');
    }

    // Method untuk menampilkan halaman profil
    public function profile()
    {
        $user = auth()->user();
        return view('users.profile', compact('user'));
    }

    // Method untuk memperbarui informasi akun
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:15',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Update email
        if ($request->filled('email')) {
            $user->email = $validated['email'];
        }

        // Update phone
        if ($request->filled('phone')) {
            $user->phone = $validated['phone'];
        }

        // Update password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        Log::info('User updated profile: ' . $user->id);

        return redirect()->route('profile')->with('success', 'Informasi akun berhasil diperbarui.');
    }
}