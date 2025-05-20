<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Pic;
use App\Models\Unit;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OperatorDashboardController extends Controller
{
    public function ticketStats(Request $request)
{
    $user = auth()->user();
    $query = Ticket::query();

    if ($user->role_id == 3) {
        // Untuk pegawai, hanya hitung tiket yang mereka buat atau ditugaskan kepada mereka
        $picIds = Pic::where('user_id', $user->id)->pluck('id')->toArray();
        $query->where('user_id', $user->id)
            ->orWhereIn('id', function ($subQuery) use ($picIds) {
                $subQuery->select('ticket_id')
                    ->from('ticket_pic')
                    ->whereIn('pic_id', $picIds)
                    ->where('pic_stats', 'active');
            });
    } elseif ($user->role_id == 2) {
        // Untuk operator, batasi ke unit mereka
        $query->where('unit_id', $user->unit_id);
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

        $pendingTickets = Ticket::where('status', 0)
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

        $assignedTickets = Ticket::where('status', 1)
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

    public function units()
    {
        $units = Unit::select('id', 'unit_name as name')->get();
        return response()->json($units);
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

    public function serviceDistribution(Request $request)
{
    $user = auth()->user();
    $query = DB::table('tickets')
        ->join('services', 'tickets.service_id', '=', 'services.id')
        ->whereNull('tickets.deleted_at')
        ->select('services.svc_name', DB::raw('COUNT(*) as count'))
        ->groupBy('services.svc_name')
        ->orderBy('count', 'desc');

    if ($user->role_id == 3) {
        $picIds = Pic::where('user_id', $user->id)->pluck('id')->toArray();
        $query->where('tickets.user_id', $user->id)
            ->orWhereIn('tickets.id', function ($subQuery) use ($picIds) {
                $subQuery->select('ticket_id')
                    ->from('ticket_pic')
                    ->whereIn('pic_id', $picIds)
                    ->where('pic_stats', 'active');
            });
    } elseif ($user->role_id == 2) {
        $unitId = $request->query('unit_id', $user->unit_id);
        $query->where('tickets.unit_id', $unitId);
    }

    $serviceDistribution = $query->get();

    return response()->json([
        'labels' => $serviceDistribution->pluck('svc_name')->all(),
        'counts' => $serviceDistribution->pluck('count')->all()
    ]);
}
}
