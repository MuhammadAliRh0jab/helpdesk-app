<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\PegawaiDashboardController;
use App\Http\Controllers\OperatorDashboardContorller;
use App\Http\Controllers\WargaDashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    // Endpoint untuk Operator Dashboard
    Route::get('/ticket-stats', [OperatorDashboardContorller::class, 'ticketStats']);
    Route::get('/ticket-performance', [OperatorDashboardContorller::class, 'ticketPerformance']);
    Route::get('/ticket-categories', [OperatorDashboardContorller::class, 'ticketCategories']);
    Route::get('/resolution-times', [OperatorDashboardContorller::class, 'resolutionTimes']);
    Route::get('/recent-tickets', [OperatorDashboardContorller::class, 'recentTickets']);
    Route::get('/units', [OperatorDashboardContorller::class, 'units']);
    Route::get('/service-stats', [OperatorDashboardContorller::class, 'serviceStats']);
    Route::get('/service-distribution', [OperatorDashboardContorller::class, 'serviceDistribution']);
    Route::get('/ticket-performance', [OperatorDashboardContorller::class, 'ticketPerformance']);

    // Route::get('/pegawai-recent-tickets', [PegawaiDashboardController::class, 'getRecentTickets']);
    // Route::get('/pegawai-ticket-stats', [PegawaiDashboardController::class, 'getTicketStats']);
    // Route::get('/pegawai-ticket-distribution/created', [PegawaiDashboardController::class, 'getTicketDistributionCreated']);
    // Route::get('/pegawai-ticket-distribution/assigned', [PegawaiDashboardController::class, 'getTicketDistributionAssigned']);
    // Route::get('/pegawai-resolution-times', [PegawaiDashboardController::class, 'getResolutionTimes']);
});

Route::middleware('auth:api')->group(function () {
    Route::get('/pegawai-recent-tickets', [PegawaiDashboardController::class, 'getRecentTickets']);
    Route::get('/pegawai-ticket-stats', [PegawaiDashboardController::class, 'getTicketStats']);
    Route::get('/pegawai-ticket-distribution-created', [PegawaiDashboardController::class, 'getTicketDistributionCreated']);
    Route::get('/pegawai-ticket-distribution-assigned', [PegawaiDashboardController::class, 'getTicketDistributionAssigned']);
    Route::get('/pegawai-resolution-times', [PegawaiDashboardController::class, 'getResolutionTimes']);
    Route::get('/pegawai-completed-tickets-history', [PegawaiDashboardController::class, 'getCompletedTicketsHistory']);
    Route::get('/pegawai-resolution-time-by-service', [PegawaiDashboardController::class, 'getResolutionTimeByService']);
    Route::get('/pegawai-completed-tickets', [PegawaiDashboardController::class, 'getCompletedTickets']);
    Route::get('/pegawai-ticket-distribution', [PegawaiDashboardController::class, 'getTicketDistribution']);
    Route::get('/pegawai-assignment-completion', [PegawaiDashboardController::class, 'getAssignmentCompletion']);
    Route::get('/pegawai-ticket-list', [PegawaiDashboardController::class, 'getTicketList']);
    Route::get('/pegawai-dashboard-metrics', [PegawaiDashboardController::class, 'getDashboardMetrics']);
    Route::get('/pegawai-stats', [PegawaiDashboardController::class, 'getStats']);
    Route::get('/warga/ticket-stats', [WargaDashboardController::class, 'getTicketStats']);
    Route::get('/warga/tickets', [WargaDashboardController::class, 'getTickets']);
    Route::get('/warga/tickets/{id}', [WargaDashboardController::class, 'getTicketDetail']);
    Route::get('/warga/static-stats', [WargaDashboardController::class, 'getStaticStats']);
});