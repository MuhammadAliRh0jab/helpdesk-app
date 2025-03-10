<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:1')->only(['index', 'create', 'store', 'edit', 'update', 'destroy']); // Hanya Super_admin
    }

    public function index()
    {
        $services = Service::with('unit')->get();
        return view('services.index', compact('services'));
    }

    public function create()
    {
        $units = Unit::all();
        return view('services.create', compact('units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'svc_name' => 'required',
            'svc_desc' => 'nullable',
            'svc_icon' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();
        if ($request->hasFile('svc_icon')) {
            $path = $request->file('svc_icon')->store('icons', 'public');
            $data['svc_icon'] = $path;
        }

        Service::create($data);
        return redirect()->route('services.index')->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function show($id)
    {
        $service = Service::with('unit')->findOrFail($id);
        return view('services.show', compact('service'));
    }

    public function edit($id)
    {
        $service = Service::findOrFail($id);
        $units = Unit::all();
        return view('services.edit', compact('service', 'units'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'svc_name' => 'required',
            'svc_desc' => 'nullable',
            'svc_icon' => 'nullable|image|max:2048',
        ]);

        $service = Service::findOrFail($id);
        $data = $request->all();

        if ($request->hasFile('svc_icon')) {
            if ($service->svc_icon && Storage::exists('public/' . $service->svc_icon)) {
                Storage::delete('public/' . $service->svc_icon);
            }
            $path = $request->file('svc_icon')->store('icons', 'public');
            $data['svc_icon'] = $path;
        }

        $service->update($data);
        return redirect()->route('services.index')->with('success', 'Layanan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        if ($service->svc_icon && Storage::exists('public/' . $service->svc_icon)) {
            Storage::delete('public/' . $service->svc_icon);
        }
        $service->delete();
        return redirect()->route('services.index')->with('success', 'Layanan berhasil dihapus.');
    }
}