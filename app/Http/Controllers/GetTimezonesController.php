<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class GetTimezonesController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return Collection<int, array{value: string, label: string}>
     */
    public function __invoke(Request $request): Collection
    {
        /** @var Collection<string, string> $timezones */
        $timezones = timezones();

        return $timezones
            ->map(fn (string $label, string $key) => [
                'value' => $key,
                'label' => $label,
            ])
            ->values();
    }
}
