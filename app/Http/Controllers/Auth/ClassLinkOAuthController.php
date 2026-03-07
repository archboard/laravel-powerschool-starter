<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClassLinkOAuthController extends Controller
{
    public function authenticate(): void
    {
        //
    }

    public function login(Request $request): \Illuminate\Http\Response
    {
        dump($request->all());

        return response('', 200);
    }
}
