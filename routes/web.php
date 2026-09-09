<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\System\HealthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public web routes
|--------------------------------------------------------------------------
|
| Phase 1A foundation routes. Product/portfolio/lab/services/insights and the
| lead form arrive with their respective domain phases.
|
*/

Route::get('/', HomeController::class)->name('home');

// Liveness/health (returns JSON; used by smoke tests and orchestration).
Route::get('/health', HealthController::class)->name('health');

// Locale preference switch (POST only; enabled locales validated).
Route::post('/locale/{locale}', LocaleController::class)
    ->whereIn('locale', ['en', 'fa'])
    ->name('locale.switch');
