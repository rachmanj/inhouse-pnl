<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\CoaMappingController;
use App\Http\Controllers\Admin\ProjectSiteController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard/Index');
    })->name('dashboard');

    Route::resource('admin/project-sites', ProjectSiteController::class)
        ->names('admin.project-sites')
        ->except(['show']);

    Route::resource('admin/accounts', AccountController::class)
        ->names('admin.accounts')
        ->except(['show']);

    Route::resource('admin/coa-mappings', CoaMappingController::class)
        ->names('admin.coa-mappings')
        ->except(['show']);
    Route::post('admin/coa-mappings/simulate', [CoaMappingController::class, 'simulate'])
        ->name('admin.coa-mappings.simulate');

    Route::resource('admin/users', UserController::class)
        ->names('admin.users')
        ->except(['show']);
    Route::post('admin/users/{user}/sites', [UserController::class, 'assignSites'])
        ->name('admin.users.assign-sites');

    Route::resource('admin/roles', RoleController::class)
        ->names('admin.roles')
        ->only(['index', 'edit', 'update']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
