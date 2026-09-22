<?php

namespace App\Http\Controllers;

use App\Navigation\NavigationItem;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @return array<int|string, array<string, bool|string>>
     */
    protected function withBreadcrumbs(NavigationItem ...$item): array
    {
        return array_map(fn (NavigationItem $item) => $item->toArray(), $item);
    }

    /**
     * @param  string|\BackedEnum|null  $ability
     * @param  array<mixed>  $arguments
     */
    public function authorize($ability, $arguments = []): Response
    {
        $abilityValue = $ability instanceof \BackedEnum ? $ability->value : $ability;
        [$ability, $arguments] = $this->parseAbilityAndArguments($abilityValue, $arguments);

        return app(Gate::class)->authorize($ability, $arguments);
    }
}
