<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Blue Control routes (/admin)
|--------------------------------------------------------------------------
|
| Authentication foundation only: login/logout and a protected dashboard
| placeholder. CRUD module routes arrive with the Blue Control phase.
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
    });
});
