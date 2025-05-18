<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class WargaDashboardController extends Controller
{
    public function getTicketStats(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            $timeRange = $request->query('time_range', 'week');
            $customStart = $request->query('start_date');
            $customEnd = $request->query('end_date');

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

            $createdPendingQuery = Ticket::where('user_id', $user->id)
                ->where('status', 0)
                ->where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate)
                ->selectRaw("DATE_FORMAT(created_at, '$groupByFormat') as period, COUNT(*) as count")
                ->groupBy('period')
                ->orderBy('period');

            $createdAssignedQuery = Ticket::where('user_id', $user->id)
                ->where('status', 1)
                ->where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate)
                ->selectRaw("DATE_FORMAT(created_at, '$groupByFormat') as period, COUNT(*) as count")
                ->groupBy('period')
                ->orderBy('period');

            $createdCompletedQuery = Ticket::where('user_id', $user->id)
                ->where('status', 2)
                ->where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate)
                ->selectRaw("DATE_FORMAT(created_at, '$groupByFormat') as period, COUNT(*) as count")
                ->groupBy('period')
                ->orderBy('period');

            $createdPending = $createdPendingQuery->get();
            $createdAssigned = $createdAssignedQuery->get();
            $createdCompleted = $createdCompletedQuery->get();

            $totalCreated = Ticket::where('user_id', $user->id)
                ->where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate)
                ->count();

            $periods = [];
            $currentPeriod = clone $startDate;
            $weeklyDataPending = [];
            $weeklyDataAssigned = [];
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

                    $weekPending = 0;
                    $weekAssigned = 0;
                    $weekCompleted = 0;

                    foreach ($createdPending as $ticket) {
                        $ticketDate = Carbon::parse($ticket->period);
                        if ($ticketDate->between($weekStart, $weekEnd)) {
                            $weekPending += (int) $ticket->count;
                        }
                    }

                    foreach ($createdAssigned as $ticket) {
                        $ticketDate = Carbon::parse($ticket->period);
                        if ($ticketDate->between($weekStart, $weekEnd)) {
                            $weekAssigned += (int) $ticket->count;
                        }
                    }

                    foreach ($createdCompleted as $ticket) {
                        $ticketDate = Carbon::parse($ticket->period);
                        if ($ticketDate->between($weekStart, $weekEnd)) {
                            $weekCompleted += (int) $ticket->count;
                        }
                    }

                    $weeklyDataPending[] = $weekPending;
                    $weeklyDataAssigned[] = $weekAssigned;
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
            }

            if ($timeInterval === 'week_in_month') {
                $labels = $weekLabels;
                $pendingData = $weeklyDataPending;
                $assignedData = $weeklyDataAssigned;
                $completedData = $weeklyDataCompleted;
            } else {
                $labels = array_unique($periods);
                $pendingData = array_fill(0, count($labels), 0);
                $assignedData = array_fill(0, count($labels), 0);
                $completedData = array_fill(0, count($labels), 0);

                foreach ($createdPending as $ticket) {
                    $periodIndex = array_search($ticket->period, $labels);
                    if ($periodIndex !== false) {
                        $pendingData[$periodIndex] = (int) $ticket->count;
                    }
                }

                foreach ($createdAssigned as $ticket) {
                    $periodIndex = array_search($ticket->period, $labels);
                    if ($periodIndex !== false) {
                        $assignedData[$periodIndex] = (int) $ticket->count;
                    }
                }

                foreach ($createdCompleted as $ticket) {
                    $periodIndex = array_search($ticket->period, $labels);
                    if ($periodIndex !== false) {
                        $completedData[$periodIndex] = (int) $ticket->count;
                    }
                }

                $formattedLabels = array_map(function($period) use ($timeInterval) {
                    if ($timeInterval === 'hour') {
                        return date('H:i', strtotime($period));
                    } elseif ($timeInterval === 'day') {
                        return date('d M', strtotime($period));
                    } elseif ($timeInterval === 'month') {
                        return date('M Y', strtotime($period . '-01'));
                    } elseif ($timeInterval === 'year') {
                        return $period;
                    }
                    return $period;
                }, $labels);

                $labels = $formattedLabels;
            }

            return response()->json([
                'created' => [
                    'pending' => [
                        'labels' => $labels,
                        'data' => $pendingData
                    ],
                    'assigned' => [
                        'labels' => $labels,
                        'data' => $assignedData
                    ],
                    'completed' => [
                        'labels' => $labels,
                        'data' => $completedData
                    ]
                ],
                'totalStats' => [
                    'created' => $totalCreated
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching ticket stats: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Failed to fetch ticket stats'], 500);
        }
    }

    public function getTickets(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->query('per_page', 10);

        $ticketsQuery = DB::table('tickets')
            ->select(
                'tickets.id',
                'tickets.ticket_code',
                'tickets.title',
                'tickets.description',
                'tickets.status',
                'tickets.created_at',
                'services.svc_name'
            )
            ->leftJoin('services', 'tickets.service_id', '=', 'services.id')
            ->where('tickets.user_id', $user->id)
            ->orderBy('tickets.created_at', 'desc');

        $tickets = $ticketsQuery->paginate($perPage);

        return response()->json([
            'data' => $tickets->items(),
            'current_page' => $tickets->currentPage(),
            'last_page' => $tickets->lastPage(),
            'per_page' => $tickets->perPage(),
            'total' => $tickets->total(),
        ]);
    }

    public function getTicketDetail($id)
    {
        $user = Auth::user();

        $ticket = DB::table('tickets')
            ->select(
                'tickets.id',
                'tickets.ticket_code',
                'tickets.title',
                'tickets.description',
                'tickets.status',
                'tickets.created_at',
                'tickets.latitude',
                'tickets.longitude',
                'tickets.rating',
                'units.unit_name',
                'services.svc_name'
            )
            ->leftJoin('units', 'tickets.unit_id', '=', 'units.id')
            ->leftJoin('services', 'tickets.service_id', '=', 'services.id')
            ->where('tickets.id', $id)
            ->where('tickets.user_id', $user->id)
            ->first();

        if (!$ticket) {
            Log::warning('Ticket not found for user ID: ' . $user->id . ', requested ID: ' . $id);
            return response()->json(['message' => 'Ticket not found'], 404);
        }

        return response()->json($ticket);
    }

    public function index()
    {
        return view('theme::dashboard.warga');
    }
}