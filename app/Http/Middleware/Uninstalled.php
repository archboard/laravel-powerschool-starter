<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Uninstalled
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse
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
