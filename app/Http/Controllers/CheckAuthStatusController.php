<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckAuthStatusController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        return response()->json();
    }
}
