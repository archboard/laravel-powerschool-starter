<?php

namespace App\Traits;

use App\Services\Filters\BaseFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

trait HasFilters
{
    /**
     * @param  Builder<Model>  $builder
     * @param  Collection<string, mixed>|array<string, mixed>  $data
     * @return Builder<Model>
     */
    public function scopeFilter(Builder $builder, Collection|array $data): Builder
    {
        $activeFilters = $this->getActiveFilters($data);
        $request = Request::create('/', 'GET', [
            'filter' => collect($activeFilters)->mapWithKeys(fn (BaseFilter $filter) => [$filter->key => $filter->currentValue])->toArray(),
        ]);

        $allowedFilters = collect($activeFilters)
            ->map(fn (BaseFilter $filter) => AllowedFilter::custom($filter->key, $filter))
            ->values()
            ->toArray();

        QueryBuilder::for($builder, $request)->allowedFilters($allowedFilters);

        return $builder;
    }

    /**
     * @param  Collection<string, mixed>|array<string, mixed>  $data
     * @return array<int, BaseFilter>
     */
    public function getActiveFilters(Collection|array $data): array
    {
        $filters = $this->filtersByKey();

        return collect($data)
            ->filter(function ($set, $key) use ($filters) {
                if (is_array($set)) {
                    return isset($set['key'])
                        && $filters->has($set['key'])
                        && isset($set['value']);
                }

                return $filters->has($key);
            })
            ->map(function ($set, $key) use ($filters) {
                if (! is_array($set)) {
                    $set = ['key' => $key, 'value' => $set];
                }

                /** @var BaseFilter $base */
                $base = $filters->get($set['key']);
                $filter = clone $base;
                $filter
                    ->when(
                        isset($set['operator']),
                        fn (BaseFilter $filter) => $filter->withOperator($set['operator'])
                    )
                    ->withValue($set['value']);

                return $filter;
            })
            ->toArray();
    }

    /**
     * @return array<int, BaseFilter>
     */
    public function filters(): array
    {
        return [];
    }

    /**
     * @param  Collection<string, mixed>|array<string, mixed>  $data
     */
    public function activeFiltersToArray(Collection|array $data): object
    {
        return (object) collect($this->getActiveFilters($data))
            ->filter(fn (BaseFilter $filter) => $filter->showAsAvailable)
            ->map(fn (BaseFilter $filter) => $filter->toFilterArray())
            ->toArray();
    }

    /**
     * @return array<int, mixed>
     */
    public function availableFiltersToArray(): array
    {
        return collect($this->filters())
            ->filter(fn (BaseFilter $filter) => $filter->showAsAvailable)
            ->map(fn (BaseFilter $filter) => $filter->toArray())
            ->values()
            ->toArray();
    }

    /**
     * @return Collection<string, BaseFilter>
     */
    public function filtersByKey(): Collection
    {
        return collect($this->filters())
            ->keyBy(fn (BaseFilter $filter) => $filter->key);
    }
}
