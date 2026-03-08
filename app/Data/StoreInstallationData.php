<?php

namespace App\Data;

use App\Models\Tenant;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class StoreInstallationData extends Data
{
    public function __construct(
        public string $name,
        public string $domain,
        public ?string $custom_domain,
        /** @var array<string, mixed> */
        public array $sis_config,
    ) {}

    /**
     * @return array<string, array<int, string>>
     */
    public static function rules(): array
    {
        $tenant = Tenant::fromRequestAndFallback(request());

        $rules = [
            'domain' => ['required', Rule::unique('tenants', 'domain')->ignoreModel($tenant)],
            ...($tenant->sis_provider?->getRules() ?? []),
        ];

        if (config('app.cloud')) {
            $rules['custom_domain'] = [
                'nullable',
                Rule::unique('tenants', 'domain')->ignoreModel($tenant),
                Rule::unique('tenants', 'custom_domain')->ignoreModel($tenant),
            ];
        }

        return $rules;
    }
}
