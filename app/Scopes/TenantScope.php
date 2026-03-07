<?php

namespace App\Scopes;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * @template TModel of Model
 */
class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $tenant = Tenant::current();

        if ($tenant) {
            $builder->where($model->getTable().'.tenant_id', $tenant->id);
        }
    }

    /**
     * @param  Builder<TModel>  $builder
     */
    public function extend(Builder $builder): void
    {
        $this->addWithoutTenant($builder);
    }

    /**
     * @param  Builder<TModel>  $builder
     */
    protected function addWithoutTenant(Builder $builder): void
    {
        $builder->macro('withoutTenant', function (Builder $builder) {
            /** @var Scope $scope */
            $scope = $this;

            return $builder->withoutGlobalScope($scope);
        });
    }
}
