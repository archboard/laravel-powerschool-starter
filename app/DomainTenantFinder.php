<?php

namespace App;

use App\Models\Tenant;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\ClassString;
use Spatie\Multitenancy\Contracts\IsTenant;
use Spatie\Multitenancy\TenantFinder\TenantFinder;

class DomainTenantFinder extends TenantFinder
{
    public function findForRequest(Request $request): ?IsTenant
    {
        $model = app(IsTenant::class);

        return config('app.cloud')
            ? $model::fromRequest($request)
            : $model::first();
    }
}
