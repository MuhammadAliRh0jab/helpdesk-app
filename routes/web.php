<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceManagementController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WargaDashboardController;
use App\Models\Service;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', fn() => view('theme::auth.landing'))->name('landing');
Route::get('/laporsebagaitamu', [TicketController::class, 'createGuest'])->name('guest');
Route::post('/tickets/guest', [TicketController::class, 'storeGuest'])->name('tickets.store.guest');
Route::get('/layanan/{uuid}', [TicketController::class, 'createForService'])
    ->name('tickets.create.service')
    ->where('uuid', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}')
    ->middleware('web');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout')
    ->middleware('auth');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Dashboard Routes
    Route::get('/dashboard/warga', [WargaDashboardController::class, 'index'])
        ->name('dashboard.warga')
        ->middleware('role:4');
    Route::get('/dashboard/pegawai', [DashboardController::class, 'pegawai'])
        ->name('dashboard.pegawai')
        ->middleware('role:3');
    Route::get('/dashboard/operator', [DashboardController::class, 'operatorDashboard'])
        ->name('dashboard.operator');
    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])
        ->name('dashboard.admin')
        ->middleware('role:1');

    // Ticket Routes
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/assigned', [TicketController::class, 'assigned'])->name('tickets.assigned');
    Route::get('/tickets/created', [TicketController::class, 'created'])
        ->name('tickets.created')
        ->middleware('role:2');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::post('/tickets/{ticket}/assign', [TicketController::class, 'assign'])
        ->name('tickets.assign')
        ->middleware('role:2');
    Route::post('/tickets/{ticket}/transfer', [TicketController::class, 'transfer'])
        ->name('tickets.transfer')
        ->middleware('role:2');
    Route::post('/tickets/{ticket}/respond', [TicketController::class, 'respond'])
        ->name('tickets.respond')
        ->middleware('role:3');
    Route::post('/tickets/{ticket}/remove-pic', [TicketController::class, 'removePic'])
        ->name('tickets.removePic')
        ->middleware('role:2');
    Route::patch('/tickets/{ticket}', [TicketController::class, 'update'])
        ->name('tickets.update')
        ->middleware('role:3');
    Route::post('/tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('tickets.reply');
    Route::get('/get-services/{unitId}', [TicketController::class, 'getServices'])->name('get.services');

    Route::prefix('users')->middleware('role:1')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::get('/{user}', [UserController::class, 'show'])->name('users.show');
        Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.resetPassword');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // Profile Routes
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile', [UserController::class, 'updateProfile'])->name('profile.update');

    // Unit Management Routes (Super Admin)
    Route::resource('units', UnitController::class)->middleware('role:1');

    // Service Management Routes (Super Admin)
    Route::resource('services', ServiceController::class)->middleware('role:1');

    // Service Management Routes (Operator)
    Route::middleware('role:2')->group(function () {
        Route::get('/services', [ServiceManagementController::class, 'index'])->name('services.index');
        Route::get('/services/create', [ServiceManagementController::class, 'create'])->name('services.create');
        Route::post('/services', [ServiceManagementController::class, 'store'])->name('services.store');
        Route::get('/services/{service}/edit', [ServiceManagementController::class, 'edit'])->name('services.edit');
        Route::put('/services/{service}', [ServiceManagementController::class, 'update'])->name('services.update');
        Route::get('/services/{uuid}/qrcode', [ServiceManagementController::class, 'generateQrCode'])
            ->name('services.qrcode')
            ->where('uuid', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');
        Route::get('/services/{uuid}/qrcode/download', [ServiceManagementController::class, 'downloadQrCode'])
            ->name('services.qrcode.download')
            ->where('uuid', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');
        Route::get('/services/{uuid}/qrcode/data', [ServiceManagementController::class, 'getQrCode'])
            ->name('services.qrcode.data')
            ->where('uuid', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');
    });

    // Settings Routes (Super Admin)
    Route::middleware('role:1')->group(function () {
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::patch('/settings/pengadu_message_limit', [SettingController::class, 'updateMessageLimit'])
            ->name('settings.updateMessageLimit');
    });
});

// Favicon Route
Route::get('/favicon.ico', fn() => response()->noContent());

// Route Model Binding for UUID
Route::bind('uuid', function ($value) {
    return Service::where('uuid', $value)->firstOrFail();
});

// Include Additional Auth Routes
require __DIR__ . '/auth.php';
