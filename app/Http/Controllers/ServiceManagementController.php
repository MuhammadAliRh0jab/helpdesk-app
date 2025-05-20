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

        // Generate UUID if null
        if (is_null($service->uuid)) {
            try {
                $service->uuid = Uuid::uuid4()->toString();
                $service->save();
            } catch (\Exception $e) {
                return redirect()->route('services.index')
                    ->with('error', 'Gagal menghasilkan UUID untuk layanan ini. Silakan coba lagi.');
            }
        }

        // Ensure UUID is not null before generating URL
        if (is_null($service->uuid)) {
            return redirect()->route('services.index')
                ->with('error', 'UUID tidak tersedia untuk layanan ini.');
        }

        // Generate the URL for the reporting form using uuid
        $url = route('tickets.create.service', ['uuid' => $service->uuid]);
        $qrCode = QrCode::size(200)->generate($url);

        return view('services.qrcode', compact('service', 'qrCode', 'url'));
    }

    public function downloadQrCode(Service $service)
    {
        $user = Auth::user();
        if ($user->role_id != 2 || $service->unit_id != $user->unit_id) {
            abort(403, 'Anda tidak diizinkan mengakses layanan ini.');
        }

        // Generate UUID if null
        if (is_null($service->uuid)) {
            try {
                $service->uuid = Uuid::uuid4()->toString();
                $service->save();
            } catch (\Exception $e) {
                return redirect()->route('services.index')
                    ->with('error', 'Gagal menghasilkan UUID untuk layanan ini. Silakan coba lagi.');
            }
        }

        // Ensure UUID is not null before generating URL
        if (is_null($service->uuid)) {
            return redirect()->route('services.index')
                ->with('error', 'UUID tidak tersedia untuk layanan ini.');
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

        return redirect()->route('services.edit', $service->id)
            ->with('success', 'Layanan berhasil dibuat. Anda dapat melihat QR code di bawah.');
    }

    public function getQrCode(Service $service)
    {
        $user = Auth::user();
        if ($user->role_id != 2 || $service->unit_id != $user->unit_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Generate UUID if null
        if (is_null($service->uuid)) {
            try {
                $service->uuid = Uuid::uuid4()->toString();
                $service->save();
            } catch (\Exception $e) {
                return response()->json(['error' => 'Gagal menghasilkan UUID untuk layanan ini.'], 500);
            }
        }

        // Ensure UUID is not null before generating URL
        if (is_null($service->uuid)) {
            return response()->json(['error' => 'UUID tidak tersedia untuk layanan ini.'], 500);
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

        // Generate UUID if null
        if (is_null($service->uuid)) {
            try {
                $service->uuid = Uuid::uuid4()->toString();
                $service->save();
            } catch (\Exception $e) {
                return redirect()->route('services.index')
                    ->with('error', 'Gagal menghasilkan UUID untuk layanan ini. Silakan coba lagi.');
            }
        }

        // Ensure UUID is not null before generating URL
        if (is_null($service->uuid)) {
            return redirect()->route('services.index')
                ->with('error', 'UUID tidak tersedia untuk layanan ini.');
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

        // Define validation rules for all possible fields
        $rules = [
            'unit_id' => 'sometimes|required|exists:units,id',
            'svc_name' => 'sometimes|required|string|max:255',
            'svc_desc' => 'sometimes|nullable|string',
            'svc_icon' => 'sometimes|nullable|image|max:2048',
            'category_id' => 'sometimes|required|in:1,2',
            'status' => 'sometimes|required|in:active,inactive',
            'allow_guest' => 'sometimes|present|boolean',
        ];

        // Validate only the fields that are present in the request
        $request->validate($rules);

        // Prepare data for update
        $data = [];

        // Handle fields if present in the request
        if ($request->has('unit_id')) {
            if ($request->unit_id != $user->unit_id) {
                abort(403, 'Anda tidak diizinkan mengubah layanan untuk unit lain.');
            }
            $data['unit_id'] = $request->unit_id;
        }

        if ($request->has('svc_name')) {
            $data['svc_name'] = $request->svc_name;
        }

        if ($request->has('svc_desc')) {
            $data['svc_desc'] = $request->svc_desc;
        }

        if ($request->hasFile('svc_icon')) {
            if ($service->svc_icon) {
                Storage::disk('public')->delete($service->svc_icon);
            }
            $path = $request->file('svc_icon')->store('icons', 'public');
            $data['svc_icon'] = $path;
        } elseif ($request->has('svc_icon') && is_null($request->svc_icon)) {
            // Handle case where svc_icon is explicitly cleared
            if ($service->svc_icon) {
                Storage::disk('public')->delete($service->svc_icon);
            }
            $data['svc_icon'] = null;
        }

        if ($request->has('category_id')) {
            $data['category_id'] = $request->category_id;
            // If category_id is not 2 (public), force allow_guest to 0
            if ($data['category_id'] != 2) {
                $data['allow_guest'] = 0;
            }
        }

        if ($request->has('status')) {
            $data['status'] = $request->status;
        }

        if ($request->has('allow_guest')) {
            $allowGuest = $request->input('allow_guest', 0);
            // Only allow allow_guest = 1 for public services (category_id == 2)
            if ($service->category_id != 2 && $allowGuest == 1) {
                return redirect()->route('services.index')->with('error', 'Hanya layanan publik yang dapat mengizinkan tamu.');
            }
            $data['allow_guest'] = $allowGuest;
        }

        // Update the service only if there are changes
        if (!empty($data)) {
            $service->update($data);
        }

        return redirect()->route('services.index')->with('success', 'Layanan berhasil diperbarui.');
    }
}