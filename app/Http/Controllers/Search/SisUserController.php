<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class SisUserController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return Collection<int, mixed>
     */
    public function __invoke(Request $request, Tenant $tenant): Collection
    {
        $data = $request->validate([
            'search' => ['required', 'string', 'min:3'],
        ]);

        return $tenant->getSisProvider()
            ?->searchForUser($data['search']) ?? collect();
    }
}
