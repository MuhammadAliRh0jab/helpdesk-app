<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:1')->only(['index', 'create', 'store', 'edit', 'update', 'destroy']); // Hanya Super_admin
    }

    public function index()
    {
        $units = Unit::all();
        return view('units.index', compact('units'));
    }

    public function create()
    {
        return view('units.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'unit_name' => 'required',
            'unit_code' => 'required|unique:units',
            'unit_short' => 'required',
            'address' => 'nullable',
        ]);

        Unit::create($request->all());
        return redirect()->route('units.index')->with('success', 'Unit berhasil ditambahkan.');
    }

    public function show($id)
    {
        $unit = Unit::findOrFail($id);
        return view('units.show', compact('unit'));
    }

    public function edit($id)
    {
        $unit = Unit::findOrFail($id);
        return view('units.edit', compact('unit'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'unit_name' => 'required',
            'unit_code' => 'required|unique:units,unit_code,' . $id,
            'unit_short' => 'required',
            'address' => 'nullable',
        ]);

        $unit = Unit::findOrFail($id);
        $unit->update($request->all());
        return redirect()->route('units.index')->with('success', 'Unit berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $unit = Unit::findOrFail($id);
        $unit->delete();
        return redirect()->route('units.index')->with('success', 'Unit berhasil dihapus.');
    }
}