<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClassLinkOAuthController extends Controller
{
    public function authenticate(): void
    {
        //
    }

    public function login(Request $request): \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
    {
        dump($request->all());

        return response('', 200);
    }
}
