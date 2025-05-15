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
        $this->middleware('auth')->except(['createGuest', 'storeGuest']);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $search = $request->input('search');
        $statusFilter = $request->input('status_filter');

        $query = Ticket::with(['responses.user', 'responses.uploads', 'user', 'uploads', 'service', 'service.unit']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('ticket_code', 'like', '%' . $search . '%')
                    ->orWhere('title', 'like', '%' . $search . '%');
            });
        }

        if ($statusFilter !== null && $statusFilter !== '') {
            $query->where('status', $statusFilter);
        }

        if ($user->role_id == 4) {
            $query->where('user_id', $user->id);
        } elseif ($user->role_id == 3) {
            $isPicActive = $user->isAssignedAsPic();
            Log::info('User ID: ' . $user->id . ', Is PIC Active in index(): ' . ($isPicActive ? 'Yes' : 'No'));
            if ($isPicActive) {
                $query->whereHas('pics', function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->where('ticket_pic.pic_stats', 'active');
                })
                    ->orWhere('user_id', $user->id);
            } else {
                $query->where('user_id', $user->id);
            }
        } elseif ($user->role_id == 2) {
            $query->where('unit_id', $user->unit_id);
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(5);

        $pics = collect();
        if ($user->role_id == 2 && $user->unit_id) {
            $pics = User::where('role_id', 3)
                ->where('unit_id', $user->unit_id)
                ->with([
                    'pics' => function ($query) {
                        $query->where('pic_stats', 'active');
                    }
                ])
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
        }

        $servicesQuery = Service::where('status', 'active');
        if ($user->role_id == 4) {
            $servicesQuery->where('category_id', 2);
        }
        $services = $servicesQuery->with('unit')->get();

        $canCreateTicket = in_array($user->role_id, [3, 4]);
        Log::info('User ID: ' . $user->id . ', Role ID: ' . $user->role_id . ', Can Create Ticket: ' . ($canCreateTicket ? 'Yes' : 'No'));

        return view('theme::tickets.index', compact('tickets', 'canCreateTicket', 'pics', 'services'));
    }

public function ticketPerformance(Request $request)
{
    $unitId = $request->query('unit_id');
    $timeRange = $request->query('time_range', 'week');
    $customStart = $request->query('custom_start');
    $customEnd = $request->query('custom_end');

    $startDate = now();
    $endDate = now();
    $groupByFormat = '%Y-%m-%d %H:00:00';
    $timeInterval = 'hour';
    $dateFormat = 'Y-m-d H:i:s';

    if ($customStart && $customEnd) {
        $startDate = Carbon::parse($customStart);
        $endDate = Carbon::parse($customEnd);
        $diffInDays = $startDate->diffInDays($endDate);

        if ($diffInDays <= 1) {
            $timeRange = 'day';
        } elseif ($diffInDays <= 7) {
            $timeRange = 'week';
        } elseif ($diffInDays <= 31) {
            $timeRange = 'month';
        } elseif ($diffInDays <= 365) {
            $timeRange = 'year';
        } else {
            $timeRange = '10year';
        }
    }

    switch ($timeRange) {
        case 'day':
            $startDate = $customStart ? Carbon::parse($customStart) : now()->startOfDay();
            $endDate = $customEnd ? Carbon::parse($customEnd) : now()->endOfDay();
            $groupByFormat = '%Y-%m-%d %H:00:00';
            $timeInterval = 'hour';
            $dateFormat = 'Y-m-d H:i:s';
            break;
        case 'week':
            $startDate = $customStart ? Carbon::parse($customStart)->startOfWeek() : now()->startOfWeek();
            $endDate = $customEnd ? Carbon::parse($customEnd)->endOfWeek() : now()->endOfWeek();
            $groupByFormat = '%Y-%m-%d';
            $timeInterval = 'day';
            $dateFormat = 'Y-m-d';
            break;
        case 'month':
            $startDate = $customStart ? Carbon::parse($customStart)->startOfMonth() : now()->startOfMonth();
            $endDate = $customEnd ? Carbon::parse($customEnd)->endOfMonth() : now()->endOfMonth();
            $groupByFormat = '%Y-%m-%d';
            $timeInterval = 'week_in_month';
            $dateFormat = 'Y-m-d';
            break;
        case 'year':
            $startDate = $customStart ? Carbon::parse($customStart)->startOfYear() : now()->startOfYear();
            $endDate = $customEnd ? Carbon::parse($customEnd)->endOfYear() : now()->endOfYear();
            $groupByFormat = '%Y-%m';
            $timeInterval = 'month';
            $dateFormat = 'Y-m';
            break;
        case '10year':
            $startDate = $customStart ? Carbon::parse($customStart)->startOfYear() : now()->subYears(10)->startOfYear();
            $endDate = $customEnd ? Carbon::parse($customEnd)->endOfYear() : now()->endOfYear();
            $groupByFormat = '%Y';
            $timeInterval = 'year';
            $dateFormat = 'Y';
            break;
    }

    // Query data tiket
    $createdTickets = Ticket::select(
        DB::raw("DATE_FORMAT(created_at, '$groupByFormat') as period"),
        DB::raw('COUNT(*) as count')
    )
    ->where('created_at', '>=', $startDate)
    ->where('created_at', '<=', $endDate)
    ->whereNull('deleted_at');

    if ($unitId) {
        $createdTickets->where('unit_id', $unitId);
    } else {
        $user = auth()->user();
        if ($user->role_id == 2) {
            $createdTickets->where('unit_id', $user->unit_id);
        }
    }

    $createdTickets = $createdTickets->groupBy('period')->get();

    $completedTickets = Ticket::select(
        DB::raw("DATE_FORMAT(updated_at, '$groupByFormat') as period"),
        DB::raw('COUNT(*) as count')
    )
    ->where('status', 2)
    ->where('updated_at', '>=', $startDate)
    ->where('updated_at', '<=', $endDate)
    ->whereNull('deleted_at');

    if ($unitId) {
        $completedTickets->where('unit_id', $unitId);
    } else {
        $user = auth()->user();
        if ($user->role_id == 2) {
            $completedTickets->where('unit_id', $user->unit_id);
        }
    }

    $completedTickets = $completedTickets->groupBy('period')->get();

    $pendingTickets = Ticket::where('status', 1)
        ->where('created_at', '>=', $startDate)
        ->where('created_at', '<=', $endDate)
        ->whereNull('deleted_at');
    if ($unitId) {
        $pendingTickets->where('unit_id', $unitId);
    } else {
        $user = auth()->user();
        if ($user->role_id == 2) {
            $pendingTickets->where('unit_id', $user->unit_id);
        }
    }
    $pendingCount = $pendingTickets->count();

    $assignedTickets = Ticket::where('status', 3)
        ->where('created_at', '>=', $startDate)
        ->where('created_at', '<=', $endDate)
        ->whereNull('deleted_at');
    if ($unitId) {
        $assignedTickets->where('unit_id', $unitId);
    } else {
        $user = auth()->user();
        if ($user->role_id == 2) {
            $assignedTickets->where('unit_id', $user->unit_id);
        }
    }
    $assignedCount = $assignedTickets->count();

    // Generate periods
    $periods = [];
    $currentPeriod = clone $startDate;
    $weeklyDataCreated = [];
    $weeklyDataCompleted = [];
    $weekLabels = [];

    if ($timeInterval === 'hour') {
        while ($currentPeriod <= $endDate) {
            $periods[] = $currentPeriod->format($dateFormat);
            $currentPeriod->addHour();
        }
    } elseif ($timeInterval === 'day') {
        while ($currentPeriod <= $endDate) {
            $periods[] = $currentPeriod->format($dateFormat);
            $currentPeriod->addDay();
        }
    } elseif ($timeInterval === 'week_in_month') {
        $weekNumber = 1;
        $currentPeriod = $startDate->copy()->startOfMonth();
        $endOfMonth = $endDate->copy()->endOfMonth();

        while ($currentPeriod <= $endOfMonth) {
            $weekStart = $currentPeriod->copy();
            $weekEnd = $currentPeriod->copy()->addDays(6);

            if ($weekEnd > $endOfMonth) {
                $weekEnd = $endOfMonth->copy();
            }

            if ($weekStart > $endDate) {
                break;
            }

            $weekLabels[] = "Minggu Ke-$weekNumber";

            $weekCreated = 0;
            $weekCompleted = 0;

            foreach ($createdTickets as $ticket) {
                $ticketDate = Carbon::parse($ticket->period);
                if ($ticketDate->between($weekStart, $weekEnd)) {
                    $weekCreated += (int) $ticket->count;
                }
            }

            foreach ($completedTickets as $ticket) {
                $ticketDate = Carbon::parse($ticket->period);
                if ($ticketDate->between($weekStart, $weekEnd)) {
                    $weekCompleted += (int) $ticket->count;
                }
            }

            $weeklyDataCreated[] = $weekCreated;
            $weeklyDataCompleted[] = $weekCompleted;

            $weekNumber++;
            $currentPeriod->addDays(7);
        }
    } elseif ($timeInterval === 'month') {
        while ($currentPeriod <= $endDate) {
            $periods[] = $currentPeriod->format($dateFormat);
            $currentPeriod->addMonth();
        }
    } elseif ($timeInterval === 'year') {
        $periods = [];
        $currentYear = $startDate->year;
        $endYear = $endDate->year;
        while ($currentYear <= $endYear) {
            $periods[] = (string) $currentYear;
            $currentYear++;
        }
    } elseif ($timeInterval === 'week') {
        while ($currentPeriod <= $endDate) {
            $weekStart = $currentPeriod->copy()->startOfWeek();
            $periods[] = $weekStart->format($dateFormat);
            $currentPeriod->addWeek();
        }
    }

    if ($timeInterval === 'week_in_month') {
        $labels = $weekLabels;
        $created = $weeklyDataCreated;
        $completed = $weeklyDataCompleted;
    } else {
        $labels = array_unique($periods);
        $created = array_fill(0, count($labels), 0);
        $completed = array_fill(0, count($labels), 0);

        foreach ($createdTickets as $ticket) {
            $periodIndex = array_search($ticket->period, $labels);
            if ($periodIndex !== false) {
                $created[$periodIndex] = (int) $ticket->count;
            }
        }

        foreach ($completedTickets as $ticket) {
            $periodIndex = array_search($ticket->period, $labels);
            if ($periodIndex !== false) {
                $completed[$periodIndex] = (int) $ticket->count;
            }
        }

        $formattedLabels = array_map(function($period) use ($timeInterval) {
            if ($timeInterval === 'hour') {
                return date('H:i', strtotime($period));
            } elseif ($timeInterval === 'day') {
                return date('d M', strtotime($period));
            } elseif ($timeInterval === 'week') {
                $weekStart = date('d M', strtotime($period));
                $weekEnd = date('d M', strtotime($period . ' +6 days'));
                return "$weekStart - $weekEnd";
            } elseif ($timeInterval === 'month') {
                return date('M Y', strtotime($period . '-01'));
            } elseif ($timeInterval === 'year') {
                return $period; // Already in year format (e.g., "2015")
            }
            return $period;
        }, $labels);

        $labels = $formattedLabels;
    }

    return response()->json([
        'labels' => $labels,
        'created' => $created,
        'completed' => $completed,
        'pending' => $pendingCount,
        'assigned' => $assignedCount
    ]);
}

public function resolutionTimes(Request $request)
{
    $unitId = $request->query('unit_id');

    $resolutionTimes = DB::table('tickets')
        ->join('services', 'tickets.service_id', '=', 'services.id')
        ->select(
            'services.svc_name as service_name',
            DB::raw('FLOOR(AVG(TIMESTAMPDIFF(DAY, tickets.created_at, tickets.updated_at))) as avgResolutionDays')
        )
        ->whereNotNull('tickets.updated_at')
        ->where('tickets.status', 2) // Completed
        ->where('tickets.unit_id', $unitId)
        ->whereNull('tickets.deleted_at')
        ->groupBy('services.svc_name')
        ->get();


    return response()->json([
        'services' => $resolutionTimes->pluck('service_name'),
        'avgResolutionDays' => $resolutionTimes->pluck('avgResolutionDays')
    ]);
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

        return view('theme::tickets.assigned', compact('tickets'));
    }

    public function createGuest()
    {
        $units = Unit::all();
        $services = Service::where('status', 'active')
            ->where('category_id', 2)
            ->where('allow_guest', 1)
            ->with('unit')
            ->get();

        return view('theme::auth.landing', compact('units', 'services'));
    }

    // public function storeGuest(Request $request)
    // {
    //     $request->validate([
    //         'unit_id' => 'required|exists:units,id',
    //         'service_id' => 'required|exists:services,id',
    //         'title' => 'required',
    //         'description' => 'required',
    //         'images.*' => 'nullable|image|max:2048',
    //         'guest_name' => 'required|string|max:255',
    //         'guest_email' => 'required|email|max:255',
    //     ]);

    //     $service = Service::findOrFail($request->service_id);
    //     if ($service->category_id != 2 || $service->allow_guest != 1) {
    //         return redirect()->back()->with('error', 'Layanan ini tidak mengizinkan tamu untuk membuat laporan.');
    //     }

    //     $ticket = Ticket::create([
    //         'user_id' => null,
    //         'unit_id' => $request->unit_id,
    //         'service_id' => $request->service_id,
    //         'ticket_code' => 'TCK' . now()->format('Ymd') . rand(1000, 9999),
    //         'title' => $request->title,
    //         'description' => $request->description,
    //         'status' => 0,
    //         'guest_name' => $request->guest_name,
    //         'guest_email' => $request->guest_email,
    //     ]);

    //     if ($request->hasFile('images')) {
    //         foreach ($request->file('images') as $image) {
    //             $uuid = Str::uuid();
    //             $path = $image->storeAs('uploads/' . now()->format('Ymd'), $uuid . '.' . $image->extension(), 'public');
    //             TicketUpload::create([
    //                 'ticket_id' => $ticket->id,
    //                 'uuid' => $uuid,
    //                 'filename_ori' => $image->getClientOriginalName(),
    //                 'filename_path' => $path,
    //             ]);
    //         }
    //     }

    //     return redirect()->route('welcome')->with('success', 'Laporan berhasil dibuat. Anda akan menerima konfirmasi melalui email.');
    // }

    public function create()
    {
        $user = auth()->user();
        if (!in_array($user->role_id, [2, 3, 4])) {
            \Log::info('User ID: ' . $user->id . ', Role ID: ' . $user->role_id . ' attempted to access create ticket page but was denied.');
            abort(403, 'Anda tidak diizinkan membuat aduan.');
        }

        $units = Unit::all();
        $servicesQuery = Service::where('status', 'active');
        if ($user->role_id == 4) {
            $servicesQuery->where('category_id', 2);
        }
        $services = $servicesQuery->with('unit')->get();

        return view('theme::tickets.create', compact('units', 'services'));
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
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'images.*' => 'nullable|image|max:2048',
        ]);

        \Log::info('Creating ticket with unit_id: ' . $request->unit_id . ', service_id: ' . $request->service_id);

        $ticket = Ticket::create([
            'user_id' => $user->id,
            'unit_id' => $request->unit_id,
            'service_id' => $request->service_id,
            'ticket_code' => 'TCK' . now()->format('Ymd') . rand(1000, 9999),
            'title' => $request->title,
            'description' => $request->description,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'status' => 0,
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

    public function storeGuest(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'service_id' => 'required|exists:services,id',
            'title' => 'required',
            'description' => 'required',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'images.*' => 'nullable|image|max:2048',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
        ]);

        $service = Service::findOrFail($request->service_id);
        if ($service->category_id != 2 || $service->allow_guest != 1) {
            return redirect()->back()->with('error', 'Layanan ini tidak mengizinkan tamu untuk membuat laporan.');
        }

        $ticket = Ticket::create([
            'user_id' => null,
            'unit_id' => $request->unit_id,
            'service_id' => $request->service_id,
            'ticket_code' => 'TCK' . now()->format('Ymd') . rand(1000, 9999),
            'title' => $request->title,
            'description' => $request->description,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'status' => 0,
            'guest_name' => $request->guest_name,
            'guest_email' => $request->guest_email,
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

        return redirect()->route('welcome')->with('success', 'Laporan berhasil dibuat. Anda akan menerima konfirmasi melalui email.');
    }

    public function getServices($unitId)
    {
        $user = auth()->user();
        $servicesQuery = Service::where('unit_id', $unitId)
            ->where('status', 'active');

        if ($user && $user->role_id == 4) {
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
    public function ticketStats(Request $request)
{
    $unitId = $request->query('unit_id');
    $query = Ticket::query();

    if ($unitId) {
        $query->where('unit_id', $unitId);
    } else {
        // For operators, limit to their unit unless viewing all units
        $user = auth()->user();
        if ($user->role_id == 2 && !$unitId) {
            $query->where('unit_id', $user->unit_id);
        }
    }

    $stats = [
        'completed' => $query->clone()->where('status', 2)->count(),
        'pending' => $query->clone()->where('status', 0)->count(),
        'assigned' => $query->clone()->where('status', 1)->count(),
    ];

    return response()->json($stats);
}
    public function respond(Request $request, Ticket $ticket)
    {
        $user = auth()->user();
        \Log::info('User ID in respond(): ' . $user->id . ', Ticket ID: ' . $ticket->id);

        if ($user->role_id != 3) {
            \Log::warning('Unauthorized role: ' . $user->role_id);
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        if (!$user->isAssignedAsPic()) {
            \Log::warning('User not assigned as PIC: ' . $user->id);
            return response()->json(['success' => false, 'message' => 'Anda belum ditugaskan sebagai PIC.'], 403);
        }

        $isAssignedToTicket = $this->isPicAssignedToTicket($user->id, $ticket);
        \Log::info('Is user assigned to this ticket in respond(): ' . ($isAssignedToTicket ? 'Yes' : 'No'));

        if (!$isAssignedToTicket) {
            \Log::warning('User ' . $user->id . ' not assigned to ticket ' . $ticket->id);
            return response()->json(['success' => false, 'message' => 'Anda belum ditugaskan sebagai PIC untuk tiket ini.'], 403);
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

            $uploads = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $uuid = Str::uuid();
                    $directory = 'uploads/' . now()->format('Ymd');
                    $filename = $uuid . '.' . $image->extension();
                    $path = $image->storeAs($directory, $filename, 'public');
                    \Log::info('File stored at: ' . $path);
                    if ($path) {
                        $upload = TicketResponseUpload::create([
                            'ticket_response_id' => $response->id,
                            'uuid' => $uuid,
                            'filename_ori' => $image->getClientOriginalName(),
                            'filename_path' => $path,
                        ]);
                        $uploads[] = $upload;
                    } else {
                        \Log::error('Failed to store file: ' . $filename);
                    }
                }
            }

            \Log::info('Response created successfully for ticket: ' . $ticket->id);
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'role_id' => $user->role_id,
                ],
                'auth_user_id' => $user->id,
                'uploads' => $uploads,
            ]);
        } catch (\Exception $e) {
            \Log::error('Exception in respond: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengirim respons.'], 500);
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

        \Log::info('PIC ID ' . $pic->id . ' removed from ticket ID: ' . $ticket->id);

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

        return view('theme::tickets.created', compact('tickets'));
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
            }

            // Ubah status tiket menjadi averaging
            $ticket->update(['status' => 2]);
            \Log::info('Ticket resolved by PIC ID: ' . $user->id . ' for ticket: ' . $ticket->ticket_code);
        } else {
            // Jika status bukan 2, hanya perbarui status tanpa memengaruhi PIC lain
            $ticket->update(['status' => $request->status]);
            \Log::info('Ticket status updated to ' . $request->status . ' by PIC ID: ' . $user->id);
        }

        return redirect()->back()->with('success', 'Status tiket berhasil diperbarui.');
    }

    public function reply(Request $request, $ticketId)
    {
        $user = auth()->user();
        \Log::info('Entering reply method. User ID: ' . $user->id . ', Ticket ID: ' . $ticketId);
        \Log::info('Request data: ', $request->all());

        if ($user->role_id != 4) {
            \Log::warning('Unauthorized role: ' . $user->role_id);
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $ticket = \App\Models\Ticket::findOrFail($ticketId);
        if ($ticket->user_id != $user->id) {
            \Log::warning('Ticket ownership mismatch. User ID: ' . $user->id . ', Ticket User ID: ' . $ticket->id);
            return response()->json(['success' => false, 'message' => 'Anda tidak diizinkan membalas respons untuk tiket ini.'], 403);
        }

        if ($ticket->status == 2) {
            \Log::warning('Ticket resolved. Status: ' . $ticket->status);
            return response()->json(['success' => false, 'message' => 'Tiket ini sudah resolved, Anda tidak dapat membalas lagi.'], 403);
        }

        // Count pengadu's responses before adding a new one
        $pengaduResponses = $ticket->responses()
            ->where('user_id', $user->id)
            ->count();
        \Log::info('Pengadu responses count before sending: ' . $pengaduResponses);

        // Check if a pegawai has ever responded in the ticket's history
        $hasPegawaiResponse = $ticket->responses()
            ->where('user_id', '!=', $user->id)
            ->whereHas('user', function ($query) {
                $query->where('role_id', 3);
            })
            ->exists();

        // If a pegawai has responded, count pengadu messages since the last pegawai response
        $pengaduMessagesSinceLastPegawai = $pengaduResponses;
        if ($hasPegawaiResponse) {
            $lastPegawaiResponse = $ticket->responses()
                ->where('user_id', '!=', $user->id)
                ->whereHas('user', function ($query) {
                    $query->where('role_id', 3);
                })
                ->latest()
                ->first();

            $pengaduMessagesSinceLastPegawai = $ticket->responses()
                ->where('user_id', $user->id)
                ->where('created_at', '>', $lastPegawaiResponse->created_at)
                ->count();
            \Log::info('Pengadu messages since last pegawai response: ' . $pengaduMessagesSinceLastPegawai);
        }

        $canSend = true;
        $messageLimitReached = false;

        // If there are 10 or more messages from pengadu since the last pegawai response
        if ($pengaduMessagesSinceLastPegawai >= 10) {
            $canSend = false;
            $messageLimitReached = true;
            \Log::info('Pengadu reached 10 messages since last pegawai reply. Ticket ID: ' . $ticket->id);
        }

        if (!$canSend) {
            return response()->json([
                'success' => false,
                'message' => 'Anda telah mencapai batas 10 pesan. Tunggu balasan dari pegawai untuk mengirim lagi.',
                'message_count' => $pengaduMessagesSinceLastPegawai,
                'limit_reached' => $messageLimitReached,
            ], 403);
        }

        $request->validate([
            'message' => 'required|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // If there's a previous response to quote (optional)
        $quotedResponseId = $ticket->responses()->latest()->first()?->id;
        $newResponse = $ticket->responses()->create([
            'user_id' => $user->id,
            'ticket_id_quote' => $quotedResponseId,
            'message' => $request->message,
        ]);

        $uploads = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $uuid = Str::uuid();
                $directory = 'Uploads/' . now()->format('Ymd');
                $filename = $uuid . '.' . $image->extension();
                $path = $image->storeAs($directory, $filename, 'public');
                if ($path) {
                    $upload = TicketResponseUpload::create([
                        'ticket_response_id' => $newResponse->id,
                        'uuid' => $uuid,
                        'filename_ori' => $image->getClientOriginalName(),
                        'filename_path' => $path,
                    ]);
                    $uploads[] = $upload;
                } else {
                    \Log::error('Failed to store file: ' . $filename);
                }
            }
        }

        // Recount pengadu messages since last pegawai response
        $pengaduMessagesSinceLastPegawai = $ticket->responses()
            ->where('user_id', $user->id)
            ->when($hasPegawaiResponse, function ($query) use ($lastPegawaiResponse) {
                $query->where('created_at', '>', $lastPegawaiResponse->created_at);
            })
            ->count();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'role_id' => $user->role_id,
            ],
            'auth_user_id' => $user->id,
            'quoted_message' => $quotedResponseId ? $ticket->responses()->find($quotedResponseId)->message : null,
            'uploads' => $uploads,
            'message_count' => $pengaduMessagesSinceLastPegawai,
            'limit_reached' => $messageLimitReached,
        ]);
    }
    public function recentTickets(Request $request)
{
    $unitId = $request->query('unit_id');
    $query = Ticket::select('ticket_code', 'title', 'status', 'created_at', 'unit_id')
        ->with('service.unit') // Include unit relation through service
        ->latest()
        ->take(10);

    if ($unitId) {
        $query->where('unit_id', $unitId);
    } else {
        // For operators, limit to their unit unless viewing all units
        $user = auth()->user();
        if ($user->role_id == 2 && !$unitId) {
            $query->where('unit_id', $user->unit_id);
        }
    }

        $tickets = $query->get()->map(function ($ticket) {
        // Map status to string for frontend consistency
        $unit_name = Unit::select('unit_name')->where('id', $ticket->unit_id)->first();
        $statusMap = [
            0 => 'pending',
            1 => 'assigned',
            2 => 'completed'
        ];
        return [
            'code' => $ticket->ticket_code,
            'title' => $ticket->title,
            'status' => $statusMap[$ticket->status] ?? 'unknown',
            'created_at' => $ticket->created_at->toDateTimeString(), // Include creation date
            'unit_name' => $unit_name->unit_name ?? 'N/A', // Include unit name
        ];
    });

    return response()->json($tickets);
}

    public function stats(Request $request)
{
    $unitId = $request->query('unit_id');
    $query = Ticket::query();

    if ($unitId) {
        $query->where('unit_id', $unitId);
    } else {
        // For operators, limit to their unit unless viewing all units
        $user = auth()->user();
        if ($user->role_id == 2 && !$unitId) {
            $query->where('unit_id', $user->unit_id);
        }
    }

    // Remove time range filter to show all-time stats
    $stats = [
        'completed' => $query->clone()->where('status', 2)->count(),
        'pending' => $query->clone()->where('status', 0)->count(),
        'assigned' => $query->clone()->where('status', 1)->count(),
    ];

    return response()->json($stats);
}

    public function units()
    {
        $units = Unit::select('id', 'unit_name as name')->get();
        return response()->json($units);
    }

    public function ticketLocations(Request $request)
    {
        $unitId = $request->query('unit_id');
        $query = Ticket::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select('ticket_code', 'title', 'latitude', 'longitude');

        if ($unitId) {
            $query->where('unit_id', $unitId);
        } else {
            // For operators, limit to their unit unless viewing all units
            $user = auth()->user();
            if ($user->role_id == 2 && !$unitId) {
                $query->where('unit_id', $user->unit_id);
            }
        }

        $tickets = $query->get()->map(function ($ticket) {
            return [
                'lat' => (float) $ticket->latitude,
                'lng' => (float) $ticket->longitude,
                'title' => $ticket->ticket_code,
                'description' => $ticket->title
            ];
        });

        return response()->json($tickets);
    }
    public function serviceStats(Request $request)
{
    $unitId = $request->query('unit_id');
    $query = \App\Models\Service::query()
        ->select('id', 'svc_name')
        ->where('status', 'active');

    if ($unitId) {
        $query->where('unit_id', $unitId);
    } else {
        $user = auth()->user();
        if ($user->role_id == 2 && !$unitId) {
            $query->where('unit_id', $user->unit_id);
        }
    }

    $services = $query->get()->map(function ($service) use ($unitId) {
        $ticketQuery = \App\Models\Ticket::where('service_id', $service->id);
        if ($unitId) {
            $ticketQuery->where('unit_id', $unitId);
        }
        return [
            'id' => $service->id,
            'name' => $service->svc_name,
            'stats' => [
                'completed' => $ticketQuery->clone()->where('status', 2)->count(),
                'pending' => $ticketQuery->clone()->where('status', 0)->count(),
                'assigned' => $ticketQuery->clone()->where('status', 1)->count(),
            ]
        ];
    });

    return response()->json($services);
}
public function ticketCategories(Request $request)
{
    $unitId = $request->query('unit_id', 2); // Default to 2 if not provided

    $roleCounts = DB::table('tickets')
        ->join('users', 'tickets.user_id', '=', 'users.id') // Asumsi created_by merujuk ke users.id
        ->whereIn('users.role_id', [2, 3, 4]) // Hanya role 2, 3, dan 4
        ->where('tickets.unit_id', $unitId) // Filter berdasarkan unit_id
        ->whereNull('tickets.deleted_at') // Hanya tiket yang belum dihapus
        ->select('users.role_id', DB::raw('COUNT(*) as count'))
        ->groupBy('users.role_id')
        ->get();

    // Format data untuk chart (misalnya, label dan count)
    $data = $roleCounts->mapWithKeys(function ($item) {
        $roleNames = [2 => 'Operator', 3 => 'Staff', 4 => 'Pengguna']; // Definisikan nama role
        $roleName = $roleNames[$item->role_id] ?? 'Unknown';
        return [$roleName => $item->count];
    })->all();

    return response()->json([
        'labels' => array_keys($data),
        'counts' => array_values($data)
    ]);
}
public function serviceDistribution(Request $request)
{
    $unitId = $request->query('unit_id'); // Default to 2 if not provided

    $serviceDistribution = DB::table('tickets')
        ->join('services', 'tickets.service_id', '=', 'services.id')
        ->where('tickets.unit_id', $unitId)
        ->whereNull('tickets.deleted_at')
        ->select('services.svc_name', DB::raw('COUNT(*) as count'))
        ->groupBy('services.svc_name')
        ->orderBy('count', 'desc') // Sort by count descending
        ->get();

    return response()->json([
        'labels' => $serviceDistribution->pluck('svc_name')->all(),
        'counts' => $serviceDistribution->pluck('count')->all()
    ]);
}
}