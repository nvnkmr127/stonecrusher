<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\GatePassController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\MetalTypeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\WelcomeController;

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

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

    // Audit Logs
    Route::get('/audit-logs', [App\Http\Controllers\AuditLogController::class, 'index'])->name('audit-logs.index');

    // Backups
    Route::get('/backups', [App\Http\Controllers\BackupController::class, 'index'])->name('backups.index');
    Route::post('/backups', [App\Http\Controllers\BackupController::class, 'create'])->name('backups.create');
    Route::get('/backups/download', [App\Http\Controllers\BackupController::class, 'download'])->name('backups.download');
    Route::post('/backups/restore', [App\Http\Controllers\BackupController::class, 'restore'])->name('backups.restore');
    Route::delete('/backups', [App\Http\Controllers\BackupController::class, 'destroy'])->name('backups.destroy');

    // Google Drive OAuth
    Route::get('/google-drive/redirect', [App\Http\Controllers\GoogleDriveController::class, 'redirect'])->name('google-drive.redirect');
    Route::get('/google-drive/callback', [App\Http\Controllers\GoogleDriveController::class, 'callback'])->name('google-drive.callback');

    // User Management
    Route::resource('users', UserController::class);
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

    // Master Data
    // Attendance (Policy handled in controller, but route access mainly Admin)
    Route::get('attendance/report/daily', [App\Http\Controllers\AttendanceReportController::class, 'daily'])->name('attendance.report.daily');
    Route::get('attendance/report', [App\Http\Controllers\AttendanceReportController::class, 'index'])->name('attendance.report');
    Route::get('attendance/report/export', [App\Http\Controllers\AttendanceReportController::class, 'export'])->name('attendance.report.export');
    Route::get('attendance/report/export-pdf', [App\Http\Controllers\AttendanceReportController::class, 'exportPdf'])->name('attendance.report.export-pdf');
    Route::resource('attendance', AttendanceController::class)->except(['show']);

    // Restricted Transaction Edits (Admin Only)
    Route::get('clients/{client}/transactions/{transaction}/edit', [App\Http\Controllers\ClientTransactionController::class, 'edit'])->name('clients.transactions.edit');
    Route::put('clients/{client}/transactions/{transaction}', [App\Http\Controllers\ClientTransactionController::class, 'update'])->name('clients.transactions.update');
    Route::delete('clients/{client}/transactions/{transaction}', [App\Http\Controllers\ClientTransactionController::class, 'destroy'])->name('clients.transactions.destroy');

    // Daily Closing
    Route::resource('daily-closings', App\Http\Controllers\DailyClosingController::class)->only(['index', 'create', 'store']);
    Route::post('daily-closings/{daily_closing}/reopen', [App\Http\Controllers\DailyClosingController::class, 'reopen'])->name('daily-closings.reopen');
});

// Admin & Manager & Accountant Routes
Route::middleware(['auth', 'verified', 'role:admin|manager|accountant'])->group(function () {

    // Client Reports (Placed before resource to avoid ID collision)
    Route::get('clients/reports/outstanding', [App\Http\Controllers\ClientReportController::class, 'index'])->name('clients.reports.outstanding');
    Route::get('clients/reports/outstanding/export', [App\Http\Controllers\ClientReportController::class, 'export'])->name('clients.reports.outstanding.export');
    Route::get('clients/reports/outstanding/export-pdf', [App\Http\Controllers\ClientReportController::class, 'exportPdf'])->name('clients.reports.outstanding.export-pdf');

    // Master Data
    Route::resource('clients', ClientController::class);

    // Reports (General)
    Route::get('reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/daily', [App\Http\Controllers\ReportController::class, 'daily'])->name('reports.daily');
    Route::get('reports/daily/export', [App\Http\Controllers\ReportController::class, 'exportDaily'])->name('reports.daily.export');
    Route::get('reports/outstanding', [App\Http\Controllers\ReportController::class, 'outstanding'])->name('reports.outstanding');
    Route::get('reports/monthly', [App\Http\Controllers\ReportController::class, 'monthly'])->name('reports.monthly');
    Route::get('reports/monthly/export', [App\Http\Controllers\ReportController::class, 'exportMonthly'])->name('reports.monthly.export');
    Route::get('reports/custom', [App\Http\Controllers\ReportController::class, 'custom'])->name('reports.custom');
    Route::get('reports/custom/export', [App\Http\Controllers\ReportController::class, 'exportCustom'])->name('reports.custom.export');
    Route::get('reports/summary/{type}', [App\Http\Controllers\ReportController::class, 'summary'])->name('reports.summary');
    Route::get('reports/summary/{type}/export', [App\Http\Controllers\ReportController::class, 'exportSummary'])->name('reports.summary.export');

    // Gate Pass Reports
    Route::get('gate-passes/daily-report', [GatePassController::class, 'dailyReport'])->name('gate-passes.daily-report');
    Route::get('gate-passes/distance-report', [GatePassController::class, 'distanceReport'])->name('gate-passes.distance-report');
    Route::get('gate-passes/distance-report/export', [GatePassController::class, 'exportDistanceReport'])->name('gate-passes.distance-report.export');
    Route::get('gate-passes/calculator', [GatePassController::class, 'calculator'])->name('gate-passes.calculator');
    Route::get('gate-passes/search-location', [GatePassController::class, 'searchLocation'])->name('gate-passes.search-location');
    Route::post('gate-passes/{gate_pass}/payment', [GatePassController::class, 'recordPayment'])->name('gate-passes.payment');
    Route::resource('gate-passes', GatePassController::class);
    Route::resource('vehicles', VehicleController::class);
    Route::resource('projects', ProjectController::class);
    Route::resource('metal-types', MetalTypeController::class);

    // Client Transactions
    Route::get('clients/{client}/transactions/create', [App\Http\Controllers\ClientTransactionController::class, 'create'])->name('clients.transactions.create');
    Route::post('clients/{client}/transactions', [App\Http\Controllers\ClientTransactionController::class, 'store'])->name('clients.transactions.store');
});

// User Routes
Route::middleware(['auth', 'verified', 'role:user'])->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
});

require __DIR__ . '/auth.php';
