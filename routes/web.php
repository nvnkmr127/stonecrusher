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
use App\Http\Controllers\EmployeeController;
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



    // Restricted Transaction Edits (Admin Only)
    Route::get('clients/{client}/transactions/{transaction}/edit', [App\Http\Controllers\ClientTransactionController::class, 'edit'])->name('clients.transactions.edit');
    Route::put('clients/{client}/transactions/{transaction}', [App\Http\Controllers\ClientTransactionController::class, 'update'])->name('clients.transactions.update');
    Route::delete('clients/{client}/transactions/{transaction}', [App\Http\Controllers\ClientTransactionController::class, 'destroy'])->name('clients.transactions.destroy');

    // Daily Closing
    Route::resource('daily-closings', App\Http\Controllers\DailyClosingController::class)->only(['index', 'create', 'store']);
    Route::post('daily-closings/{daily_closing}/reopen', [App\Http\Controllers\DailyClosingController::class, 'reopen'])->name('daily-closings.reopen');
});

// Owner & Admin Dashboard
Route::middleware(['auth', 'verified', 'role:admin|owner'])->group(function () {
    Route::get('/owner/dashboard', [App\Http\Controllers\OwnerDashboardController::class, 'index'])->name('owner.dashboard');
});

// Admin & Manager & Accountant Routes
Route::middleware(['auth', 'verified', 'role:admin|manager|accountant'])->group(function () {

    // Attendance (Policy handled in controller)
    Route::get('attendance/report/daily', [App\Http\Controllers\AttendanceReportController::class, 'daily'])->name('attendance.report.daily');
    Route::get('attendance/report', [App\Http\Controllers\AttendanceReportController::class, 'index'])->name('attendance.report');
    Route::get('attendance/report/export', [App\Http\Controllers\AttendanceReportController::class, 'export'])->name('attendance.report.export');
    Route::get('attendance/report/export-pdf', [App\Http\Controllers\AttendanceReportController::class, 'exportPdf'])->name('attendance.report.export-pdf');
    Route::post('attendance/report/lock', [App\Http\Controllers\AttendanceReportController::class, 'lock'])->name('attendance.report.lock');
    Route::post('attendance/report/release', [App\Http\Controllers\AttendanceReportController::class, 'release'])->name('attendance.report.release');
    Route::get('reports/liability', [App\Http\Controllers\PayrollLiabilityController::class, 'index'])->name('reports.liability');
    Route::get('attendance/bulk', [App\Http\Controllers\AttendanceController::class, 'bulk'])->name('attendance.bulk');
    Route::post('attendance/bulk', [App\Http\Controllers\AttendanceController::class, 'bulkStore'])->name('attendance.bulk.store');
    Route::post('attendance/ajax-store', [App\Http\Controllers\AttendanceController::class, 'ajaxStore'])->name('attendance.ajax.store');
    Route::get('attendance/calendar', [App\Http\Controllers\AttendanceController::class, 'calendar'])->name('attendance.calendar');
    Route::resource('attendance', AttendanceController::class)->except(['show']);

    // Client Reports (Placed before resource to avoid ID collision)
    Route::get('clients/reports/outstanding', [App\Http\Controllers\ClientReportController::class, 'index'])->name('clients.reports.outstanding');
    Route::get('clients/reports/outstanding/export', [App\Http\Controllers\ClientReportController::class, 'export'])->name('clients.reports.outstanding.export');
    Route::get('clients/reports/outstanding/export-pdf', [App\Http\Controllers\ClientReportController::class, 'exportPdf'])->name('clients.reports.outstanding.export-pdf');

    // Master Data
    Route::resource('clients', ClientController::class);
    Route::resource('employees', EmployeeController::class);
    Route::post('employees/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('employees.toggle-status');

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
    Route::get('reports/vehicle-usage', [App\Http\Controllers\ReportController::class, 'vehicleUsage'])->name('reports.vehicle-usage');

    // Gate Pass Reports
    Route::get('gate-passes/next-number', [GatePassController::class, 'nextNumber'])->name('gate-passes.next-number');
    Route::get('gate-passes/daily-report', [GatePassController::class, 'dailyReport'])->name('gate-passes.daily-report');
    Route::get('gate-passes/distance-report', [GatePassController::class, 'distanceReport'])->name('gate-passes.distance-report');
    Route::get('gate-passes/distance-report/export', [GatePassController::class, 'exportDistanceReport'])->name('gate-passes.distance-report.export');
    Route::get('gate-passes/calculator', [GatePassController::class, 'calculator'])->name('gate-passes.calculator');
    Route::get('gate-passes/search-location', [GatePassController::class, 'searchLocation'])->name('gate-passes.search-location');
    Route::post('gate-passes/{gate_pass}/payment', [GatePassController::class, 'recordPayment'])->name('gate-passes.payment');
    Route::resource('gate-passes', GatePassController::class);
    // Vehicle Management
    Route::get('vehicles/search', [VehicleController::class, 'search'])->name('vehicles.search');
    Route::post('vehicles/quick-store', [VehicleController::class, 'quickStore'])->name('vehicles.quick-store');
    Route::resource('vehicles', VehicleController::class);
    Route::resource('vehicle-maintenance', App\Http\Controllers\VehicleMaintenanceController::class);
    Route::post('vehicle-maintenance/{vehicle_maintenance}/complete', [App\Http\Controllers\VehicleMaintenanceController::class, 'markComplete'])->name('vehicle-maintenance.complete');
    Route::resource('projects', ProjectController::class);
    Route::resource('salary-advances', App\Http\Controllers\SalaryAdvanceController::class);
    Route::resource('metal-types', MetalTypeController::class);

    // Diesel Management
    Route::resource('diesel', App\Http\Controllers\DieselEntryController::class);
    Route::resource('diesel-stocks', App\Http\Controllers\DieselStockController::class);
    Route::resource('operational-units', App\Http\Controllers\OperationalUnitController::class);

    // Operational Records (Quarry & Crusher)
    Route::get('operations/quarry', [App\Http\Controllers\OperationalRecordController::class, 'quarryIndex'])->name('quarry.index');
    Route::get('operations/crusher', [App\Http\Controllers\OperationalRecordController::class, 'crusherIndex'])->name('crusher.index');
    Route::post('operations/{unit}/records', [App\Http\Controllers\OperationalRecordController::class, 'storeRecord'])->name('operations.records.store');
    Route::put('operations/records/{record}', [App\Http\Controllers\OperationalRecordController::class, 'updateRecord'])->name('operations.records.update');
    Route::delete('operations/records/{record}', [App\Http\Controllers\OperationalRecordController::class, 'destroyRecord'])->name('operations.records.destroy');
    Route::post('operations/{unit}/tags', [App\Http\Controllers\OperationalRecordController::class, 'storeTag'])->name('operations.tags.store');
    Route::delete('operations/tags/{tag}', [App\Http\Controllers\OperationalRecordController::class, 'destroyTag'])->name('operations.tags.destroy');

    // Operational Profit & Loss Report
    Route::get('reports/operational-profit-loss', [App\Http\Controllers\ReportController::class, 'operationalProfitLoss'])->name('reports.operational-profit-loss');

    // Crusher Profit Engine API Endpoints
    Route::get('api/crusher/{unit}/profitability', [App\Http\Controllers\CrusherProfitController::class, 'getProfitability'])->name('api.crusher.profitability');
    Route::get('api/crusher/{unit}/monthly-summary', [App\Http\Controllers\CrusherProfitController::class, 'getMonthlySummary'])->name('api.crusher.monthly-summary');

    // Quarry Cost Engine API Endpoints
    Route::get('api/quarry/{unit}/cost-breakdown', [App\Http\Controllers\QuarryCostController::class, 'getCostBreakdown'])->name('api.quarry.cost-breakdown');
    Route::get('api/quarry/{unit}/daily-summary', [App\Http\Controllers\QuarryCostController::class, 'getDailySummary'])->name('api.quarry.daily-summary');
    Route::get('api/quarry/{unit}/monthly-summary', [App\Http\Controllers\QuarryCostController::class, 'getMonthlySummary'])->name('api.quarry.monthly-summary');
    Route::get('api/quarry/{unit}/vendor-summary', [App\Http\Controllers\QuarryCostController::class, 'getVendorSummary'])->name('api.quarry.vendor-summary');

    // Monthly P&L Engine Endpoints
    Route::get('api/finance/profit-loss', [App\Http\Controllers\ProfitLossController::class, 'getProfitLoss'])->name('api.finance.profit-loss');
    Route::get('api/finance/profit-loss/monthly', [App\Http\Controllers\ProfitLossController::class, 'getMonthlySummary'])->name('api.finance.profit-loss.monthly');
    Route::get('api/finance/profit-loss/export', [App\Http\Controllers\ProfitLossController::class, 'export'])->name('api.finance.profit-loss.export');

    // Client Transactions
    Route::get('clients/{client}/transactions/create', [App\Http\Controllers\ClientTransactionController::class, 'create'])->name('clients.transactions.create');
    Route::post('clients/{client}/transactions', [App\Http\Controllers\ClientTransactionController::class, 'store'])->name('clients.transactions.store');
});

// User Routes
Route::middleware(['auth', 'verified', 'role:user'])->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
});

require __DIR__ . '/auth.php';
