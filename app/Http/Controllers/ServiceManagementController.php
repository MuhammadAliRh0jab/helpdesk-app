<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ServiceManagementController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->role_id != 2) {
            abort(403, 'Anda tidak diizinkan mengakses halaman ini.');
        }

        $services = Service::where('unit_id', $user->unit_id)
            ->with('unit')
            ->get();

        return view('services.index', compact('services'));
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->role_id != 2) {
            abort(403, 'Anda tidak diizinkan mengakses halaman ini.');
        }

        $unit = Unit::findOrFail($user->unit_id);
        $units = collect([$unit]);

        return view('services.create', compact('units'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->role_id != 2) {
            abort(403, 'Anda tidak diizinkan mengakses halaman ini.');
        }

        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'svc_name' => 'required',
            'svc_desc' => 'nullable',
            'svc_icon' => 'nullable|image|max:2048',
            'category_id' => 'required|in:1,2',
            'status' => 'required|in:active,inactive',
            'allow_guest' => 'nullable|boolean',
        ]);

        if ($request->unit_id != $user->unit_id) {
            abort(403, 'Anda tidak diizinkan menambahkan layanan untuk unit lain.');
        }

        $data = $request->all();
        if ($request->hasFile('svc_icon')) {
            $path = $request->file('svc_icon')->store('icons', 'public');
            $data['svc_icon'] = $path;
        }

        $data['allow_guest'] = $request->has('allow_guest') ? 1 : 0;
        if ($data['category_id'] != 2 && $data['allow_guest'] == 1) {
            $data['allow_guest'] = 0; // Force allow_guest to 0 if not public
        }

        Service::create($data);
        return redirect()->route('services.index')->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function edit(Service $service)
    {
        $user = Auth::user();
        if ($user->role_id != 2) {
            abort(403, 'Anda tidak diizinkan mengakses halaman ini.');
        }

        if ($service->unit_id != $user->unit_id) {
            abort(403, 'Anda tidak diizinkan mengedit layanan dari unit lain.');
        }

        $unit = Unit::findOrFail($user->unit_id);
        $units = collect([$unit]);

        return view('services.edit', compact('service', 'units'));
    }

    public function update(Request $request, Service $service)
    {
        $user = Auth::user();
        if ($user->role_id != 2) {
            abort(403, 'Anda tidak diizinkan mengubah layanan ini.');
        }

        if ($service->unit_id != $user->unit_id) {
            abort(403, 'Anda tidak diizinkan mengubah layanan dari unit lain.');
        }

        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'svc_name' => 'required',
            'svc_desc' => 'nullable',
            'svc_icon' => 'nullable|image|max:2048',
            'category_id' => 'required|in:1,2',
            'status' => 'required|in:active,inactive',
            'allow_guest' => 'nullable|boolean',
        ]);

        if ($request->unit_id != $user->unit_id) {
            abort(403, 'Anda tidak diizinkan mengubah layanan untuk unit lain.');
        }

        $data = $request->all();
        if ($request->hasFile('svc_icon')) {
            // Delete old icon if it exists
            if ($service->svc_icon) {
                Storage::disk('public')->delete($service->svc_icon);
            }
            $path = $request->file('svc_icon')->store('icons', 'public');
            $data['svc_icon'] = $path;
        } else {
            // Keep existing icon
            $data['svc_icon'] = $service->svc_icon;
        }

        $data['allow_guest'] = $request->has('allow_guest') ? 1 : 0;
        if ($data['category_id'] != 2 && $data['allow_guest'] == 1) {
            $data['allow_guest'] = 0; // Force allow_guest to 0 if not public
        }

        $service->update($data);
        return redirect()->route('services.index')->with('success', 'Layanan berhasil diperbarui.');
    }
}