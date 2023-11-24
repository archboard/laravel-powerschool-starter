<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Resources\SchoolResource;
use Illuminate\Http\Request;

class SchoolSettingsController extends Controller
{
    public function edit(Request $request)
    {
        $school = $request->school();

        return inertia('settings/School', [
            'title' => __('School Settings'),
            'school' => new SchoolResource($school),
        ]);
    }
}
