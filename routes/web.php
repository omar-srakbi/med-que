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
use App\Http\Controllers\Api\DepartmentServiceController;
use App\Http\Controllers\Api\PatientSearchController;
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
    Route::resource('patients', PatientController::class);
    Route::get('patients/incomplete', [IncompletePatientController::class, 'index'])->name('patients.incomplete');
    Route::get('patients/{patient}/complete', [IncompletePatientController::class, 'complete'])->name('patients.complete');
    Route::post('patients/{patient}/complete', [IncompletePatientController::class, 'update']);
    
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
    
    // Audit Logs (Admin only)
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('audit-logs/export', [AuditLogController::class, 'export'])->name('audit-logs.export');
    
    // Reports routes
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/daily-patients', [ReportController::class, 'dailyPatients'])->name('reports.daily-patients');
    Route::get('reports/daily-revenue', [ReportController::class, 'dailyRevenue'])->name('reports.daily-revenue');
    Route::get('reports/patient-history', [ReportController::class, 'patientHistory'])->name('reports.patient-history');
    
    // Search
    Route::get('search', [SearchController::class, 'search'])->name('search');
    
    // Queue display
    Route::get('/queue/display', [QueueController::class, 'display'])->name('queue.display');
    
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });
});
