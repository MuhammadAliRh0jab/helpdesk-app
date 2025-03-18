<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Service;
use App\Models\TicketUpload;
use App\Models\TicketResponse;
use App\Models\TicketResponseUpload;
use App\Models\Pic;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
{
    $user = auth()->user();

    // Inisialisasi query untuk tickets
    if ($user->role_id == 4) { // Warga Kota (role_id = 4)
        $tickets = Ticket::where('user_id', $user->id)
            ->with(['responses.user', 'responses.uploads', 'user', 'uploads', 'service', 'service.unit'])
            ->orderBy('created_at', 'desc')
            ->get();
    } elseif ($user->role_id == 2) { // Operator (role_id = 2)
        $tickets = Ticket::where('unit_id', $user->unit_id)
            ->with(['responses.user', 'responses.uploads', 'user', 'uploads', 'service', 'service.unit'])
            ->orderBy('created_at', 'desc')
            ->get();
    } elseif ($user->role_id == 3) { // Pegawai (PIC) (role_id = 3)
        $isPicActive = $user->isAssignedAsPic();

        Log::info('User ID: ' . $user->id . ', Is PIC Active in index(): ' . ($isPicActive ? 'Yes' : 'No'));

        // Jika PIC aktif, ambil tiket yang ditugaskan; jika tidak, ambil tiket yang dibuat
        if ($isPicActive) {
            $tickets = Ticket::whereHas('pics', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->where('ticket_pic.pic_stats', 'active');
            })
            ->orWhere('user_id', $user->id)
            ->with(['responses.user', 'responses.uploads', 'user', 'uploads', 'service', 'service.unit'])
            ->orderBy('created_at', 'desc')
            ->get();
        } else {
            $tickets = Ticket::where('user_id', $user->id)
                ->with(['responses.user', 'responses.uploads', 'user', 'uploads', 'service', 'service.unit'])
                ->orderBy('created_at', 'desc')
                ->get();
        }
    } else { // Super_admin (role_id = 1)
        $tickets = Ticket::with(['responses.user', 'responses.uploads', 'user', 'uploads', 'service', 'service.unit'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // Filter layanan berdasarkan peran dan status aktif
    $servicesQuery = Service::where('status', 'active');
    if ($user->role_id == 4) { // Warga hanya melihat layanan publik
        $servicesQuery->where('category_id', 2); // Publik
    }
    $services = $servicesQuery->with('unit')->get();

    // Tentukan apakah pengguna dapat membuat aduan
    $canCreateTicket = in_array($user->role_id, [3, 4]);
    Log::info('User ID: ' . $user->id . ', Role ID: ' . $user->role_id . ', Can Create Ticket: ' . ($canCreateTicket ? 'Yes' : 'No'));

    // Ambil daftar PIC untuk operator
    $pics = collect();
    if ($user->role_id == 2 && $user->unit_id) {
        $pics = User::where('role_id', 3)
            ->where('unit_id', $user->unit_id)
            ->with(['pics' => function ($query) {
                $query->where('pic_stats', 'active');
            }])
            ->get()
            ->map(function ($user) {
                return (object) [
                    'id' => $user->id,
                    'username' => $user->username,
                    'pic_desc' => $user->pics->first()->pic_desc ?? 'Pegawai tanpa deskripsi',
                    'is_active' => $user->pics->first() ? true : false,
                ];
            });

        Log::info('Operator unit_id: ' . $user->unit_id);
        Log::info('PICs found: ' . $pics->pluck('username')->implode(', ') . ' (Count: ' . $pics->count() . ')');
        if ($pics->isEmpty()) {
            Log::info('No PICs found for unit_id: ' . $user->unit_id);
        }
    }

    return view('mobile.ticket', compact('tickets', 'canCreateTicket', 'pics', 'services'));
}
    // Tambahkan method isAssignedAsPic ke model User jika belum ada
    public function isAssignedAsPic()
    {
        return Pic::where('user_id', $this->id)
            ->where('pic_stats', 'active')
            ->exists();
    }

    public function assigned()
    {
        $user = auth()->user();

        if ($user->role_id != 3) {
            abort(403, 'Unauthorized action.');
        }

        $isPicActive = $user->isAssignedAsPic();
        \Log::info('User ID: ' . $user->id . ', Is PIC Active in assigned(): ' . ($isPicActive ? 'Yes' : 'No'));

        if (!$isPicActive) {
            return redirect()->route('tickets.index')->with('error', 'Anda belum ditugaskan sebagai PIC.');
        }

        $tickets = Ticket::whereHas('pics', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->where('ticket_pic.pic_stats', 'active');
        })
        ->with(['responses.user', 'responses.uploads', 'user', 'uploads', 'service', 'service.unit'])
        ->whereNull('deleted_at')
        ->orderBy('created_at', 'desc')
        ->get();

        \Log::info('Tickets assigned to user: ' . $tickets->pluck('ticket_code')->implode(', '));

        return view('tickets.assigned', compact('tickets'));
    }

    public function create()
{
    // Pastikan hanya warga (role_id = 4) atau pegawai (role_id = 3) yang dapat membuat aduan
    $user = auth()->user();
    if (!in_array($user->role_id, [2, 3, 4])) {
        \Log::info('User ID: ' . $user->id . ', Role ID: ' . $user->role_id . ' attempted to access create ticket page but was denied.');
        abort(403, 'Anda tidak diizinkan membuat aduan.');
    }

    // Ambil unit untuk dropdown
    $units = Unit::all();

    // Ambil layanan yang aktif, filter berdasarkan peran
    $servicesQuery = Service::where('status', 'active');
    if ($user->role_id == 4) { // Warga hanya melihat layanan publik
        $servicesQuery->where('category_id', 2); // Publik
    }
    $services = $servicesQuery->with('unit')->get();

    return view('mobile.create_ticket', compact('units', 'services'));
    // return view('tickets.create', compact('units', 'services'));
}

    public function store(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->role_id, [2, 3, 4])) {
            abort(403, 'Anda tidak diizinkan membuat aduan.');
        }

        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'service_id' => 'required|exists:services,id',
            'title' => 'required',
            'description' => 'required',
            'images.*' => 'image|max:2048',
        ]);

        \Log::info('Creating ticket with unit_id: ' . $request->unit_id . ', service_id: ' . $request->service_id);

        $ticket = Ticket::create([
            'user_id' => $user->id,
            'unit_id' => $request->unit_id,
            'service_id' => $request->service_id,
            'ticket_code' => 'TCK' . now()->format('Ymd') . rand(1000, 9999),
            'title' => $request->title,
            'description' => $request->description,
            'status' => 0, // Pending
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $uuid = Str::uuid();
                $path = $image->storeAs('uploads/' . now()->format('Ymd'), $uuid . '.' . $image->extension(), 'public');
                TicketUpload::create([
                    'ticket_id' => $ticket->id,
                    'uuid' => $uuid,
                    'filename_ori' => $image->getClientOriginalName(),
                    'filename_path' => $path,
                ]);
            }
        }

        return redirect()->route('tickets.index')->with('success', 'Aduan berhasil dibuat.');
    }

    public function getServices($unitId)
{
    $user = auth()->user();
    $servicesQuery = Service::where('unit_id', $unitId)
        ->where('status', 'active');

    if ($user->role_id == 4) { // Warga hanya melihat layanan publik
        $servicesQuery->where('category_id', 2);
    }

    $services = $servicesQuery->get(['id', 'svc_name']);
    return response()->json($services);
}
public function assign(Request $request, Ticket $ticket)
{
    $user = auth()->user();
    \Log::info('User ID in assign(): ' . $user->id . ', Ticket ID: ' . $ticket->id);

    if ($user->role_id != 2) {
        abort(403, 'Unauthorized action.');
    }

    $request->validate([
        'pic_id' => 'required|exists:users,id',
    ]);

    $picUser = User::where('id', $request->pic_id)
        ->where('unit_id', $user->unit_id)
        ->where('role_id', 3)
        ->first();

    if (!$picUser) {
        \Log::warning('Selected PIC ID ' . $request->pic_id . ' does not match unit_id ' . $user->unit_id . ' or role_id 3');
        return redirect()->back()->with('error', 'PIC tidak valid atau tidak berada di unit yang sama.');
    }

    if ($ticket->unit_id != $user->unit_id) {
        \Log::warning('Ticket unit_id ' . $ticket->unit_id . ' does not match Operator unit_id ' . $user->unit_id);
        return redirect()->back()->with('error', 'Tiket tidak berada di unit Anda.');
    }

    // Cek apakah PIC ini sudah ditugaskan ke tiket ini
    $existingPic = Pic::where('user_id', $picUser->id)
        ->whereHas('tickets', function ($query) use ($ticket) {
            $query->where('ticket_id', $ticket->id)
                  ->where('pic_stats', 'active');
        })
        ->first();

    if ($existingPic) {
        \Log::info('PIC ID ' . $picUser->id . ' already assigned to ticket ' . $ticket->id);
        return redirect()->back()->with('error', 'PIC ini sudah ditugaskan ke tiket ini.');
    }

    // Buat atau perbarui entri PIC
    $pic = Pic::firstOrCreate(
        ['user_id' => $picUser->id],
        [
            'services_id' => $ticket->service_id,
            'pic_start' => now(),
            'pic_desc' => 'Pegawai ditugaskan untuk tiket ' . $ticket->ticket_code,
            'pic_stats' => 'active',
        ]
    );

    if ($pic->wasRecentlyCreated) {
        \Log::info('New PIC entry created for user ID: ' . $picUser->id);
    } else {
        $pic->update(['pic_stats' => 'active', 'pic_start' => now(), 'services_id' => $ticket->service_id]);
        \Log::info('Existing PIC entry updated for user ID: ' . $picUser->id);
    }

    // Tambahkan PIC ke tiket
    DB::table('ticket_pic')->insert([
        'ticket_id' => $ticket->id,
        'pic_id' => $pic->id,
        'pic_stats' => 'active',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Update status tiket menjadi "Ditugaskan" (status = 1) jika belum
    if ($ticket->status == 0) {
        $ticket->update(['status' => 1]);
        \Log::info('Ticket status updated to Ditugaskan for ticket ID: ' . $ticket->id);
    }

    \Log::info('New PIC ID: ' . $pic->id . ' assigned to ticket ID: ' . $ticket->id);

    // Tambahkan pesan otomatis ke riwayat percakapan
    TicketResponse::create([
        'ticket_id' => $ticket->id,
        'user_id' => $user->id,
        'message' => "PIC baru ({$picUser->username}) telah ditambahkan ke tiket ini.",
    ]);

    return redirect()->back()->with('success', 'PIC baru berhasil ditugaskan ke tiket.');
}
public function respond(Request $request, Ticket $ticket)
{
    $user = auth()->user();
    \Log::info('User ID in respond(): ' . $user->id . ', Ticket ID: ' . $ticket->id);

    if ($user->role_id != 3) {
        \Log::warning('Unauthorized role: ' . $user->role_id);
        abort(403, 'Unauthorized action.');
    }

    if (!$user->isAssignedAsPic()) {
        \Log::warning('User not assigned as PIC: ' . $user->id);
        abort(403, 'Anda belum ditugaskan sebagai PIC.');
    }

    $isAssignedToTicket = $this->isPicAssignedToTicket($user->id, $ticket);
    \Log::info('Is user assigned to this ticket in respond(): ' . ($isAssignedToTicket ? 'Yes' : 'No'));

    if (!$isAssignedToTicket) {
        \Log::warning('User ' . $user->id . ' not assigned to ticket ' . $ticket->id);
        abort(403, 'Anda belum ditugaskan sebagai PIC untuk tiket ini.');
    }

    try {
        $request->validate([
            'message' => 'required',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $response = $ticket->responses()->create([
            'user_id' => $user->id,
            'message' => $request->message,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $uuid = Str::uuid();
                $directory = 'uploads/' . now()->format('Ymd');
                $filename = $uuid . '.' . $image->extension();
                $path = $image->storeAs($directory, $filename, 'public');
                \Log::info('File stored at: ' . $path);
                if ($path) {
                    TicketResponseUpload::create([
                        'ticket_response_id' => $response->id,
                        'uuid' => $uuid,
                        'filename_ori' => $image->getClientOriginalName(),
                        'filename_path' => $path,
                    ]);
                } else {
                    \Log::error('Failed to store file: ' . $filename);
                }
            }
        }

        \Log::info('Response created successfully for ticket: ' . $ticket->id);
        return redirect()->back()->with('success', 'Respons berhasil ditambahkan.');
    } catch (\Exception $e) {
        \Log::error('Exception in respond: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Terjadi kesalahan saat mengirim respons.');
    }
}
    public function removePic(Request $request, Ticket $ticket)
{
    $user = auth()->user();
    \Log::info('User ID in removePic(): ' . $user->id . ', Ticket ID: ' . $ticket->id);

    if ($user->role_id != 2) {
        abort(403, 'Unauthorized action.');
    }

    $request->validate([
        'pic_id' => 'required|exists:pics,id',
    ]);

    $pic = Pic::findOrFail($request->pic_id);

    // Pastikan PIC ini terkait dengan tiket
    $ticketPic = DB::table('ticket_pic')
        ->where('ticket_id', $ticket->id)
        ->where('pic_id', $pic->id)
        ->where('pic_stats', 'active')
        ->first();

    if (!$ticketPic) {
        \Log::warning('PIC ID ' . $pic->id . ' not assigned to ticket ' . $ticket->id);
        return redirect()->back()->with('error', 'PIC ini tidak ditugaskan ke tiket ini.');
    }

    // Nonaktifkan PIC dari tiket
    DB::table('ticket_pic')
        ->where('ticket_id', $ticket->id)
        ->where('pic_id', $pic->id)
        ->update(['pic_stats' => 'inactive', 'updated_at' => now()]);

    \Log::info('PIC ID ' . $pic->id . ' removed from ticket ID ' . $ticket->id);

    // Tambahkan pesan otomatis ke riwayat percakapan
    $picUser = User::find($pic->user_id);
    TicketResponse::create([
        'ticket_id' => $ticket->id,
        'user_id' => $user->id,
        'message' => "PIC ({$picUser->username}) telah dinonaktifkan dari tiket ini.",
    ]);

    // Jika tidak ada PIC aktif lagi, ubah status tiket kembali ke Pending
    $activePics = DB::table('ticket_pic')
        ->where('ticket_id', $ticket->id)
        ->where('pic_stats', 'active')
        ->count();

    if ($activePics == 0) {
        $ticket->update(['status' => 0]);
        \Log::info('No active PICs left, ticket status changed to Pending for ticket ID: ' . $ticket->id);
    }

    return redirect()->back()->with('success', 'PIC berhasil dinonaktifkan dari tiket.');
}

    private function isPicAssignedToTicket($userId, $ticket)
    {
        if (!$ticket instanceof \App\Models\Ticket) {
            Log::error('Invalid ticket instance in isPicAssignedToTicket: ' . (is_object($ticket) ? get_class($ticket) : gettype($ticket)));
            return false;
        }

        $exists = DB::table('ticket_pic')
            ->join('pics', 'ticket_pic.pic_id', '=', 'pics.id')
            ->where('ticket_pic.ticket_id', $ticket->id)
            ->where('pics.user_id', $userId)
            ->where('ticket_pic.pic_stats', 'active')
            ->exists();

        \Log::info('isPicAssignedToTicket for user ' . $userId . ' and ticket ' . $ticket->id . ': ' . ($exists ? 'Yes' : 'No'));
        return $exists;
    }

    public function transfer(Request $request, Ticket $ticket)
{
    // Pastikan hanya operator (role_id = 2) yang dapat mengalihkan aduan
    if (auth()->user()->role_id != 2) {
        abort(403, 'Anda tidak diizinkan mengalihkan aduan.');
    }

    // Pastikan aduan masih dalam status Pending (status = 0)
    if ($ticket->status != 0) {
        return redirect()->route('tickets.index')->with('error', 'Aduan hanya dapat dialihkan jika masih dalam status Pending.');
    }

    // Validasi input
    $request->validate([
        'unit_id' => 'required|exists:units,id',
        'service_id' => 'required|exists:services,id',
    ]);

    $originalUnit = \App\Models\Unit::find($ticket->unit_id);
    $newUnit = \App\Models\Unit::find($request->unit_id);

    // Simpan unit asal sebelum pengalihan (jika belum ada)
    if (!$ticket->original_unit_id) {
        $ticket->original_unit_id = $ticket->unit_id;
    }

    // Update unit_id dan service_id ke unit dan layanan baru
    $ticket->unit_id = $request->unit_id;
    $ticket->service_id = $request->service_id;
    $ticket->save();

    // Tambahkan pesan otomatis ke riwayat percakapan
    \App\Models\TicketResponse::create([
        'ticket_id' => $ticket->id,
        'user_id' => auth()->user()->id,
        'message' => "Aduan telah dialihkan dari unit {$originalUnit->unit_name} ke unit {$newUnit->unit_name}.",
    ]);

    return redirect()->route('tickets.index')->with('success', 'Aduan berhasil dialihkan ke unit lain.');
}
public function created()
{
    // Pastikan hanya operator (role_id = 2) yang dapat mengakses
    $user = auth()->user();
    if ($user->role_id != 2) {
        abort(403, 'Anda tidak diizinkan mengakses halaman ini.');
    }

    // Ambil tiket yang dibuat oleh operator
    $tickets = Ticket::where('user_id', $user->id)
        ->with(['responses.user', 'responses.uploads', 'user', 'uploads', 'service', 'service.unit'])
        ->orderBy('created_at', 'desc')
        ->get();

    return view('tickets.created', compact('tickets'));
}
public function update(Request $request, Ticket $ticket)
{
    $user = auth()->user();
    \Log::info('User ID in update(): ' . $user->id . ', Ticket ID: ' . $ticket->id);

    if ($user->role_id != 3) {
        abort(403, 'Unauthorized action.');
    }

    if (!$user->isAssignedAsPic()) {
        abort(403, 'Anda belum ditugaskan sebagai PIC.');
    }

    $isAssignedToTicket = $this->isPicAssignedToTicket($user->id, $ticket);
    \Log::info('Is user assigned to this ticket in update(): ' . ($isAssignedToTicket ? 'Yes' : 'No'));

    if (!$isAssignedToTicket) {
        abort(403, 'Anda belum ditugaskan sebagai PIC untuk tiket ini.');
    }

    $request->validate([
        'status' => 'required|in:0,1,2',
    ]);

    // Jika PIC mengubah status menjadi 2 (Resolved), tiket langsung selesai
    if ($request->status == 2) {
        // Nonaktifkan semua PIC yang terkait dengan tiket ini
        $assignments = DB::table('ticket_pic')
            ->where('ticket_id', $ticket->id)
            ->where('pic_stats', 'active')
            ->get();

        foreach ($assignments as $assignment) {
            DB::table('ticket_pic')
                ->where('ticket_id', $ticket->id)
                ->where('pic_id', $assignment->pic_id)
                ->update(['pic_stats' => 'inactive', 'updated_at' => now()]);

            \Log::info('Removed ticket_pic relation for ticket: ' . $ticket->ticket_code . ' and PIC: ' . $assignment->pic_id);

            // Cek apakah PIC ini masih memiliki tiket aktif lainnya
            // $pic = Pic::find($assignment->pic_id);
            // if ($pic) {
            //     $otherTickets = $pic->tickets()
            //         ->where('tickets.id', '!=', $ticket->id)
            //         ->where('tickets.status', '!=', 2)
            //         ->exists();

            //     if (!$otherTickets) {
            //         $pic->delete();
            //         \Log::info('PIC entry deleted for PIC ID: ' . $pic->id . ' after ticket resolved: ' . $ticket->ticket_code);
            //     } else {
            //         \Log::info('PIC ID ' . $pic->id . ' still has unresolved tickets. Not deleting PIC entry.');
            //     }
            // }
        }

        // Ubah status tiket menjadi Resolved
        $ticket->update(['status' => 2]);
        \Log::info('Ticket resolved by PIC ID: ' . $user->id . ' for ticket: ' . $ticket->ticket_code);
    } else {
        // Jika status bukan 2, hanya perbarui status tanpa memengaruhi PIC lain
        $ticket->update(['status' => $request->status]);
        \Log::info('Ticket status updated to ' . $request->status . ' by PIC ID: ' . $user->id);
    }

    return redirect()->back()->with('success', 'Status tiket berhasil diperbarui.');
}

    public function reply(Request $request, TicketResponse $response)
    {
        $user = auth()->user();
        \Log::info('Entering reply method. User ID: ' . $user->id . ', TicketResponse ID: ' . $response->id);
        \Log::info('Request data: ', $request->all());

        if ($user->role_id != 4) {
            \Log::warning('Unauthorized role: ' . $user->role_id);
            abort(403, 'Unauthorized action.');
        }

        $ticket = $response->ticket;
        if ($ticket->user_id != $user->id) {
            \Log::warning('Ticket ownership mismatch. User ID: ' . $user->id . ', Ticket User ID: ' . $ticket->user_id);
            abort(403, 'Anda tidak diizinkan membalas respons untuk tiket ini.');
        }

        if ($ticket->status == 2) {
            \Log::warning('Ticket resolved. Status: ' . $ticket->status);
            abort(403, 'Tiket ini sudah resolved, Anda tidak dapat membalas lagi.');
        }

        $latestResponse = $ticket->responses()->latest()->first();
        \Log::info('Latest response ID: ' . ($latestResponse ? $latestResponse->id : 'null'));

        if (!$latestResponse) {
            \Log::warning('No latest response found.');
            abort(403, 'Tidak ada respons yang dapat dibalas.');
        }

        if ($latestResponse->user_id == $user->id) {
            \Log::warning('User trying to reply to own message. User ID: ' . $user->id);
            abort(403, 'Anda tidak dapat membalas pesan Anda sendiri.');
        }

        if ($response->id != $latestResponse->id) {
            \Log::warning('Response ID mismatch. Selected: ' . $response->id . ', Latest: ' . $latestResponse->id);
            abort(403, 'Anda hanya dapat membalas pesan terakhir.');
        }

        $request->validate([
            'message' => 'required|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $newResponse = $ticket->responses()->create([
            'user_id' => $user->id,
            'ticket_id_quote' => $response->id,
            'message' => $request->message,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $uuid = Str::uuid();
                $directory = 'uploads/' . now()->format('Ymd');
                $filename = $uuid . '.' . $image->extension();
                $path = $image->storeAs($directory, $filename, 'public');
                if ($path) {
                    TicketResponseUpload::create([
                        'ticket_response_id' => $newResponse->id,
                        'uuid' => $uuid,
                        'filename_ori' => $image->getClientOriginalName(),
                        'filename_path' => $path,
                    ]);
                } else {
                    \Log::error('Failed to store file: ' . $filename);
                }
            }
        }

        return redirect()->back()->with('success', 'Balasan berhasil dikirim.');
    }
}
