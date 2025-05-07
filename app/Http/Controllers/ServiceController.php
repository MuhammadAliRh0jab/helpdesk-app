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
        // Hanya Super_admin (role:1) dapat mengakses index dan show
        $this->middleware('role:1')->only(['index', 'show']);
    }

    public function index()
    {
        $services = Service::with('unit')->get();
        return view('services.index', compact('services'));
    }

    public function show($id)
    {
        $service = Service::with('unit')->findOrFail($id);
        return view('services.show', compact('service'));
    }
}