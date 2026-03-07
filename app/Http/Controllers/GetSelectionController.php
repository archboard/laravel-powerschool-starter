<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GetSelectionController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $model): \Illuminate\Http\JsonResponse
    {
        return response()->json(
            $request->user()
                ?->getModelSelection($model) ?? []
        );
    }
}
