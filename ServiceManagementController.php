<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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

    public function generateQrCode(Service $service)
    {
        $user = Auth::user();
        if ($user->role_id != 2 || $service->unit_id != $user->unit_id) {
            abort(403, 'Anda tidak diizinkan mengakses layanan ini.');
        }

        // Generate the URL for the reporting form using uuid
        $url = route('tickets.create.service', ['uuid' => $service->uuid]);

        // Generate QR code as SVG or PNG
        $qrCode = QrCode::size(200)->generate($url);

        return view('services.qrcode', compact('service', 'qrCode', 'url'));
    }

    public function downloadQrCode(Service $service)
    {
        $user = Auth::user();
        if ($user->role_id != 2 || $service->unit_id != $user->unit_id) {
            abort(403, 'Anda tidak diizinkan mengakses layanan ini.');
        }

        // Generate the URL using uuid
        $url = route('tickets.create.service', ['uuid' => $service->uuid]);
        $qrCode = QrCode::format('png')->size(200)->generate($url);

        return response($qrCode)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="qrcode_service_' . $service->uuid . '.png"');
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
            abort(403, 'Anda tidak diizinkan membuat layanan.');
        }

        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'svc_name' => 'required|string|max:255',
            'svc_desc' => 'nullable|string',
            'svc_icon' => 'nullable|image|max:2048',
            'category_id' => 'required|in:1,2',
            'status' => 'required|in:active,inactive',
            'allow_guest' => 'nullable|boolean',
        ]);

        if ($request->unit_id != $user->unit_id) {
            abort(403, 'Anda tidak diizinkan membuat layanan untuk unit lain.');
        }

        $data = $request->all();
        if ($request->hasFile('svc_icon')) {
            $path = $request->file('svc_icon')->store('icons', 'public');
            $data['svc_icon'] = $path;
        }

        $data['allow_guest'] = $request->has('allow_guest') ? 1 : 0;
        if ($data['category_id'] != 2) {
            $data['allow_guest'] = 0;
        }

        $service = Service::create($data);

        return redirect()->route('services.edit', $service->uuid)
            ->with('success', 'Layanan berhasil dibuat. Anda dapat melihat QR code di bawah.');
    }

    public function getQrCode(Service $service)
    {
        $user = Auth::user();
        if ($user->role_id != 2 || $service->unit_id != $user->unit_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Generate the URL using uuid
        $url = route('tickets.create.service', ['uuid' => $service->uuid]);
        $qrCode = QrCode::size(200)->generate($url);

        return response()->json([
            'qrCode' => $qrCode,
            'url' => $url,
            'downloadUrl' => route('services.qrcode.download', $service->uuid),
        ]);
    }

    public function edit(Service $service)
    {
        $user = Auth::user();
        if ($user->role_id != 2 || $service->unit_id != $user->unit_id) {
            abort(403, 'Anda tidak diizinkan mengakses layanan ini.');
        }

        // Generate and save UUID if null
        if (is_null($service->uuid)) {
            try {
                $service->uuid = Uuid::uuid4()->toString();
                $service->save();
            } catch (\Exception $e) {
                return redirect()->route('services.index')
                    ->with('error', 'Gagal menghasilkan UUID untuk layanan ini. Silakan coba lagi.');
            }
        }

        $units = Unit::all();
        $url = route('tickets.create.service', ['uuid' => $service->uuid]);
        $qrCode = QrCode::size(200)->generate($url);

        return view('services.edit', compact('service', 'units', 'qrCode', 'url'));
    }

    public function update(Request $request, Service $service)
    {
        $user = Auth::user();
        if ($user->role_id != 2 || $service->unit_id != $user->unit_id) {
            abort(403, 'Anda tidak diizinkan mengubah layanan ini.');
        }

        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'svc_name' => 'required|string|max:255',
            'svc_desc' => 'nullable|string',
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
            if ($service->svc_icon) {
                Storage::disk('public')->delete($service->svc_icon);
            }
            $path = $request->file('svc_icon')->store('icons', 'public');
            $data['svc_icon'] = $path;
        } else {
            $data['svc_icon'] = $service->svc_icon;
        }

        $data['allow_guest'] = $request->has('allow_guest') ? 1 : 0;
        if ($data['category_id'] != 2) {
            $data['allow_guest'] = 0;
        }

        $service->update($data);
        return redirect()->route('services.index')->with('success', 'Layanan berhasil diperbarui.');
    }

    public function updateStatus(Request $request, Service $service)
    {
        $user = Auth::user();
        if ($user->role_id != 2 || $service->unit_id != $user->unit_id) {
            abort(403, 'Anda tidak diizinkan mengubah status layanan ini.');
        }

        $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        $service->update(['status' => $request->status]);
        return redirect()->route('services.index')->with('success', 'Status layanan berhasil diperbarui.');
    }

    public function updateAllowGuest(Request $request, Service $service)
    {
        $user = Auth::user();
        if ($user->role_id != 2 || $service->unit_id != $user->unit_id) {
            abort(403, 'Anda tidak diizinkan mengubah pengaturan tamu layanan ini.');
        }

        $request->validate([
            'allow_guest' => 'required|boolean',
        ]);

        if ($service->category_id != 2 && $request->allow_guest == 1) {
            return redirect()->route('services.index')->with('error', 'Hanya layanan publik yang dapat mengizinkan tamu.');
        }

        $service->update(['allow_guest' => $request->allow_guest]);
        return redirect()->route('services.index')->with('success', 'Pengaturan tamu layanan berhasil diperbarui.');
    }

    public function updateCategory(Request $request, Service $service)
    {
        $user = Auth::user();
        if ($user->role_id != 2 || $service->unit_id != $user->unit_id) {
            abort(403, 'Anda tidak diizinkan mengubah kategori layanan ini.');
        }

        $request->validate([
            'category_id' => 'required|in:1,2',
        ]);

        $data = ['category_id' => $request->category_id];
        if ($request->category_id != 2) {
            $data['allow_guest'] = 0;
        }

        $service->update($data);
        return redirect()->route('services.index')->with('success', 'Kategori layanan berhasil diperbarui.');
    }
}