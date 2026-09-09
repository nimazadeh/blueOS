<?php

namespace App\Providers;

use App\Core\Activity\ActivityLogger;
use App\Core\Media\MediaService;
use App\Core\Settings\SettingsService;
use App\Models\Settings;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // SettingsService is a singleton: it caches and must keep one view
        // of the settings collection per request lifecycle.
        $this->app->singleton(SettingsService::class, function ($app) {
            return new SettingsService(
                $app->make(Cache::class),
                new Settings(),
            );
        });

        $this->app->singleton(MediaService::class, fn ($app) => new MediaService(
            disk: $app['config']->get('media.disk', 'media'),
        ));

        $this->app->singleton(ActivityLogger::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
