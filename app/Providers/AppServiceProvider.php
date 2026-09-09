<?php

namespace App\Providers;

use App\Core\Activity\ActivityLogger;
use App\Core\Media\MediaService;
use App\Core\Settings\SettingsService;
use App\Domains\Leads\Policies\LeadPolicy;
use App\Domains\Media\Policies\MediaPolicy;
use App\Domains\Portfolio\Policies\PortfolioProjectPolicy;
use App\Domains\Products\Policies\ProductPolicy;
use App\Domains\Services\Policies\ServicePolicy;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\Facades\Gate;
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
        // RBAC: owner bypasses every check; everyone else is limited to their
        // explicit permissions (see docs/admin.md).
        Gate::before(fn (User $user) => $user->isOwner() ? true : null);

        Gate::policy(\App\Domains\Products\Models\Product::class, ProductPolicy::class);
        Gate::policy(\App\Domains\Portfolio\Models\PortfolioProject::class, PortfolioProjectPolicy::class);
        Gate::policy(\App\Domains\Services\Models\Service::class, ServicePolicy::class);
        Gate::policy(\App\Domains\Leads\Models\Lead::class, LeadPolicy::class);
        Gate::policy(\App\Domains\Media\Models\Media::class, MediaPolicy::class);

        Gate::define('settings.manage', fn (User $user) => $user->hasPermission('settings.manage'));
        Gate::define('activity.view', fn (User $user) => $user->hasPermission('activity.view'));

        // Footer branding/services data is shared by every public page; the
        // query lives in a composer so views stay data-free (architecture rule).
        \Illuminate\Support\Facades\View::composer('components.public.footer', function ($view): void {
            $view->with(
                'footerServices',
                \App\Domains\Services\Models\Service::query()
                    ->published()
                    ->ordered()
                    ->limit(8)
                    ->get(),
            );
        });
    }
}
