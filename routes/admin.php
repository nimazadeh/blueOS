<?php

use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\Leads\LeadController;
use App\Http\Controllers\Admin\Media\MediaController;
use App\Http\Controllers\Admin\Portfolio\ProjectController;
use App\Http\Controllers\Admin\Products\ProductController;
use App\Http\Controllers\Admin\Services\ServiceController;
use App\Http\Controllers\Admin\SettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Blue Control routes (/admin)
|--------------------------------------------------------------------------
|
| Authentication + RBAC-protected management modules. Every mutating action
| is either authorized by a FormRequest or an explicit `authorize` call in
| the controller (docs/admin.md).
|
*/

Route::name('admin.')->group(function () {
    Route::middleware('guest.admin')->group(function () {
        Route::get('/admin/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/admin/login', [LoginController::class, 'login'])
            ->middleware('throttle:5,1')
            ->name('login.submit');
    });

    Route::middleware('auth:admin')->group(function () {
        Route::post('/admin/logout', [LoginController::class, 'logout'])->name('logout');
        Route::get('/admin', DashboardController::class)->name('dashboard');

        // ── Products ───────────────────────────────────────────────────────
        Route::prefix('admin/products')->name('products.')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::get('/create', [ProductController::class, 'create'])->name('create');
            Route::post('/', [ProductController::class, 'store'])->name('store');
            Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
            Route::put('/{product}', [ProductController::class, 'update'])->name('update');
            Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
            Route::post('/{product}/publish', [ProductController::class, 'publish'])->name('publish');
            Route::post('/{product}/unpublish', [ProductController::class, 'unpublish'])->name('unpublish');
        });

        // ── Portfolio ──────────────────────────────────────────────────────
        Route::prefix('admin/portfolio')->name('portfolio.')->group(function () {
            Route::get('/', [ProjectController::class, 'index'])->name('index');
            Route::get('/create', [ProjectController::class, 'create'])->name('create');
            Route::post('/', [ProjectController::class, 'store'])->name('store');
            Route::get('/{project}/edit', [ProjectController::class, 'edit'])->name('edit');
            Route::put('/{project}', [ProjectController::class, 'update'])->name('update');
            Route::delete('/{project}', [ProjectController::class, 'destroy'])->name('destroy');
            Route::post('/{project}/publish', [ProjectController::class, 'publish'])->name('publish');
            Route::post('/{project}/unpublish', [ProjectController::class, 'unpublish'])->name('unpublish');
        });

        // ── Services ───────────────────────────────────────────────────────
        Route::prefix('admin/services')->name('services.')->group(function () {
            Route::get('/', [ServiceController::class, 'index'])->name('index');
            Route::get('/create', [ServiceController::class, 'create'])->name('create');
            Route::post('/', [ServiceController::class, 'store'])->name('store');
            Route::get('/{service}/edit', [ServiceController::class, 'edit'])->name('edit');
            Route::put('/{service}', [ServiceController::class, 'update'])->name('update');
            Route::delete('/{service}', [ServiceController::class, 'destroy'])->name('destroy');
            Route::post('/{service}/publish', [ServiceController::class, 'publish'])->name('publish');
            Route::post('/{service}/unpublish', [ServiceController::class, 'unpublish'])->name('unpublish');
        });

        // ── Leads ──────────────────────────────────────────────────────────
        Route::prefix('admin/leads')->name('leads.')->group(function () {
            Route::get('/', [LeadController::class, 'index'])->name('index');
            Route::get('/{lead}', [LeadController::class, 'show'])->name('show');
            Route::post('/{lead}/status', [LeadController::class, 'updateStatus'])->name('status');
            Route::post('/{lead}/notes', [LeadController::class, 'storeNote'])->name('notes');
        });

        // ── Media ──────────────────────────────────────────────────────────
        Route::prefix('admin/media')->name('media.')->group(function () {
            Route::get('/', [MediaController::class, 'index'])->name('index');
            Route::post('/', [MediaController::class, 'store'])->name('store');
            Route::patch('/{media}', [MediaController::class, 'update'])->name('update');
            Route::delete('/{media}', [MediaController::class, 'destroy'])->name('destroy');
        });

        // ── Settings & activity ────────────────────────────────────────────
        Route::get('/admin/settings', [SettingsController::class, 'index'])->name('settings');
        Route::patch('/admin/settings', [SettingsController::class, 'update'])->name('settings.update');

        Route::get('/admin/activity', ActivityController::class)->name('activity');
    });
});
