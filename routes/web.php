<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceManagementController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('mobile.auth.landing');
})->name('landing');

// Routes untuk guest (belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

// Route untuk logout
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout')->middleware('auth');

// Routes yang memerlukan autentikasi
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/warga', [DashboardController::class, 'warga'])->name('dashboard.warga')->middleware('role:4');
    Route::get('/dashboard/pegawai', [DashboardController::class, 'pegawai'])->name('dashboard.pegawai')->middleware('role:3');
    Route::get('/dashboard/operator', [DashboardController::class, 'operator'])->name('dashboard.operator')->middleware('role:2');
    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])->name('dashboard.admin')->middleware('role:1');
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/assigned', [TicketController::class, 'assigned'])->name('tickets.assigned');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create'); // Hapus middleware role:4
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::post('/tickets/{ticket}/assign', [TicketController::class, 'assign'])->middleware('role:2')->name('tickets.assign');
    Route::post('/tickets/{ticket}/transfer', [TicketController::class, 'transfer'])->middleware('role:2')->name('tickets.transfer');
    Route::post('/tickets/{ticket}/respond', [TicketController::class, 'respond'])->middleware('role:3')->name('tickets.respond');
    Route::post('/tickets/{ticket}/remove-pic', [TicketController::class, 'removePic'])->middleware('role:2')->name('tickets.removePic');
    Route::patch('/tickets/{ticket}', [TicketController::class, 'update'])->middleware('role:3')->name('tickets.update');
    Route::post('/tickets/reply/{response}', [TicketController::class, 'reply'])->middleware('role:4')->name('tickets.reply');
    Route::resource('users', UserController::class)->middleware('role:1');
    Route::resource('units', UnitController::class)->middleware('role:1');
    Route::resource('services', ServiceController::class)->middleware('role:1');
    Route::get('/get-services/{unitId}', [TicketController::class, 'getServices'])->name('get.services');
    Route::get('/services', [ServiceManagementController::class, 'index'])->middleware('role:2')->name('services.index');
    Route::patch('/services/{service}/status', [ServiceManagementController::class, 'updateStatus'])->middleware('role:2')->name('services.updateStatus');
    Route::get('/tickets/created', [TicketController::class, 'created'])->middleware('role:2')->name('tickets.created');
    // Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('role:2')->name('dashboard.index');
});

// Tambahkan route untuk favicon agar tidak mengganggu log
Route::get('/favicon.ico', function () {
    return response()->noContent();
});

// Sertakan route autentikasi tambahan jika ada
require __DIR__.'/auth.php';
