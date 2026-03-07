<?php

namespace App\Data;

use App\Enums\SisConfigFieldType;
use Spatie\LaravelData\Data;

class SisConfigFieldData extends Data
{
    public function __construct(
        public string $key,
        public string $label,
        public SisConfigFieldType $type,
        public bool $required,
    ) {}
}
