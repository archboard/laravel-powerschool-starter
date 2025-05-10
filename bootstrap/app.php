<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->group('tenant', [
            \App\Http\Middleware\Installed::class,
            \Spatie\Multitenancy\Http\Middleware\NeedsTenant::class,
            \Spatie\Multitenancy\Http\Middleware\EnsureValidTenantSession::class,
        ]);

        $middleware->alias([
            'self_hosted' => \App\Http\Middleware\SelfHosted::class,
            'cloud' => \App\Http\Middleware\Cloud::class,
            'allows_pw_auth' => \App\Http\Middleware\AllowsPasswordLogins::class,
            'sis_configured' => \App\Http\Middleware\SisConfigured::class,
            'uninstalled' => \App\Http\Middleware\Uninstalled::class,
            'installed' => \App\Http\Middleware\Installed::class,
            'has_school' => \App\Http\Middleware\HasSchoolSet::class,
            'scoped_permissions' => \App\Http\Middleware\ScopeBouncerToSchool::class,
            'no_admin' => \App\Http\Middleware\NoDistrictAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
