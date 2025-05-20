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
use App\Http\Controllers\PegawaiDashboardController;
use App\Http\Controllers\WargaDashboardController;
use App\Http\Controllers\SettingController;

Route::get('/', function () {
    return view('theme::auth.landing');
})->name('landing');
Route::get('/laporsebagaitamu', function () {
    return view('theme::guest');
})->name('guest');
Route::get('/laporsebagaitamu', [TicketController::class, 'createGuest'])->name('guest');

Route::post('/tickets/guest', [TicketController::class, 'storeGuest'])->name('tickets.store.guest');

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
    Route::get('/dashboard/warga', [WargaDashboardController::class, 'index'])->name('dashboard.warga')->middleware('role:4');
    Route::get('/dashboard/pegawai', [DashboardController::class, 'pegawai'])->name('dashboard.pegawai')->middleware('role:3');
    Route::get('/dashboard/operator', [DashboardController::class, 'operatorDashboard'])->name('dashboard.operator')->middleware('auth');
    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])->name('dashboard.admin')->middleware('role:1');
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/assigned', [TicketController::class, 'assigned'])->name('tickets.assigned');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::post('/tickets/{ticket}/assign', [TicketController::class, 'assign'])->middleware('role:2')->name('tickets.assign');
    Route::post('/tickets/{ticket}/transfer', [TicketController::class, 'transfer'])->middleware('role:2')->name('tickets.transfer');
    Route::post('/tickets/{ticket}/respond', [TicketController::class, 'respond'])->middleware('role:3')->name('tickets.respond');
    Route::post('/tickets/{ticket}/remove-pic', [TicketController::class, 'removePic'])->middleware('role:2')->name('tickets.removePic');
    Route::patch('/tickets/{ticket}', [TicketController::class, 'update'])->middleware('role:3')->name('tickets.update');
    Route::post('tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('tickets.reply');

    // Rute untuk manajemen pengguna (Super_admin)
    Route::prefix('users')->middleware('role:1')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::get('/{user}', [UserController::class, 'show'])->name('users.show');
        Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.resetPassword');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // Rute untuk manajemen profil pengguna
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile', [UserController::class, 'updateProfile'])->name('profile.update');

    Route::resource('units', UnitController::class)->middleware('role:1');
    Route::resource('services', ServiceController::class)->middleware('role:1');
    Route::get('/get-services/{unitId}', [TicketController::class, 'getServices'])->name('get.services');
    Route::get('/services', [ServiceManagementController::class, 'index'])->middleware('role:2')->name('services.index');
    Route::get('/services/create', [ServiceManagementController::class, 'create'])->middleware('role:2')->name('services.create');
    Route::post('/services', [ServiceManagementController::class, 'store'])->middleware('role:2')->name('services.store');
    Route::patch('/services/{service}/status', [ServiceManagementController::class, 'updateStatus'])->middleware('role:2')->name('services.updateStatus');
    Route::patch('/services/{service}/allow-guest', [ServiceManagementController::class, 'updateAllowGuest'])->middleware('role:2')->name('services.updateAllowGuest');
    Route::patch('/services/{service}/category', [ServiceManagementController::class, 'updateCategory'])->middleware('role:2')->name('services.updateCategory');
    Route::get('/tickets/created', [TicketController::class, 'created'])->middleware('role:2')->name('tickets.created');
    Route::get('/services/{service}/edit', [ServiceManagementController::class, 'edit'])->middleware('role:2')->name('services.edit');
    Route::put('/services/{service}', [ServiceManagementController::class, 'update'])->middleware('role:2')->name('services.update');
    Route::get('/services/{service}/qrcode', [ServiceManagementController::class, 'generateQrCode'])
        ->middleware('role:2')
        ->name('services.qrcode');
    Route::get('/services/{service}/qrcode/download', [ServiceManagementController::class, 'downloadQrCode'])
        ->middleware('role:2')
        ->name('services.qrcode.download');
    Route::get('/services/{service}/qrcode/data', [ServiceManagementController::class, 'getQrCode'])
        ->middleware('role:2')
        ->name('services.qrcode.data');
    Route::middleware(['auth', 'role:1'])->group(function () {
        Route::get('/settings', [App\Http\Controllers\SettingController::class, 'index'])->name('settings.index')->middleware('role:1');
        Route::patch('/settings/pengadu_message_limit', [App\Http\Controllers\SettingController::class, 'updateMessageLimit'])->name('settings.updateMessageLimit')->middleware('role:1');
    });
    // Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('role:2')->name('dashboard.index');
});

// Tambahkan route untuk favicon agar tidak mengganggu log
Route::get('/favicon.ico', function () {
    return response()->noContent();
});

Route::get('/report/service/{service}', [TicketController::class, 'createForService'])
    ->name('tickets.create.service');

// Sertakan route autentikasi tambahan jika ada
require __DIR__ . '/auth.php';
