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
        $host = $request->getHost();

        return $this->getTenantModel()::query()
            ->where('domain', $host)
            ->orWhere('custom_domain', $host)
            ->first();
    }
}
