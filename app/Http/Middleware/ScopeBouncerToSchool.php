<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Silber\Bouncer\BouncerFacade;
use Symfony\Component\HttpFoundation\Response;

class ScopeBouncerToSchool
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && ($school = $user->school_id)) {
            BouncerFacade::scope()->to($school);

            return $next($request);
        }

        return to_route('select-school');
    }
}
