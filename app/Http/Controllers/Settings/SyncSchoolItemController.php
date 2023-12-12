<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SyncSchoolItemController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $item)
    {
        $school = $request->school();
        $method = 'sync'.ucfirst($item);

        if (method_exists($school, $method)) {
            $school->$method();
            session()->flash('success', __('Synced successfully'));
        }

        return back();
    }
}
