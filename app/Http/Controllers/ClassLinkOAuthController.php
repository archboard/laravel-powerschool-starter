<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ClassLinkOAuthController extends Controller
{
    public function authenticate(): void
    {
        //
    }

    public function login(Request $request): Response|JsonResponse
    {
        dump($request->all());

        return response('', 200);
    }
}
