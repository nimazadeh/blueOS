<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        admin: __DIR__.'/../routes/admin.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Locale resolution (session → header → default) runs for every web
        // AND admin request so layout direction/language are always correct.
        $middleware->web(append: [
            App\Http\Middleware\SetLocale::class,
        ]);

        $middleware->appendToGroup('admin', [
            App\Http\Middleware\SetLocale::class,
        ]);

        $middleware->alias([
            'guest.admin' => App\Http\Middleware\RedirectIfAdminAuthenticated::class,
        ]);

        // Unauthenticated accesses to `auth:admin` routes land on Blue Control
        // login (not the framework default `login` route name).
        $middleware->redirectGuestsTo(fn () => route('admin.login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Branded error pages (resources/views/errors/*) render by default;
        // stack traces stay out of responses in production.
    })->create();
