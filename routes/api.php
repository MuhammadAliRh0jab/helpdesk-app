<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;

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
    Route::get('/ticket-stats', [TicketController::class, 'ticketStats']);
    Route::get('/ticket-performance', [TicketController::class, 'ticketPerformance']);
    Route::get('/ticket-categories', [TicketController::class, 'ticketCategories']);
    Route::get('/resolution-times', [TicketController::class, 'resolutionTimes']);
    Route::get('/ticket-locations', [TicketController::class, 'ticketLocations']);
    Route::get('/recent-tickets', [TicketController::class, 'recentTickets']);
    Route::get('/units', [TicketController::class, 'units']);
    Route::get('/service-stats', [TicketController::class, 'serviceStats']);
    Route::get('/service-distribution', [TicketController::class, 'serviceDistribution']);

    // Endpoint untuk Pegawai Dashboard
    Route::get('/pegawai-ticket-stats', [TicketController::class, 'pegawaiTicketStats']);
    Route::get('/pegawai-resolution-times', [TicketController::class, 'pegawaiResolutionTimes']);
    Route::get('/pegawai-recent-tickets', [TicketController::class, 'pegawaiRecentTickets']);
    
});