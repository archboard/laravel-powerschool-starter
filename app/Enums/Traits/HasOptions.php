<?php

namespace App\Enums\Traits;

use Illuminate\Support\Str;

trait HasOptions
{
    /**
     * @return array<string, string>
     */
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

    /**
     * @return \Illuminate\Support\Collection<int, static>
     */
    public static function collect(): \Illuminate\Support\Collection
    {
        /** @var \Illuminate\Support\Collection<int, static> */
        return collect(static::cases());
    }

    /**
     * @return array<int, array{label: string, value: mixed}>
     */
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
