<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use App\Models\Pic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    

    private function getPersonalStatsForPegawai($userId)
    {
        $picIds = Pic::where('user_id', $userId)
            ->where('pic_stats', 'active')
            ->pluck('id')
            ->toArray();

        $resolved = DB::table('tickets')
            ->join('ticket_pic', 'tickets.id', '=', 'ticket_pic.ticket_id')
            ->whereIn('ticket_pic.pic_id', $picIds)
            ->where('ticket_pic.pic_stats', 'inactive')
            ->where('tickets.status', 2)
            ->whereNull('tickets.deleted_at')
            ->distinct()
            ->count('tickets.id');

        Log::info('Resolved Tickets Query Result for User ' . $userId . ': ' . $resolved);
        Log::info('Associated PIC IDs for User ' . $userId . ': ' . json_encode($picIds));

        return ['resolved' => $resolved];
    }

    private function getCreatedStatsForPegawai($userId)
    {
        $created = Ticket::where('user_id', $userId)
            ->whereNull('deleted_at')
            ->count();

        return ['created' => $created];
    }

    private function getAssignedStatsForPegawai($userId)
    {
        $picIds = Pic::where('user_id', $userId)
            ->where('pic_stats', 'active')
            ->pluck('id')
            ->toArray();

        $assigned = DB::table('tickets')
            ->join('ticket_pic', 'tickets.id', '=', 'ticket_pic.ticket_id')
            ->whereIn('ticket_pic.pic_id', $picIds)
            ->where('ticket_pic.pic_stats', 'active')
            ->where('tickets.status', 1)
            ->whereNull('tickets.deleted_at')
            ->distinct()
            ->count('tickets.id');

        return ['assigned' => $assigned];
    }

    // Method lain untuk role lain (warga, operator, admin)
    public function warga()
    {
        $user = Auth::user();
        $ticketStats = $this->getTicketStatsForWarga($user->id);

        $latestTicket = Ticket::where('user_id', $user->id)
            ->with(['service', 'unit'])
            ->orderBy('created_at', 'desc')
            ->first();
        $tickets = Ticket::where('user_id', $user->id)
            ->with(['service', 'unit'])
            ->orderBy('created_at', 'desc')
            ->paginate(5);
        return view('theme::dashboard.warga', compact('user', 'ticketStats', 'latestTicket', 'tickets'));
    }

    public function operator()
    {
        $user = Auth::user();
        $ticketStats = $this->getTicketStatsForOperator($user->unit_id);
        $personalStats = $this->getPersonalStatsForOperator($user->id);
        return view('dashboard.operator', compact('user', 'ticketStats', 'personalStats'));
    }

    public function operatorDashboard(Request $request)
    {
        $user = auth()->user();
        if ($user->role_id != 2) {
            abort(403, 'Unauthorized action.');
        }

        $query = Ticket::where('unit_id', $user->unit_id);
        $ticketStats = [
            'completed' => $query->clone()->where('status', 2)->count(),
            'pending' => $query->clone()->where('status', 0)->count(),
            'assigned' => $query->clone()->where('status', 1)->count(),
        ];

        $ticketStats['total'] = $ticketStats['completed'] + $ticketStats['pending'] + $ticketStats['assigned'];

        return view('theme::dashboard.operator', compact('ticketStats'));
    }

    public function admin()
    {
        $user = Auth::user();
        $ticketStats = $this->getTicketStatsForAdmin();
        return view('dashboard.admin', compact('user', 'ticketStats'));
    }

    private function getTicketStatsForWarga($userId)
    {
        return [
            'completed' => Ticket::where('user_id', $userId)->where('status', 2)->count(),
            'pending' => Ticket::where('user_id', $userId)->where('status', 0)->count(),
            'assigned' => Ticket::where('user_id', $userId)->where('status', 1)->count(),
        ];
    }

    private function getTicketStatsForOperator($unitId)
    {
        return [
            'completed' => Ticket::where('unit_id', $unitId)->where('status', 2)->count(),
            'pending' => Ticket::where('unit_id', $unitId)->where('status', 0)->count(),
            'assigned' => Ticket::where('unit_id', $unitId)->where('status', 1)->count(),
        ];
    }

    private function getPersonalStatsForOperator($userId)
    {
        return [
            'created' => Ticket::where('user_id', $userId)->count(),
        ];
    }

    private function getTicketStatsForAdmin()
    {
        return [
            'completed' => Ticket::where('status', 2)->count(),
            'pending' => Ticket::where('status', 0)->count(),
            'assigned' => Ticket::where('status', 1)->count(),
        ];
    }

    public function pegawai()
{
    $user = Auth::user();
    if ($user->role_id != 3) {
        abort(403, 'Unauthorized action.');
    }

    $userId = $user->id;

    Log::info('Loading dashboard for user: ' . $userId . ', Role: ' . $user->role_id);

    // Mengambil data statistik tiket menggunakan API dari TicketController
    $ticketStatsResponse = app(TicketController::class)->pegawaiTicketStats();
    $ticketStats = json_decode($ticketStatsResponse->content(), true);

    // Mengambil data waktu penyelesaian menggunakan API
    $resolutionTimesResponse = app(TicketController::class)->pegawaiResolutionTimes();
    $resolutionTimes = json_decode($resolutionTimesResponse->content(), true);
    $averageResolutionTime = !empty($resolutionTimes['avgResolutionDays']) 
        ? array_sum($resolutionTimes['avgResolutionDays']) / count($resolutionTimes['avgResolutionDays']) 
        : 0;

    // Mengambil data tiket terbaru menggunakan API
    $recentTicketsResponse = app(TicketController::class)->pegawaiRecentTickets();
    $recentTickets = json_decode($recentTicketsResponse->content(), true);

    // Menambahkan assigned stats (dari metode yang sudah ada)
    $assignedStats = $this->getAssignedStatsForPegawai($userId);
    $ticketStats = array_merge($ticketStats, $assignedStats);

    return view('dashboard.pegawai', compact('ticketStats', 'averageResolutionTime', 'recentTickets'));
}

    private function getResolvedTicketsForPegawai($userId)
    {
        // Ambil semua PIC ID yang terkait dengan user
        $picIds = Pic::where('user_id', $userId)->pluck('id')->toArray();

        if (empty($picIds)) {
            return collect(); // Kembalikan koleksi kosong jika tidak ada PIC
        }

        $tickets = DB::table('tickets')
            ->join('ticket_pic', 'tickets.id', '=', 'ticket_pic.ticket_id')
            ->whereIn('ticket_pic.pic_id', $picIds)
            ->where('ticket_pic.pic_stats', 'inactive') // Filter tiket selesai
            ->where('tickets.status', 2) // Status 2 menandakan selesai
            ->whereNull('tickets.deleted_at')
            ->select('tickets.ticket_code', 'tickets.title', 'tickets.updated_at')
            ->orderBy('tickets.updated_at', 'desc')
            ->get();

        Log::info('Resolved Tickets for User ' . $userId . ': ' . $tickets->count());
        return $tickets;
    }

    private function getCreatedTicketsForPegawai($userId)
    {
        $tickets = Ticket::where('user_id', $userId)
            ->whereNull('deleted_at')
            ->select('ticket_code', 'title', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        Log::info('Created Tickets for User ' . $userId . ': ' . $tickets->count());
        return $tickets;
    }

    private function getAverageResolutionTimeForPegawai($userId)
    {
        $picIds = Pic::where('user_id', $userId)->pluck('id')->toArray();

        if (empty($picIds)) {
            return 0;
        }

        $averageTime = DB::table('tickets')
            ->join('ticket_pic', 'tickets.id', '=', 'ticket_pic.ticket_id')
            ->whereIn('ticket_pic.pic_id', $picIds)
            ->where('ticket_pic.pic_stats', 'inactive')
            ->where('tickets.status', 2)
            ->whereNull('tickets.deleted_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(DAY, tickets.created_at, tickets.updated_at)) as avg_days'))
            ->value('avg_days');

        $averageTime = $averageTime ? round($averageTime, 2) : 0;
        Log::info('Average Resolution Time for User ' . $userId . ': ' . $averageTime . ' days');
        return $averageTime;
    }
}