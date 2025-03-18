<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Pic;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function warga()
    {
        $user = Auth::user();
        $ticketStats = $this->getTicketStatsForWarga($user->id);

        $latestTicket = Ticket::where('user_id', $user->id)
            ->with(['service', 'unit'])
            ->orderBy('created_at', 'desc')
            ->first();
        $tickets = Ticket::where('user_id', $user->id)->get();

        return view('mobile.home', compact('user', 'ticketStats', 'latestTicket'));
    }

    public function pegawai()
{
    $user = Auth::user();
    $userId = $user->id;

    Log::info('Loading dashboard for user: ' . $userId . ', Role: ' . $user->role_id);

    $personalStats = $this->getPersonalStatsForPegawai($userId);
    $createdStats = $this->getCreatedStatsForPegawai($userId);

    return view('dashboard.pegawai', compact( 'personalStats', 'createdStats'));
}

private function getPersonalStatsForPegawai($userId)
{
    // Dapatkan semua ID pic yang terkait dengan user
    $picIds = Pic::where('user_id', $userId)->pluck('id')->toArray();

    // Gunakan Query Builder langsung
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

    return [
        'resolved' => $resolved,
    ];
}

private function getCreatedStatsForPegawai($userId)
{
    return [
        'created' => Ticket::where('user_id', $userId) // Tiket yang diajukan oleh pegawai
            ->count(),
    ];
}

    public function operator()
    {
        $user = Auth::user();
        $ticketStats = $this->getTicketStatsForOperator($user->unit_id);
        $personalStats = $this->getPersonalStatsForOperator($user->id);
        return view('dashboard.operator', compact('user', 'ticketStats', 'personalStats'));
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

//     private function getTicketStatsForPegawai($userId)
// {
//     return [
//         'completed' => Ticket::whereHas('pics', function ($query) use ($userId) {
//             $query->where('user_id', $userId)
//                   ->where('ticket_pic.pic_stats', 'inactive');
//         })->where('status', 2)->count(),
//         'pending' => Ticket::whereHas('pics', function ($query) use ($userId) {
//             $query->where('user_id', $userId)
//                   ->where('ticket_pic.pic_stats', 'active');
//         })->where('status', 0)->count(),
//         'assigned' => Ticket::whereHas('pics', function ($query) use ($userId) {
//             $query->where('user_id', $userId)
//                   ->where('ticket_pic.pic_stats', 'active');
//         })->where('status', 1)->count(),
//     ];
// }

//     private function getPersonalStatsForPegawai($userId)
// {
//     return [
//         'resolved' => Ticket::whereHas('pics', function ($query) use ($userId) {
//             $query->where('user_id', $userId)
//                   ->where('ticket_pic.pic_stats', 'inactive'); // Tentukan tabel ticket_pic secara eksplisit
//         })->where('status', 2)->count(),
//     ];
// }

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
    private function getHistoricalResolvedStatsForPegawai($userId)
{
    return [
        'historical_resolved' => Ticket::whereHas('picsHistory', function ($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->where('ticket_pic.pic_stats', 'inactive');
        })->where('status', 2)->count(),
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
}
