<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Spatie\Multitenancy\Models\Concerns\UsesTenantModel;
use Spatie\Multitenancy\Models\Tenant;
use Spatie\Multitenancy\TenantFinder\TenantFinder;

class DomainTenantFinder extends TenantFinder
{
    use UsesTenantModel;

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public function findForRequest(Request $request): ?Tenant
    {
        return $this->getTenantModel()::query()
            ->whereHas('domains', function (Builder $builder) use ($request) {
                $builder->where('hostname', $request->getHost());
            })
            ->first();
    }
}
