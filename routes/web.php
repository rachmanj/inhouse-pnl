<?php

use App\Http\Controllers\Reports\ApprovalStepController;
use App\Http\Controllers\Reports\ReportPackageController;
use App\Http\Controllers\Intelligence\InsightsController;
use App\Http\Controllers\Admin\ReportPeriodController;
use App\Enums\ReportPeriodStatus;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\CoaMappingController;
use App\Http\Controllers\Admin\ProjectSiteController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Import\ImportBatchController;
use App\Http\Controllers\Journals\JournalController;
use App\Http\Controllers\PettyCash\PettyCashExpenseController;
use App\Http\Controllers\PettyCash\PettyCashFundController;
use App\Http\Controllers\Pnl\ConsolidatedPnlController;
use App\Http\Controllers\Pnl\SitePnlController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Tax\TaxFilingController;
use App\Http\Controllers\Tax\TaxPaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/pnl/sites/{projectSite:code}', [SitePnlController::class, 'show'])->name('pnl.site.show');
    Route::get('/pnl/consolidated', [ConsolidatedPnlController::class, 'show'])->name('pnl.consolidated.show');

    Route::resource('imports', ImportBatchController::class)->except(['edit', 'update']);
    Route::post('/imports/{importBatch}/resolve-mapping', [ImportBatchController::class, 'resolveMapping'])->name('imports.resolve-mapping');
    Route::post('/imports/{importBatch}/confirm', [ImportBatchController::class, 'confirm'])->name('imports.confirm');

    Route::resource('journals', JournalController::class);
    Route::post('/journals/{journal}/approve', [JournalController::class, 'approve'])->name('journals.approve');
    Route::post('/journals/{journal}/reject', [JournalController::class, 'reject'])->name('journals.reject');

    Route::get('/petty-cash', [PettyCashFundController::class, 'index'])->name('petty-cash.index');
    Route::get('/petty-cash/{pettyCashFund}/expenses', [PettyCashExpenseController::class, 'index'])->name('petty-cash.expenses.index');
    Route::post('/petty-cash/{pettyCashFund}/expenses', [PettyCashExpenseController::class, 'store'])->name('petty-cash.expenses.store');
    Route::delete('/petty-cash/expenses/{pettyCashExpense}', [PettyCashExpenseController::class, 'destroy'])->name('petty-cash.expenses.destroy');

    Route::get('/tax', [TaxFilingController::class, 'index'])->name('tax.index');
    Route::get('/tax/calendar', [TaxFilingController::class, 'calendar'])->name('tax.calendar');
    Route::get('/tax/{taxFiling}/payments', [TaxPaymentController::class, 'index'])->name('tax.payments.index');
    Route::post('/tax/{taxFiling}/payments', [TaxPaymentController::class, 'store'])->name('tax.payments.store');

    Route::get('/reports', [ReportPackageController::class, 'index'])->name('reports.index');
    Route::get('/reports/create', [ReportPackageController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportPackageController::class, 'store'])->name('reports.store');
    Route::get('/reports/{reportPackage}', [ReportPackageController::class, 'show'])->name('reports.show');
    Route::post('/reports/{reportPackage}/generate', [ReportPackageController::class, 'generate'])->name('reports.generate');
    Route::post('/reports/{reportPackage}/deliver', [ReportPackageController::class, 'deliver'])->name('reports.deliver');
    Route::post('/reports/{reportPackage}/approval-steps/{approvalStep}/approve', [ApprovalStepController::class, 'approve'])->name('reports.approval.approve');
    Route::post('/reports/{reportPackage}/approval-steps/{approvalStep}/reject', [ApprovalStepController::class, 'reject'])->name('reports.approval.reject');

    Route::get('/intelligence/insights', [InsightsController::class, 'index'])->name('intelligence.insights');

    Route::get('/admin/report-periods', [ReportPeriodController::class, 'index'])->name('admin.periods.index');
    Route::patch('/admin/report-periods/{reportPeriod}/status', [ReportPeriodController::class, 'transition'])->name('admin.periods.transition');

    Route::resource('admin/project-sites', ProjectSiteController::class)->names('admin.project-sites')->except(['show']);
    Route::resource('admin/accounts', AccountController::class)->names('admin.accounts')->except(['show']);
    Route::resource('admin/coa-mappings', CoaMappingController::class)->names('admin.coa-mappings')->except(['show']);
    Route::post('admin/coa-mappings/simulate', [CoaMappingController::class, 'simulate'])->name('admin.coa-mappings.simulate');
    Route::resource('admin/users', UserController::class)->names('admin.users')->except(['show']);
    Route::post('admin/users/{user}/sites', [UserController::class, 'assignSites'])->name('admin.users.assign-sites');
    Route::resource('admin/roles', RoleController::class)->names('admin.roles')->only(['index', 'edit', 'update']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
