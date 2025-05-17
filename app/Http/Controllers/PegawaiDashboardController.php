<?php

namespace App\Http\Controllers;

use App\Models\Pic;
use App\Models\Ticket;
use App\Models\TicketPic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PegawaiDashboardController extends Controller
{
    public function showDashboard(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return redirect()->route('login')->withErrors(['error' => 'Unauthenticated']);
            }

            $pic = Pic::where('user_id', $user->id)->first();

            // Calculate ticket stats for statistics cards
            $ticketStats = [
                // Total tickets completed (assigned to the user as a handler)
                'total_completed' => $pic ? TicketPic::where('pic_id', $pic->id)
                    ->whereHas('ticket', function ($query) {
                        $query->where('status', 2);
                    })
                    ->count() : 0,

                // Tickets assigned to the user (as handler)
                'assigned_to_handle' => $pic ? TicketPic::where('pic_id', $pic->id)->count() : 0,

                // Tickets created by the user, broken down by status
                'created_pending' => Ticket::where('user_id', $user->id)
                    ->where('status', 0)
                    ->count(),
                'created_assigned' => Ticket::where('user_id', $user->id)
                    ->where('status', 1)
                    ->count(),
                'created_completed' => Ticket::where('user_id', $user->id)
                    ->where('status', 2)
                    ->count(),
            ];

            // Calculate average resolution time
            $averageResolutionTime = 0;
            if ($pic) {
                $tickets = TicketPic::where('pic_id', $pic->id)
                    ->join('tickets', 'ticket_pic.ticket_id', '=', 'tickets.id')
                    ->where('tickets.status', 2)
                    ->select('tickets.created_at', 'tickets.updated_at')
                    ->get();

                if (!$tickets->isEmpty()) {
                    $resolutionTimes = $tickets->map(function ($ticket) {
                        $created = new \DateTime($ticket->created_at);
                        $updated = new \DateTime($ticket->updated_at);
                        $interval = $created->diff($updated);
                        return $interval->days + ($interval->h / 24);
                    });
                    $averageResolutionTime = $resolutionTimes->avg();
                }
            }

            return view('dashboard', compact('ticketStats', 'averageResolutionTime'));
        } catch (\Exception $e) {
            Log::error('Error rendering dashboard: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withErrors(['error' => 'Failed to load dashboard']);
        }
    }

    // Other methods remain unchanged for this update
    public function getTicketDistribution(Request $request)
    {
        try {
            $userId = auth()->user()->id;
            Log::info('Fetching ticket distribution for user ID: ' . $userId);

            $pic = Pic::where('user_id', $userId)->first();
            Log::info('Found PIC: ' . ($pic ? json_encode(['id' => $pic->id, 'user_id' => $pic->user_id]) : 'null'));

            if (!$pic) {
                Log::warning('No PIC record found for user ID: ' . $userId);
                return response()->json([
                    'labels' => ['Belum Selesai', 'Selesai'],
                    'data' => [0, 0],
                    'percentages' => ['pending' => 0, 'completed' => 0],
                ]);
            }

            $query = DB::table('tickets')
                ->join('ticket_pic', 'tickets.id', '=', 'ticket_pic.ticket_id')
                ->where('ticket_pic.pic_id', $pic->id)
                ->select('tickets.status');
            Log::debug('SQL Query: ' . $query->toSql(), ['bindings' => $query->getBindings()]);
            $tickets = $query->get();
            Log::info('Raw ticket data retrieved: ' . json_encode($tickets));

            $pending = $tickets->whereIn('status', [0, 1])->count();
            $completed = $tickets->where('status', 2)->count();
            $total = $pending + $completed;
            $percentages = [
                'pending' => $total > 0 ? ($pending / $total) * 100 : 0,
                'completed' => $total > 0 ? ($completed / $total) * 100 : 0,
            ];

            Log::info('Processed distribution: pending=' . $pending . ', completed=' . $completed . ', total=' . $total);
            return response()->json([
                'labels' => ['Belum Selesai', 'Selesai'],
                'data' => [$pending, $completed],
                'percentages' => $percentages,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching ticket distribution: ' . $e->getMessage(), ['exception' => $e, 'user_id' => $userId]);
            return response()->json(['error' => 'Failed to fetch ticket distribution'], 500);
        }
    }

    public function getAssignmentCompletion(Request $request)
    {
        try {
            $userId = auth()->user()->id;
            Log::info('Fetching assignment completion for user ID: ' . $userId);

            $pic = Pic::where('user_id', $userId)->first();
            if (!$pic) {
                Log::warning('No PIC record found for user ID: ' . $userId);
                return response()->json([
                    'labels' => [],
                    'assignedData' => [],
                    'completedData' => [],
                ]);
            }

            // Time range parameters
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

            // Query data
            $query = DB::table('ticket_pic')
                ->join('tickets', 'ticket_pic.ticket_id', '=', 'tickets.id')
                ->where('ticket_pic.pic_id', $pic->id)
                ->where('ticket_pic.created_at', '>=', $startDate)
                ->where('ticket_pic.created_at', '<=', $endDate)
                ->select(
                    DB::raw("DATE_FORMAT(ticket_pic.created_at, '$groupByFormat') as period"),
                    DB::raw('COUNT(*) as assigned'),
                    DB::raw('COUNT(CASE WHEN tickets.status = 2 THEN 1 END) as completed')
                )
                ->groupBy('period')
                ->orderBy('period');

            $data = $query->get();

            // Generate periods
            $periods = [];
            $currentPeriod = clone $startDate;
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

                    $weekAssigned = 0;
                    $weekCompleted = 0;

                    foreach ($data as $entry) {
                        $entryDate = Carbon::parse($entry->period);
                        if ($entryDate->between($weekStart, $weekEnd)) {
                            $weekAssigned += (int) $entry->assigned;
                            $weekCompleted += (int) $entry->completed;
                        }
                    }

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
                $assignedData = $weeklyDataAssigned;
                $completedData = $weeklyDataCompleted;
            } else {
                $labels = array_unique($periods);
                $assignedData = array_fill(0, count($labels), 0);
                $completedData = array_fill(0, count($labels), 0);

                foreach ($data as $entry) {
                    $periodIndex = array_search($entry->period, $labels);
                    if ($periodIndex !== false) {
                        $assignedData[$periodIndex] = (int) $entry->assigned;
                        $completedData[$periodIndex] = (int) $entry->completed;
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

            Log::info('Processed data: labels=' . json_encode($labels) . ', assignedData=' . json_encode($assignedData) . ', completedData=' . json_encode($completedData));
            return response()->json([
                'labels' => $labels,
                'assignedData' => $assignedData,
                'completedData' => $completedData,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching assignment completion: ' . $e->getMessage(), ['exception' => $e, 'user_id' => $userId]);
            return response()->json(['error' => 'Failed to fetch assignment completion'], 500);
        }
    }

    public function getTicketList(Request $request)
    {
        try {
            $userId = auth()->user()->id;
            Log::info('Fetching ticket list for user ID: ' . $userId);

            $pic = Pic::where('user_id', $userId)->first();
            Log::info('Found PIC: ' . ($pic ? json_encode(['id' => $pic->id, 'user_id' => $pic->user_id]) : 'null'));

            if (!$pic) {
                Log::warning('No PIC record found for user ID: ' . $userId);
                return response()->json([]);
            }

            $query = DB::table('tickets')
                ->join('ticket_pic', 'tickets.id', '=', 'ticket_pic.ticket_id')
                ->where('ticket_pic.pic_id', $pic->id)
                ->select('tickets.ticket_code as code', 'tickets.title', 'tickets.status', 'tickets.created_at', 'tickets.updated_at')
                ->orderBy('tickets.created_at', 'desc');
            Log::debug('SQL Query: ' . $query->toSql(), ['bindings' => $query->getBindings()]);
            $tickets = $query->get();
            Log::info('Raw ticket list data: ' . json_encode($tickets));

            return response()->json($tickets);
        } catch (\Exception $e) {
            Log::error('Error fetching ticket list: ' . $e->getMessage(), ['exception' => $e, 'user_id' => $userId]);
            return response()->json(['error' => 'Failed to fetch ticket list'], 500);
        }
    }

    public function getRecentTickets(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            $pic = Pic::where('user_id', $user->id)->first();

            // Fetch created tickets with unit information
            $createdTickets = Ticket::where('user_id', $user->id)
                ->select('tickets.id', 'tickets.ticket_code as code', 'tickets.title', 'tickets.status', 'tickets.created_at', 'units.unit_name')
                ->join('services', 'tickets.service_id', '=', 'services.id')
                ->join('units', 'services.unit_id', '=', 'units.id')
                ->orderBy('tickets.created_at', 'desc')
                ->get()
                ->map(function ($ticket) {
                    return [
                        'id' => $ticket->id,
                        'code' => $ticket->code,
                        'title' => $ticket->title,
                        'status' => $ticket->status,
                        'created_at' => $ticket->created_at,
                        'unit_name' => $ticket->unit_name ?? 'N/A',
                        'type' => 'created'
                    ];
                });

            // Fetch assigned tickets with unit information
            $assignedTickets = collect();
            if ($pic) {
                $assignedTickets = TicketPic::where('pic_id', $pic->id)
                    ->join('tickets', 'ticket_pic.ticket_id', '=', 'tickets.id')
                    ->select('tickets.id', 'tickets.ticket_code as code', 'tickets.title', 'tickets.status', 'tickets.created_at', 'units.unit_name')
                    ->join('services', 'tickets.service_id', '=', 'services.id')
                    ->join('units', 'services.unit_id', '=', 'units.id')
                    ->orderBy('tickets.updated_at', 'desc')
                    ->get()
                    ->map(function ($ticket) {
                        return [
                            'id' => $ticket->id,
                            'code' => $ticket->code,
                            'title' => $ticket->title,
                            'status' => $ticket->status,
                            'created_at' => $ticket->created_at,
                            'unit_name' => $ticket->unit_name ?? 'N/A',
                            'type' => 'assigned'
                        ];
                    });
            } else {
                Log::warning('No PIC record found for user ID: ' . $user->id);
            }

            // Merge and sort tickets by created_at (most recent first)
            $tickets = $createdTickets->merge($assignedTickets)
                ->sortByDesc('created_at')
                ->take(5)
                ->values();

            return response()->json($tickets);
        } catch (\Exception $e) {
            Log::error('Error fetching recent tickets: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Failed to fetch recent tickets'], 500);
        }
    }

    public function getTicketStats(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            $pic = Pic::where('user_id', $user->id)->first();

            // Time range parameters
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

            // Created tickets by status (only Pending and Completed)
            $createdPendingQuery = Ticket::where('user_id', $user->id)
                ->where('status', 0)
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
            $createdCompleted = $createdCompletedQuery->get();

            // Assigned tickets by status (if PIC exists)
            $assignedPending = collect();
            $assignedAssigned = collect();
            $assignedCompleted = collect();
            $totalAssigned = 0;
            $totalResolved = 0;

            if ($pic) {
                $assignedPending = TicketPic::where('pic_id', $pic->id)
                    ->join('tickets', 'ticket_pic.ticket_id', '=', 'tickets.id')
                    ->where('tickets.status', 0)
                    ->where('tickets.updated_at', '>=', $startDate)
                    ->where('tickets.updated_at', '<=', $endDate)
                    ->selectRaw("DATE_FORMAT(tickets.updated_at, '$groupByFormat') as period, COUNT(*) as count")
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get();

                $assignedAssigned = TicketPic::where('pic_id', $pic->id)
                    ->join('tickets', 'ticket_pic.ticket_id', '=', 'tickets.id')
                    ->where('tickets.status', 1)
                    ->where('tickets.updated_at', '>=', $startDate)
                    ->where('tickets.updated_at', '<=', $endDate)
                    ->selectRaw("DATE_FORMAT(tickets.updated_at, '$groupByFormat') as period, COUNT(*) as count")
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get();

                $assignedCompleted = TicketPic::where('pic_id', $pic->id)
                    ->join('tickets', 'ticket_pic.ticket_id', '=', 'tickets.id')
                    ->where('tickets.status', 2)
                    ->where('tickets.updated_at', '>=', $startDate)
                    ->where('tickets.updated_at', '<=', $endDate)
                    ->selectRaw("DATE_FORMAT(tickets.updated_at, '$groupByFormat') as period, COUNT(*) as count")
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get();

                // Total stats for cards (within the time range)
                $assignedTickets = TicketPic::where('pic_id', $pic->id)
                    ->join('tickets', 'ticket_pic.ticket_id', '=', 'tickets.id')
                    ->where('tickets.updated_at', '>=', $startDate)
                    ->where('tickets.updated_at', '<=', $endDate)
                    ->select('tickets.id', 'tickets.status')
                    ->get();
                $totalAssigned = $assignedTickets->count();
                $totalResolved = $assignedTickets->where('status', 2)->count();
            }

            // Total created tickets within the time range
            $totalCreated = Ticket::where('user_id', $user->id)
                ->where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate)
                ->count();

            // Generate periods
            $periods = [];
            $currentPeriod = clone $startDate;
            $weeklyDataPending = [];
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
                    $weekCompleted = 0;

                    foreach ($createdPending as $ticket) {
                        $ticketDate = Carbon::parse($ticket->period);
                        if ($ticketDate->between($weekStart, $weekEnd)) {
                            $weekPending += (int) $ticket->count;
                        }
                    }

                    foreach ($createdCompleted as $ticket) {
                        $ticketDate = Carbon::parse($ticket->period);
                        if ($ticketDate->between($weekStart, $weekEnd)) {
                            $weekCompleted += (int) $ticket->count;
                        }
                    }

                    $weeklyDataPending[] = $weekPending;
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
                $completedData = $weeklyDataCompleted;
            } else {
                $labels = array_unique($periods);
                $pendingData = array_fill(0, count($labels), 0);
                $completedData = array_fill(0, count($labels), 0);

                foreach ($createdPending as $ticket) {
                    $periodIndex = array_search($ticket->period, $labels);
                    if ($periodIndex !== false) {
                        $pendingData[$periodIndex] = (int) $ticket->count;
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
                    'completed' => [
                        'labels' => $labels,
                        'data' => $completedData
                    ]
                ],
                'assigned' => [
                    'pending' => [
                        'labels' => $labels,
                        'data' => $assignedPending->pluck('count')->toArray()
                    ],
                    'assigned' => [
                        'labels' => $labels,
                        'data' => $assignedAssigned->pluck('count')->toArray()
                    ],
                    'completed' => [
                        'labels' => $labels,
                        'data' => $assignedCompleted->pluck('count')->toArray()
                    ]
                ],
                'totalStats' => [
                    'created' => $totalCreated,
                    'assigned' => $totalAssigned,
                    'resolved' => $totalResolved
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching ticket stats: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Failed to fetch ticket stats'], 500);
        }
    }

    public function getCompletedTicketsHistory(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            $pic = Pic::where('user_id', $user->id)->first();
            if (!$pic) {
                Log::warning('No PIC record found for user ID: ' . $user->id);
                return response()->json([
                    'labels' => [],
                    'data' => []
                ]);
            }

            $completedTickets = TicketPic::where('pic_id', $pic->id)
                ->join('tickets', 'ticket_pic.ticket_id', '=', 'tickets.id')
                ->where('tickets.status', 2)
                ->selectRaw('DATE(tickets.updated_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return response()->json([
                'labels' => $completedTickets->pluck('date'),
                'data' => $completedTickets->pluck('count')
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching completed tickets history: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Failed to fetch completed tickets history'], 500);
        }
    }

    public function getResolutionTimeByService(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            $pic = Pic::where('user_id', $user->id)->first();
            if (!$pic) {
                Log::warning('No PIC record found for user ID: ' . $user->id);
                return response()->json([
                    'labels' => [],
                    'data' => []
                ]);
            }

            $query = TicketPic::where('pic_id', $pic->id)
                ->join('tickets', 'ticket_pic.ticket_id', '=', 'tickets.id')
                ->join('services', 'tickets.service_id', '=', 'services.id')
                ->where('tickets.status', 2)
                ->select('services.svc_name as service_name', 'tickets.created_at', 'tickets.updated_at');
            Log::debug('SQL Query: ' . $query->toSql(), ['bindings' => $query->getBindings()]);

            $tickets = $query->get();
            Log::info('Raw resolution time data: ' . json_encode($tickets));

            if ($tickets->isEmpty()) {
                return response()->json([
                    'labels' => [],
                    'data' => []
                ]);
            }

            $resolutionTimes = $tickets->groupBy('service_name')->map(function ($group) {
                $times = $group->map(function ($ticket) {
                    $created = new \DateTime($ticket->created_at);
                    $updated = new \DateTime($ticket->updated_at);
                    $interval = $created->diff($updated);
                    return $interval->days + ($interval->h / 24);
                });
                return $times->avg();
            });

            Log::info('Processed resolution times: ' . json_encode($resolutionTimes));
            return response()->json([
                'labels' => $resolutionTimes->keys()->toArray(),
                'data' => $resolutionTimes->values()->map(fn($value) => round($value, 2))->toArray()
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching resolution time by service: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Failed to fetch resolution time by service'], 500);
        }
    }

    public function getTicketDistributionCreated(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            $tickets = Ticket::where('user_id', $user->id)
                ->select('status')
                ->get();

            $total = $tickets->count();
            $pending = $tickets->where('status', 0)->count();
            $assigned = $tickets->where('status', 1)->count();
            $completed = $tickets->where('status', 2)->count();

            $percentages = [
                'pending' => $total ? ($pending / $total) * 100 : 0,
                'assigned' => $total ? ($assigned / $total) * 100 : 0,
                'completed' => $total ? ($completed / $total) * 100 : 0,
            ];

            return response()->json([
                'labels' => ['Pending', 'Ditugaskan', 'Selesai'],
                'data' => [$pending, $assigned, $completed],
                'percentages' => $percentages,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching ticket distribution (created): ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Failed to fetch ticket distribution'], 500);
        }
    }

    public function getTicketDistributionAssigned(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            $pic = Pic::where('user_id', $user->id)->first();
            if (!$pic) {
                Log::warning('No PIC record found for user ID: ' . $user->id);
                return response()->json([
                    'labels' => ['Ditugaskan', 'Selesai'],
                    'data' => [0, 0],
                    'percentages' => ['assigned' => 0, 'completed' => 0]
                ]);
            }

            $tickets = TicketPic::where('pic_id', $pic->id)
                ->join('tickets', 'ticket_pic.ticket_id', '=', 'tickets.id')
                ->select('tickets.status')
                ->get();

            $total = $tickets->count();
            $assigned = $tickets->where('status', 1)->count();
            $completed = $tickets->where('status', 2)->count();

            $percentages = [
                'assigned' => $total ? ($assigned / $total) * 100 : 0,
                'completed' => $total ? ($completed / $total) * 100 : 0,
            ];

            return response()->json([
                'labels' => ['Ditugaskan', 'Selesai'],
                'data' => [$assigned, $completed],
                'percentages' => $percentages,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching ticket distribution (assigned): ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Failed to fetch ticket distribution'], 500);
        }
    }

    public function getResolutionTimes(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            $pic = Pic::where('user_id', $user->id)->first();
            if (!$pic) {
                Log::warning('No PIC record found for user ID: ' . $user->id);
                return response()->json(['avgResolutionDays' => 0], 200);
            }

            $tickets = TicketPic::where('pic_id', $pic->id)
                ->join('tickets', 'ticket_pic.ticket_id', '=', 'tickets.id')
                ->where('tickets.status', 2)
                ->select('tickets.created_at', 'tickets.updated_at')
                ->get();

            if ($tickets->isEmpty()) {
                return response()->json(['avgResolutionDays' => 0], 200);
            }

            $resolutionTimes = $tickets->map(function ($ticket) {
                $created = new \DateTime($ticket->created_at);
                $updated = new \DateTime($ticket->updated_at);
                $interval = $created->diff($updated);
                return $interval->days + ($interval->h / 24);
            });

            $avgResolutionDays = $resolutionTimes->avg();

            return response()->json([
                'avgResolutionDays' => $avgResolutionDays
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching resolution times: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Failed to fetch resolution times'], 500);
        }
    }
    public function getStats()
{
    $user = Auth::user();

    $stats = [
        'resolved_as_handler' => Ticket::whereHas('picsHistory', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('status', 2)->count(),

        'to_be_completed_as_handler' => Ticket::whereHas('pics', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->whereIn('status', [0, 1])->count(),

        'pending_as_creator' => Ticket::where('user_id', $user->id)->where('status', 0)->count(),

        'assigned_as_creator' => Ticket::where('user_id', $user->id)->where('status', 1)->count(),

        'completed_as_creator' => Ticket::where('user_id', $user->id)->where('status', 2)->count(),
    ];

    return response()->json($stats);
}

}