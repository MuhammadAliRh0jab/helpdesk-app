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

            \Log::info('User ID: ' . $user->id . ', Is PIC Active in index(): ' . ($isPicActive ? 'Yes' : 'No'));

            if ($isPicActive) {
                return redirect()->route('tickets.assigned');
            }

            $tickets = Ticket::where('user_id', $user->id)
                ->with(['responses.user', 'responses.uploads', 'user', 'uploads', 'service', 'service.unit'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else { // Super_admin (role_id = 1)
            $tickets = Ticket::with(['responses.user', 'responses.uploads', 'user', 'uploads', 'service', 'service.unit'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $canCreateTicket = $user->role_id == 4 || ($user->role_id == 3 && !$user->isAssignedAsPic());

        $pics = [];
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

            \Log::info('Operator unit_id: ' . $user->unit_id);
            \Log::info('PICs found: ' . $pics->pluck('username')->implode(', ') . ' (Count: ' . $pics->count() . ')');
            if ($pics->isEmpty()) {
                \Log::info('No PICs found for unit_id: ' . $user->unit_id);
            }
        }

        return view('tickets.index', compact('tickets', 'canCreateTicket', 'pics'));
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
        $user = auth()->user();
        if ($user->role_id != 4) {
            abort(403, 'Anda tidak diizinkan membuat aduan.');
        }

        $units = Unit::all();
        $services = Service::all(); // Awalnya kosong, akan diisi dinamis via AJAX

        return view('tickets.create', compact('services', 'units'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if ($user->role_id != 4) {
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
        $services = Service::where('unit_id', $unitId)->get(['id', 'svc_name']);
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

        $pic = Pic::firstOrCreate(
            ['user_id' => $picUser->id],
            [
                'services_id' => $ticket->service_id, // Gunakan service_id dari tiket
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

        DB::table('ticket_pic')->updateOrInsert(
            ['ticket_id' => $ticket->id, 'pic_id' => $pic->id],
            ['pic_stats' => 'active', 'created_at' => now(), 'updated_at' => now()]
        );

        $ticket->update(['status' => 1]);

        \Log::info('Ticket assigned to PIC ID: ' . $pic->id . ', New Status: ' . $ticket->status);

        return redirect()->back()->with('success', 'Tiket berhasil ditugaskan ke PIC.');
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

        if ($request->status == 2) {
            $pic = Pic::where('user_id', $user->id)
                      ->where('pic_stats', 'active')
                      ->first();
            
            if ($pic) {
                DB::table('ticket_pic')
                    ->where('ticket_id', $ticket->id)
                    ->where('pic_id', $pic->id)
                    ->delete();

                \Log::info('Removed ticket_pic relation for ticket: ' . $ticket->ticket_code . ' and PIC: ' . $pic->id);

                $otherTickets = $pic->tickets()
                    ->where('tickets.id', '!=', $ticket->id)
                    ->where('tickets.status', '!=', 2)
                    ->exists();

                if ($otherTickets) {
                    \Log::info('PIC ' . $pic->id . ' still has unresolved tickets. Not deleting PIC entry for user: ' . $user->username);
                } else {
                    $pic->delete();
                    \Log::info('PIC entry deleted for user: ' . $user->username . ' after ticket resolved: ' . $ticket->ticket_code);
                }
            }
        }

        $ticket->update(['status' => $request->status]);

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