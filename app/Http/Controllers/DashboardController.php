<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Pastikan hanya operator yang dapat mengakses
        $user = auth()->user();
        if ($user->role_id != 2) {
            abort(403, 'Anda tidak diizinkan mengakses halaman ini.');
        }

        // Ambil data laporan dari unit operator
        $totalReports = Ticket::where('unit_id', $user->unit_id)->count();
        $respondedReports = Ticket::where('unit_id', $user->unit_id)
            ->where('status', 1) // Status 1 = Ditugaskan
            ->count();
        $completedReports = Ticket::where('unit_id', $user->unit_id)
            ->where('status', 2) // Status 2 = Resolved
            ->count();
        $unrespondedReports = Ticket::where('unit_id', $user->unit_id)
            ->where('status', 0) // Status 0 = Pending
            ->count();

        // Data untuk diagram
        $chartData = [
            'labels' => ['Total', 'Direspon', 'Selesai', 'Belum Direspon'],
            'data' => [
                $totalReports,
                $respondedReports,
                $completedReports,
                $unrespondedReports
            ],
            'backgroundColor' => [
                'rgba(54, 162, 235, 0.6)',
                'rgba(75, 192, 192, 0.6)',
                'rgba(153, 102, 255, 0.6)',
                'rgba(255, 99, 132, 0.6)'
            ]
        ];

        return view('dashboard.index', compact('totalReports', 'respondedReports', 'completedReports', 'unrespondedReports', 'chartData'));
    }
}