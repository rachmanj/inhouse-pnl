<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\CoaMappingController;
use App\Http\Controllers\Admin\ProjectSiteController;
use App\Http\Controllers\Admin\ReportPeriodController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Import\ImportBatchController;
use App\Http\Controllers\Intelligence\InsightsController;
use App\Http\Controllers\Journals\JournalController;
use App\Http\Controllers\PettyCash\PettyCashExpenseController;
use App\Http\Controllers\PettyCash\PettyCashFundController;
use App\Http\Controllers\Pnl\ConsolidatedPnlController;
use App\Http\Controllers\Pnl\SitePnlController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Reports\ApprovalStepController;
use App\Http\Controllers\Reports\ReportPackageController;
use App\Http\Controllers\Tax\TaxFilingController;
use App\Http\Controllers\Tax\TaxPaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/pnl/sites/{projectSite:code}', [SitePnlController::class, 'show'])
        ->name('pnl.site.show')
        ->middleware('permission:pnl.view-own-site|pnl.view-all-sites');

    Route::get('/pnl/consolidated', [ConsolidatedPnlController::class, 'show'])
        ->name('pnl.consolidated.show')
        ->middleware('permission:pnl.view-all-sites');

    Route::get('/imports', [ImportBatchController::class, 'index'])->name('imports.index')->middleware('permission:imports.manage');
    Route::get('/imports/create', [ImportBatchController::class, 'create'])->name('imports.create')->middleware('permission:imports.create');
    Route::post('/imports', [ImportBatchController::class, 'store'])->name('imports.store')->middleware('permission:imports.create');
    Route::get('/imports/{importBatch}', [ImportBatchController::class, 'show'])->name('imports.show')->middleware('permission:imports.manage');
    Route::post('/imports/{importBatch}/resolve-mapping', [ImportBatchController::class, 'resolveMapping'])->name('imports.resolve-mapping')->middleware('permission:imports.manage');
    Route::post('/imports/{importBatch}/confirm', [ImportBatchController::class, 'confirm'])->name('imports.confirm')->middleware('permission:imports.manage');
    Route::delete('/imports/{importBatch}', [ImportBatchController::class, 'destroy'])->name('imports.destroy')->middleware('permission:imports.manage');

    Route::get('/journals', [JournalController::class, 'index'])->name('journals.index')->middleware('permission:journals.manage');
    Route::get('/journals/create', [JournalController::class, 'create'])->name('journals.create')->middleware('permission:journals.manage');
    Route::post('/journals', [JournalController::class, 'store'])->name('journals.store')->middleware('permission:journals.manage');
    Route::get('/journals/{journal}', [JournalController::class, 'show'])->name('journals.show')->middleware('permission:journals.manage');
    Route::patch('/journals/{journal}', [JournalController::class, 'update'])->name('journals.update')->middleware('permission:journals.manage');
    Route::post('/journals/{journal}/approve', [JournalController::class, 'approve'])->name('journals.approve')->middleware('permission:journals.approve');
    Route::post('/journals/{journal}/reject', [JournalController::class, 'reject'])->name('journals.reject')->middleware('permission:journals.approve');

    Route::get('/petty-cash', [PettyCashFundController::class, 'index'])->name('petty-cash.index')->middleware('permission:pettycash.manage');
    Route::get('/petty-cash/{pettyCashFund}/expenses', [PettyCashExpenseController::class, 'index'])->name('petty-cash.expenses.index')->middleware('permission:pettycash.manage');
    Route::post('/petty-cash/{pettyCashFund}/expenses', [PettyCashExpenseController::class, 'store'])->name('petty-cash.expenses.store')->middleware('permission:pettycash.manage');
    Route::delete('/petty-cash/expenses/{pettyCashExpense}', [PettyCashExpenseController::class, 'destroy'])->name('petty-cash.expenses.destroy')->middleware('permission:pettycash.manage');

    Route::get('/tax', [TaxFilingController::class, 'index'])->name('tax.index')->middleware('permission:tax.manage');
    Route::get('/tax/calendar', [TaxFilingController::class, 'calendar'])->name('tax.calendar')->middleware('permission:tax.manage');
    Route::get('/tax/{taxFiling}/payments', [TaxPaymentController::class, 'index'])->name('tax.payments.index')->middleware('permission:tax.manage');
    Route::post('/tax/{taxFiling}/payments', [TaxPaymentController::class, 'store'])->name('tax.payments.store')->middleware('permission:tax.manage');

    Route::get('/reports', [ReportPackageController::class, 'index'])->name('reports.index')->middleware('permission:reports.generate');
    Route::get('/reports/create', [ReportPackageController::class, 'create'])->name('reports.create')->middleware('permission:reports.generate');
    Route::post('/reports', [ReportPackageController::class, 'store'])->name('reports.store')->middleware('permission:reports.generate');
    Route::get('/reports/{reportPackage}', [ReportPackageController::class, 'show'])->name('reports.show')->middleware('permission:reports.generate');
    Route::post('/reports/{reportPackage}/generate', [ReportPackageController::class, 'generate'])->name('reports.generate')->middleware('permission:reports.generate');
    Route::post('/reports/{reportPackage}/deliver', [ReportPackageController::class, 'deliver'])->name('reports.deliver')->middleware('permission:reports.deliver');
    Route::post('/reports/{reportPackage}/approval-steps/{approvalStep}/approve', [ApprovalStepController::class, 'approve'])->name('reports.approval.approve')->middleware('permission:reports.approve');
    Route::post('/reports/{reportPackage}/approval-steps/{approvalStep}/reject', [ApprovalStepController::class, 'reject'])->name('reports.approval.reject')->middleware('permission:reports.approve');

    Route::get('/intelligence/insights', [InsightsController::class, 'index'])->name('intelligence.insights');

    Route::get('/admin/report-periods', [ReportPeriodController::class, 'index'])->name('admin.periods.index')->middleware('permission:periods.manage');
    Route::patch('/admin/report-periods/{reportPeriod}/status', [ReportPeriodController::class, 'transition'])->name('admin.periods.transition')->middleware('permission:periods.manage|periods.lock');

    Route::resource('admin/project-sites', ProjectSiteController::class)->names('admin.project-sites')->except(['show'])->middleware('permission:sites.manage');
    Route::resource('admin/accounts', AccountController::class)->names('admin.accounts')->except(['show'])->middleware('permission:accounts.manage');
    Route::resource('admin/coa-mappings', CoaMappingController::class)->names('admin.coa-mappings')->except(['show'])->middleware('permission:coa-mappings.manage');
    Route::post('admin/coa-mappings/simulate', [CoaMappingController::class, 'simulate'])->name('admin.coa-mappings.simulate')->middleware('permission:coa-mappings.manage');
    Route::resource('admin/users', UserController::class)->names('admin.users')->except(['show'])->middleware('permission:users.manage');
    Route::post('admin/users/{user}/sites', [UserController::class, 'assignSites'])->name('admin.users.assign-sites')->middleware('permission:users.manage');
    Route::resource('admin/roles', RoleController::class)->names('admin.roles')->only(['index', 'edit', 'update'])->middleware('permission:roles.manage');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
