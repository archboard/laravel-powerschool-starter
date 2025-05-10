<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClassLinkOAuthController extends Controller
{
    public function authenticate()
    {
        //
    }

    public function login(Request $request)
    {
        dump($request->all());

        return response();
    }
}
