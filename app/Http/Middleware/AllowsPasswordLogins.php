<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AllowsPasswordLogins
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        $tenant = Tenant::current();
        if ($tenant?->allow_password_auth || $request->routeIs('login')) {
            return $next($request);
        }

        abort(404);
    }
}
