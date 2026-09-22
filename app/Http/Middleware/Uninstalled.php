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

        $tenant = $request->tenant();
        if ($tenant->installed()) {
            abort(404);
        }

        return $next($request);
    }
}
