<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Uninstalled
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($user = $request->user()) {
            if ($user->cant('install')) {
                abort(404);
            }
        }

        // PowerSchool's plugin registration configures the SIS before the wizard finishes, so it stays open until a user exists
        $tenant = $request->tenant();
        if ($tenant->installed() && $tenant->users()->exists()) {
            abort(404);
        }

        return $next($request);
    }
}
