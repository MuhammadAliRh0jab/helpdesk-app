<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Service;
use App\Models\Unit;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SuperadminDashboardController extends Controller
{
    public function ticketStats(Request $request)
    {
        $query = Ticket::query();
        $unitId = $request->query('unit_id');

        if ($unitId) {
            $query->where('unit_id', $unitId);
        }

        $stats = [
            'completed' => $query->clone()->where('status', 2)->count(),
            'pending' => $query->clone()->where('status', 0)->count(),
            'assigned' => $query->clone()->where('status', 1)->count(),
        ];

        return response()->json($stats);
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
        }

        $completedTickets = $completedTickets->groupBy('period')->get();

        $pendingTickets = Ticket::where('status', 0)
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->whereNull('deleted_at');
        if ($unitId) {
            $pendingTickets->where('unit_id', $unitId);
        }
        $pendingCount = $pendingTickets->count();

        $assignedTickets = Ticket::where('status', 1)
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->whereNull('deleted_at');
        if ($unitId) {
            $assignedTickets->where('unit_id', $unitId);
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
                    return $period;
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

    public function ticketCategories(Request $request)
    {
        $unitId = $request->query('unit_id');

        $query = DB::table('tickets')
            ->join('users', 'tickets.user_id', '=', 'users.id')
            ->whereIn('users.role_id', [2, 3, 4])
            ->whereNull('tickets.deleted_at')
            ->select('users.role_id', DB::raw('COUNT(*) as count'))
            ->groupBy('users.role_id');

        if ($unitId) {
            $query->where('tickets.unit_id', $unitId);
        }

        $roleCounts = $query->get();

        $data = $roleCounts->mapWithKeys(function ($item) {
            $roleNames = [2 => 'Operator', 3 => 'Staff', 4 => 'Pengguna'];
            $roleName = $roleNames[$item->role_id] ?? 'Unknown';
            return [$roleName => $item->count];
        })->all();

        return response()->json([
            'labels' => array_keys($data),
            'counts' => array_values($data)
        ]);
    }

    public function resolutionTimes(Request $request)
    {
        $unitId = $request->query('unit_id');

        $query = DB::table('tickets')
            ->join('services', 'tickets.service_id', '=', 'services.id')
            ->select(
                'services.svc_name as service_name',
                DB::raw('FLOOR(AVG(TIMESTAMPDIFF(DAY, tickets.created_at, tickets.updated_at))) as avgResolutionDays')
            )
            ->whereNotNull('tickets.updated_at')
            ->where('tickets.status', 2)
            ->whereNull('tickets.deleted_at')
            ->groupBy('services.svc_name');

        if ($unitId) {
            $query->where('tickets.unit_id', $unitId);
        }

        $resolutionTimes = $query->get();

        return response()->json([
            'services' => $resolutionTimes->pluck('service_name'),
            'avgResolutionDays' => $resolutionTimes->pluck('avgResolutionDays')
        ]);
    }

    public function recentTickets(Request $request)
    {
        $unitId = $request->query('unit_id');
        $query = Ticket::select('ticket_code', 'title', 'status', 'created_at', 'unit_id')
            ->with('service.unit')
            ->latest()
            ->take(10);

        if ($unitId) {
            $query->where('unit_id', $unitId);
        }

        $tickets = $query->get()->map(function ($ticket) {
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
                'created_at' => $ticket->created_at->toDateTimeString(),
                'unit_name' => $unit_name->unit_name ?? 'N/A',
            ];
        });

        return response()->json($tickets);
    }

    public function units()
    {
        $units = Unit::select('id', 'unit_name as name')->get();
        return response()->json($units);
    }

    public function serviceStats(Request $request)
    {
        $unitId = $request->query('unit_id');
        $query = Service::query()
            ->select('id', 'svc_name')
            ->where('status', 'active');

        if ($unitId) {
            $query->where('unit_id', $unitId);
        }

        $services = $query->get()->map(function ($service) use ($unitId) {
            $ticketQuery = Ticket::where('service_id', $service->id);
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

    public function serviceDistribution(Request $request)
    {
        $unitId = $request->query('unit_id');
        $query = DB::table('tickets')
            ->join('services', 'tickets.service_id', '=', 'services.id')
            ->whereNull('tickets.deleted_at')
            ->select('services.svc_name', DB::raw('COUNT(*) as count'))
            ->groupBy('services.svc_name')
            ->orderBy('count', 'desc');

        if ($unitId) {
            $query->where('tickets.unit_id', $unitId);
        }

        $serviceDistribution = $query->get();

        return response()->json([
            'labels' => $serviceDistribution->pluck('svc_name')->all(),
            'counts' => $serviceDistribution->pluck('count')->all()
        ]);
    }

    public function unitDistribution(Request $request)
    {
        $unitId = $request->query('unit_id');

        // Query untuk mengambil semua unit, termasuk yang tidak memiliki pengaduan
        $query = DB::table('units')
            ->leftJoin('tickets', 'units.id', '=', 'tickets.unit_id')
            ->whereNull('tickets.deleted_at')
            ->select('units.unit_name', DB::raw('COALESCE(COUNT(tickets.id), 0) as count'))
            ->groupBy('units.unit_name')
            ->orderBy('count', 'desc');

        // Jika ada filter unit_id (opsional)
        if ($unitId) {
            $query->where('units.id', $unitId);
        }

        $unitDistribution = $query->get();

        return response()->json([
            'labels' => $unitDistribution->pluck('unit_name')->all(),
            'counts' => $unitDistribution->pluck('count')->all()
        ]);
    }

}
