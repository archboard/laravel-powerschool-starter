<?php

namespace App\Enums\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait HasOptions
{
    public static function options(): array
    {
        return array_reduce(
            static::cases(),
            function (array $carry, $enum) {
                $carry[$enum->value] = $enum->label();

                return $carry;
            },
            []
        );
    }

    public static function collect(): Collection
    {
        return collect(static::cases());
    }

    public static function selectOptions(): array
    {
        return array_map(fn ($sis) => [
            'label' => $sis->label(),
            'value' => $sis->value,
        ], static::cases());
    }

    public function label(): string
    {
        return Str::of($this->value)
            ->replace(['-', '_'], ' ')
            ->ucfirst();
    }
}
