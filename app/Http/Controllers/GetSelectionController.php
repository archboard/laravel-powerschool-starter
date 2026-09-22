<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetSelectionController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $model): JsonResponse
    {
        return response()->json(
            $request->user()
                ?->getModelSelection($model) ?? []
        );
    }
}
