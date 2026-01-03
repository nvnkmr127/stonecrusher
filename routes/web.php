<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\MetalTypeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // User Management
    Route::resource('users', UserController::class);
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

    // Master Data
    // Attendance (Policy handled in controller, but route access mainly Admin)
    Route::get('attendance/report', [App\Http\Controllers\AttendanceReportController::class, 'index'])->name('attendance.report');
    Route::get('attendance/report/export', [App\Http\Controllers\AttendanceReportController::class, 'export'])->name('attendance.report.export');
    Route::resource('attendance', AttendanceController::class);

    // Restricted Transaction Edits (Admin Only)
    Route::get('clients/{client}/transactions/{transaction}/edit', [App\Http\Controllers\ClientTransactionController::class, 'edit'])->name('clients.transactions.edit');
    Route::put('clients/{client}/transactions/{transaction}', [App\Http\Controllers\ClientTransactionController::class, 'update'])->name('clients.transactions.update');
});

// Admin & Manager Routes
Route::middleware(['auth', 'verified', 'role:admin|manager'])->group(function () {

    // Client Reports (Placed before resource to avoid ID collision)
    Route::get('clients/reports/outstanding', [App\Http\Controllers\ClientReportController::class, 'index'])->name('clients.reports.outstanding');
    Route::get('clients/reports/outstanding/export', [App\Http\Controllers\ClientReportController::class, 'export'])->name('clients.reports.outstanding.export');

    // Master Data
    Route::resource('clients', ClientController::class);
    Route::resource('vehicles', VehicleController::class); // Assuming managers need this too? Keep it here for consistency if needed, else leave in Admin. 
    // Actually, stick to just Clients for now to be safe.

    // Client Transactions
    Route::get('clients/{client}/transactions/create', [App\Http\Controllers\ClientTransactionController::class, 'create'])->name('clients.transactions.create');
    Route::post('clients/{client}/transactions', [App\Http\Controllers\ClientTransactionController::class, 'store'])->name('clients.transactions.store');
});

// User Routes
Route::middleware(['auth', 'verified', 'role:user'])->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
});

require __DIR__ . '/auth.php';
