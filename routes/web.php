<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\Public\LeadSubmissionController;
use App\Http\Controllers\Public\MediaController;
use App\Http\Controllers\Public\PortfolioController;
use App\Http\Controllers\Public\ProductController;
use App\Http\Controllers\Public\ServiceController;
use App\Http\Controllers\System\HealthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public web routes
|--------------------------------------------------------------------------
|
| Public pages read the domains through controllers → actions — never
| directly from Blade. All content routes are restricted to published
| status in the controller queries.
|
*/

Route::get('/', HomeController::class)->name('home');

// Liveness/health (returns JSON; used by smoke tests and orchestration).
Route::get('/health', HealthController::class)->name('health');

// Locale preference switch (POST only; enabled locales validated).
Route::post('/locale/{locale}', LocaleController::class)
    ->whereIn('locale', ['en', 'fa'])
    ->name('locale.switch');

// Lead generation (public intake, throttled — see docs/domains.md).
Route::post('/leads', LeadSubmissionController::class)
    ->middleware('throttle:5,1')
    ->name('leads.store');

// Domain presentation.
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio.index');
Route::get('/portfolio/{slug}', [PortfolioController::class, 'show'])->name('portfolio.show');

Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');

// Media delivery (published entities only; see controller).
Route::get('/media/{media}', MediaController::class)->name('media.show');
