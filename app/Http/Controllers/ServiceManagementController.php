<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceManagementController extends Controller
{
    public function index()
    {
        // Pastikan hanya operator (role_id = 2) yang dapat mengakses
        $user = auth()->user();
        if ($user->role_id != 2) {
            abort(403, 'Anda tidak diizinkan mengakses halaman ini.');
        }

        // Ambil layanan hanya dari unit operator yang sedang login
        $services = Service::where('unit_id', $user->unit_id)
            ->with('unit')
            ->get();

        return view('services.index', compact('services'));
    }

    public function updateStatus(Request $request, Service $service)
    {
        // Pastikan hanya operator (role_id = 2) yang dapat mengubah status
        $user = auth()->user();
        if ($user->role_id != 2) {
            abort(403, 'Anda tidak diizinkan mengubah status layanan.');
        }

        // Pastikan layanan yang diubah berasal dari unit operator
        if ($service->unit_id != $user->unit_id) {
            abort(403, 'Anda tidak diizinkan mengubah status layanan dari unit lain.');
        }

        // Validasi input
        $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        // Update status layanan
        $service->update([
            'status' => $request->status,
        ]);

        return redirect()->route('services.index')->with('success', 'Status layanan berhasil diperbarui.');
    }
}