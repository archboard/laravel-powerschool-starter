<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;

class AllowsPasswordLogins
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
    {
        $tenant = Tenant::current();
        if ($tenant?->allow_password_auth || $request->routeIs('login')) {
            return $next($request);
        }

        abort(404);
    }
}
