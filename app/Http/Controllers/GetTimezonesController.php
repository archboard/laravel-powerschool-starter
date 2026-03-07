<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GetTimezonesController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Support\Collection<int, array{value: string, label: string}>
     */
    public function __invoke(Request $request): \Illuminate\Support\Collection
    {
        /** @var \Illuminate\Support\Collection<string, string> $timezones */
        $timezones = timezones();

        return $timezones
            ->map(fn (string $label, string $key) => [
                'value' => $key,
                'label' => $label,
            ])
            ->values();
    }
}
