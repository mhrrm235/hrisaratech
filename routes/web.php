<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\PayrollsController;
use App\Http\Controllers\PresencesController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\InventoryCategoryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InventoryUsageLogController;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\LetterTemplateController;
use App\Http\Controllers\LetterConfigurationController;
use App\Http\Controllers\LetterArchiveController;
use App\Http\Controllers\SignatureController;
use App\Http\Controllers\KPIController;
use App\Http\Controllers\ReportingController;

Route::get('/', [AuthenticatedSessionController::class, 'create']);

Route::middleware(['auth'])->group(function () {
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard chart, buatan sendiri
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/presence', [DashboardController::class, 'presence']);

    // Resource routes for departments
    Route::resource('departments', DepartmentController::class)->middleware(['role:HR,Power User']);

    // Resource routes for roles
    Route::resource('roles', RoleController::class)->middleware(['role:HR,Power User']);

    // Resource routes for employees
    Route::resource('employees', EmployeeController::class)->middleware(['role:HR,Power User']);

    // Resource routes for tasks
    Route::resource('tasks', TaskController::class)->middleware(['role:Developer,HR,Power User']);
    Route::get('tasks/done/{id}', [TaskController::class, 'done'])->name('tasks.done');
    Route::get('tasks/pending/{id}', [TaskController::class, 'pending'])->name('tasks.pending');

    // Resource routes for payroll
    Route::resource('payrolls', PayrollsController::class)->middleware(['role:Developer,HR,Power User']);

    // Resource routes for presences (attendance)
    Route::resource('presences', PresencesController::class)->middleware(['role:Developer,HR,Power User']);
    
    // Resource routes for leave requests
    Route::resource('leave-requests', LeaveRequestController::class)->middleware(['role:Developer,HR,Power User']);
    
    Route::get('leave-requests/confirm/{id}', [LeaveRequestController::class, 'confirm'])->name('leave-requests.confirm')->middleware(['role:Developer,HR,Power User']);
    Route::get('leave-requests/reject/{id}', [LeaveRequestController::class, 'reject'])->name('leave-requests.reject')->middleware(['role:Developer,HR,Power User']);
    
    // Resource routes for inventory categories
    Route::resource('inventory-categories', InventoryCategoryController::class)->middleware(['role:HR,Power User']);
    
    // Resource routes for inventories
    Route::resource('inventories', InventoryController::class)->middleware(['role:HR,Power User']);
    
    // Resource routes for inventory usage logs
    Route::resource('inventory-usage-logs', InventoryUsageLogController::class)->middleware(['role:Developer,HR,Power User']);

    // Resource routes for letters - all authenticated users can create/submit
    Route::resource('letters', LetterController::class)->middleware(['auth']);
    Route::post('letters/{letter}/submit', [LetterController::class, 'submit'])->name('letters.submit')->middleware(['auth']);
    
    // Letter approval actions - only HR and Power User
    Route::post('letters/{letter}/approve', [LetterController::class, 'approve'])->name('letters.approve')->middleware(['role:HR,Power User']);
    Route::post('letters/{letter}/reject', [LetterController::class, 'reject'])->name('letters.reject')->middleware(['role:HR,Power User']);
    Route::post('letters/{letter}/print', [LetterController::class, 'print'])->name('letters.print')->middleware(['role:HR,Power User']);
    Route::get('letters/{letter}/export', [LetterController::class, 'export'])->name('letters.export')->middleware(['role:HR,Power User']);

    // Resource routes for letter templates
    Route::resource('letter-templates', LetterTemplateController::class)->middleware(['role:HR,Power User']);

    // Resource routes for letter configuration
    Route::get('letter-configurations', [LetterConfigurationController::class, 'index'])->name('letter-configurations.index')->middleware(['role:HR,Power User']);
    Route::post('letter-configurations', [LetterConfigurationController::class, 'update'])->name('letter-configurations.update')->middleware(['role:HR,Power User']);

    // Resource routes for letter archives
    Route::resource('letter-archives', LetterArchiveController::class)->only(['index', 'show'])->middleware(['role:Developer,HR,Power User']);
    
    // Digital Signature routes
    Route::get('signatures/{signable}/{id}/pad', [SignatureController::class, 'pad'])->name('signatures.pad');
    Route::post('signatures/{signable}/{id}', [SignatureController::class, 'store'])->name('signatures.store');
    Route::get('signatures/{signable}/{id}/list', [SignatureController::class, 'list'])->name('signatures.list');
    Route::get('signature-logs', [SignatureController::class, 'logs'])->name('signatures.logs');
    Route::post('signatures/{signature}/verify', [SignatureController::class, 'verify'])->name('signatures.verify')->middleware(['role:HR,Power User']);
    Route::get('signatures/{signature}/download', [SignatureController::class, 'download'])->name('signatures.download');
    Route::get('signatures/{signature}/validate', [SignatureController::class, 'validate'])->name('signatures.validate');

    // KPI and Reporting routes
    Route::get('kpi/dashboard', [KPIController::class, 'dashboard'])->name('kpi.dashboard');
    Route::get('kpi/employee/{id}', [KPIController::class, 'show'])->name('kpi.show');
    Route::get('kpi/team', [KPIController::class, 'team'])->name('kpi.team')->middleware(['role:Manager,HR,Power User']);
    Route::get('kpi/department', [KPIController::class, 'department'])->name('kpi.department')->middleware(['role:Manager,HR,Power User']);
    Route::post('kpi/recalculate/{id}', [KPIController::class, 'recalculate'])->name('kpi.recalculate')->middleware(['role:HR,Power User']);
    
    Route::get('reports/monthly-recap', [ReportingController::class, 'monthlyRecap'])->name('reports.monthly-recap')->middleware(['role:Manager,HR,Power User']);
    Route::get('reports/executive', [ReportingController::class, 'executiveDashboard'])->name('reports.executive')->middleware(['role:HR,Power User']);
    Route::get('reports/{id}/export-pdf', [ReportingController::class, 'exportPDF'])->name('reports.export-pdf');
    Route::get('reports/export-csv', [ReportingController::class, 'exportCSV'])->name('reports.export-csv')->middleware(['role:Manager,HR,Power User']);
});

// Bawaan Breeze.
// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';