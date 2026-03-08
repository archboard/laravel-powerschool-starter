<?php

namespace App\Data;

use App\Models\Tenant;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class InstallationValuesData extends Data
{
    public function __construct(
        public ?string $name,
        public ?string $domain,
        public ?string $custom_domain,
        /** @var array<string, mixed> */
        public array $sis_config,
        /** @var DataCollection<int, SisConfigFieldData> */
        public DataCollection $sis_config_fields,
    ) {}

    public static function fromTenant(Tenant $tenant): self
    {
        $fields = $tenant->sis_provider?->getConfigFieldDefinitions() ?? [];

        $config = array_combine(
            array_column($fields, 'key'),
            array_map(fn (SisConfigFieldData $f) => $tenant->sis_config->get($f->key), $fields),
        ) ?: [];

        return new self(
            name: $tenant->name,
            domain: $tenant->domain,
            custom_domain: $tenant->custom_domain,
            sis_config: $config,
            sis_config_fields: SisConfigFieldData::collect($fields, DataCollection::class),
        );
    }
}
