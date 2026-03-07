<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Uninstalled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     */
    public function handle(Request $request, Closure $next): \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
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
