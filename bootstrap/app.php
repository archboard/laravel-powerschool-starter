<?php

use App\Http\Middleware\AllowsPasswordLogins;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\Cloud;
use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\HasSchoolSet;
use App\Http\Middleware\Installed;
use App\Http\Middleware\NoDistrictAdmin;
use App\Http\Middleware\ScopeBouncerToSchool;
use App\Http\Middleware\SelfHosted;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\SisConfigured;
use App\Http\Middleware\Uninstalled;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Spatie\Multitenancy\Exceptions\NoCurrentTenant;
use Spatie\Multitenancy\Http\Middleware\EnsureValidTenantSession;
use Spatie\Multitenancy\Http\Middleware\NeedsTenant;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            SetLocale::class,
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->group('tenant', [
            Installed::class,
            NeedsTenant::class,
            EnsureValidTenantSession::class,
        ]);

        $middleware->alias([
            'auth' => Authenticate::class,
            'self_hosted' => SelfHosted::class,
            'cloud' => Cloud::class,
            'allows_pw_auth' => AllowsPasswordLogins::class,
            'sis_configured' => SisConfigured::class,
            'uninstalled' => Uninstalled::class,
            'installed' => Installed::class,
            'has_school' => HasSchoolSet::class,
            'scoped_permissions' => ScopeBouncerToSchool::class,
            'no_admin' => NoDistrictAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (NoCurrentTenant $e) {
            abort(404);
        });

        $exceptions->respond(function (Response $response, Throwable $e, Request $request) {
            if (
                app()->environment('production') &&
                in_array($response->getStatusCode(), [500, 503, 404, 403]) &&
                (! $request->wantsJson() || $request->inertia())
            ) {
                $title = __('Error');

                return inertia('Error', [
                    'status' => $response->getStatusCode(),
                    'title' => $title,
                ])
                    ->withViewData(compact('title'))
                    ->toResponse($request)
                    ->setStatusCode($response->getStatusCode());
            }

            return $response;
        });
    })->create();
