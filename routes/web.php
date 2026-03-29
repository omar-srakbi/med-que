<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\DashboardController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AddAdminController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\IncompletePatientController;
use App\Http\Controllers\PrintSettingsController;
use App\Http\Controllers\Api\DepartmentServiceController;
use App\Http\Controllers\Api\PatientSearchController;
use App\Http\Controllers\ReportSettingsController;
use App\Http\Controllers\ReportBuilderController;
use App\Http\Controllers\ReportExportController;
use Illuminate\Support\Facades\Route;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    
    // Add first admin (for new database setup)
    Route::get('/addadmin', [AddAdminController::class, 'index'])->name('add-admin.index');
    Route::post('/addadmin', [AddAdminController::class, 'store'])->name('add-admin.store');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::post('/language/switch', [LanguageController::class, 'switch'])->name('language.switch');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/api-data', [DashboardController::class, 'apiData'])->name('dashboard.api-data');
    
    // API routes
    Route::get('/api/departments/{departmentId}/services', [DepartmentServiceController::class, 'index']);
    Route::get('/api/patients/search', [PatientSearchController::class, 'search']);
    Route::post('/api/patients/search-or-create', [PatientSearchController::class, 'searchOrCreate']);
    
    // Patient routes
    Route::get('patients/incomplete', [IncompletePatientController::class, 'index'])->name('patients.incomplete');
    Route::get('patients/{patient}/complete', [IncompletePatientController::class, 'complete'])->name('patients.complete');
    Route::post('patients/{patient}/complete', [IncompletePatientController::class, 'update']);
    Route::resource('patients', PatientController::class);
    
    // Staff routes (admin only)
    Route::resource('staff', StaffController::class)->middleware('role:Admin');
    
    // Role routes (admin only)
    Route::resource('roles', RoleController::class)->middleware('role:Admin');
    Route::post('roles/{role}/reassign-users', [RoleController::class, 'reassignUsers'])->name('roles.reassign-users');
    Route::post('roles/{role}/toggle-active', [RoleController::class, 'toggleActive'])->name('roles.toggle-active');
    Route::post('roles/{role}/destroy-with-password', [RoleController::class, 'destroyWithPassword'])->name('roles.destroy-with-password');
    Route::post('roles/update-order', [RoleController::class, 'updateOrder'])->name('roles.update-order');
    
    // Department routes (admin only)
    Route::resource('departments', DepartmentController::class)->middleware('role:Admin');
    Route::post('departments/{department}/services', [DepartmentController::class, 'addService'])->name('departments.services.add');
    Route::put('departments/services/{service}', [DepartmentController::class, 'updateService'])->name('departments.services.update');
    Route::delete('departments/services/{service}', [DepartmentController::class, 'destroyService'])->name('departments.services.destroy');
    Route::post('departments/{department}/ticket-settings', [DepartmentController::class, 'updateTicketSettings'])->name('departments.ticket-settings');
    
    // Ticket routes
    Route::resource('tickets', TicketController::class);
    Route::get('/tickets/{ticket}/receipt', [TicketController::class, 'receipt'])->name('tickets.receipt');
    Route::post('/tickets/{ticket}/call', [TicketController::class, 'call'])->name('tickets.call');
    Route::post('/tickets/{ticket}/complete', [TicketController::class, 'complete'])->name('tickets.complete');
    Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])->name('tickets.destroy');
    
    // Medical Records routes
    Route::resource('medical-records', MedicalRecordController::class);

    // Settings routes
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');
    
    // Print Settings routes
    Route::get('settings/printing', [PrintSettingsController::class, 'index'])->name('settings.printing.index');
    Route::post('settings/printing', [PrintSettingsController::class, 'update'])->name('settings.printing.update');
    Route::post('settings/printing/receipt', [PrintSettingsController::class, 'updateReceipt'])->name('settings.printing.updateReceipt');
    Route::post('settings/printing/report', [PrintSettingsController::class, 'updateReport'])->name('settings.printing.updateReport');
    Route::get('settings/printing/designer', [PrintSettingsController::class, 'designer'])->name('settings.printing.designer');
    Route::post('settings/printing/save-layout', [PrintSettingsController::class, 'saveLayout'])->name('settings.printing.save-layout');
    Route::post('settings/printing/template/{id}/default', [PrintSettingsController::class, 'setDefaultTemplate'])->name('settings.printing.template.default');
    Route::delete('settings/printing/template/{id}', [PrintSettingsController::class, 'deleteTemplate'])->name('settings.printing.template.delete');
    Route::get('settings/printing/preview', [PrintSettingsController::class, 'previewReceipt'])->name('settings.printing.preview');
    Route::post('settings/printing/preview', [PrintSettingsController::class, 'previewAjax'])->name('settings.printing.preview.ajax');
    Route::get('settings/printing/logs', [PrintSettingsController::class, 'printLogs'])->name('settings.printing.logs');
    Route::get('settings/printing/logs/export', [PrintSettingsController::class, 'exportLogs'])->name('settings.printing.logs.export');
    Route::get('api/print-settings/{type}', [PrintSettingsController::class, 'getSettings'])->name('api.print-settings.get');
    Route::get('settings/printing/reports', [ReportSettingsController::class, 'index'])->name('settings.printing.reports.index');
    Route::post('settings/printing/reports', [ReportSettingsController::class, 'update'])->name('settings.printing.reports.update');
    Route::post('settings/printing/reports/preview', [ReportSettingsController::class, 'previewAjax'])->name('settings.printing.reports.preview.ajax');
    Route::post('settings/printing/reports/export', [ReportSettingsController::class, 'export'])->name('settings.printing.reports.export');
    
    // Audit Logs (Admin only)
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('audit-logs/export', [AuditLogController::class, 'export'])->name('audit-logs.export');
    
    // Reports routes
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/daily-patients', [ReportController::class, 'dailyPatients'])->name('reports.daily-patients');
    Route::get('reports/daily-revenue', [ReportController::class, 'dailyRevenue'])->name('reports.daily-revenue');
    Route::get('reports/patient-history', [ReportController::class, 'patientHistory'])->name('reports.patient-history');
    Route::get('reports/monthly-revenue', [ReportController::class, 'monthlyRevenue'])->name('reports.monthly-revenue');
    Route::get('reports/department-performance', [ReportController::class, 'departmentPerformance'])->name('reports.department-performance');
    Route::get('reports/cashier-performance', [ReportController::class, 'cashierPerformance'])->name('reports.cashier-performance');
    Route::get('reports/services', [ReportController::class, 'services'])->name('reports.services');
    Route::get('reports/patient-demographics', [ReportController::class, 'patientDemographics'])->name('reports.patient-demographics');
    Route::get('reports/visit-frequency', [ReportController::class, 'visitFrequency'])->name('reports.visit-frequency');
    
    // Report exports
    Route::get('reports/export/pdf/{type}', [ReportExportController::class, 'exportPdf'])->name('reports.export.pdf');
    Route::get('reports/export/excel/{type}', [ReportExportController::class, 'exportExcel'])->name('reports.export.excel');
    Route::get('reports/export/csv/{type}', [ReportExportController::class, 'exportCsv'])->name('reports.export.csv');
    
    // Report builder
    Route::get('reports/builder', [ReportBuilderController::class, 'index'])->name('reports.builder.index');
    Route::get('reports/builder/create', [ReportBuilderController::class, 'create'])->name('reports.builder.create');
    Route::post('reports/builder', [ReportBuilderController::class, 'store'])->name('reports.builder.store');
    Route::get('reports/builder/{report}/edit', [ReportBuilderController::class, 'edit'])->name('reports.builder.edit');
    Route::put('reports/builder/{report}', [ReportBuilderController::class, 'update'])->name('reports.builder.update');
    Route::delete('reports/builder/{report}', [ReportBuilderController::class, 'destroy'])->name('reports.builder.destroy');
    Route::post('reports/builder/preview', [ReportBuilderController::class, 'preview'])->name('reports.builder.preview');
    Route::get('reports/builder/{report}/show', [ReportBuilderController::class, 'show'])->name('reports.builder.show');
    Route::post('reports/builder/{report}/update-labels', [ReportBuilderController::class, 'updateLabels'])->name('reports.builder.update-labels');
    
    // Search
    Route::get('search', [SearchController::class, 'search'])->name('search');
    
    // Queue display
    Route::get('/queue/display', [QueueController::class, 'display'])->name('queue.display');
    
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });
});
