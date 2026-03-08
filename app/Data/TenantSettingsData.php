<?php

namespace App\Data;

use App\Enums\Sis;
use App\Models\Tenant;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

class TenantSettingsData extends Data
{
    public function __construct(
        #[Max(255)]
        public string $name,
        public ?string $domain,
        public ?Sis $sis_provider,
        public bool $allow_password_auth = false,
        public bool $allow_oidc_login = false,
    ) {}

    /**
     * @return array<string, array<int, string>>
     */
    public static function rules(): array
    {
        $tenant = Tenant::current();

        return [
            'domain' => array_filter([
                'required',
                $tenant ? Rule::unique('tenants', 'domain')->ignoreModel($tenant) : null,
            ]),
        ];
    }

    public static function fromTenant(Tenant $tenant): self
    {
        return new self(
            name: $tenant->name,
            domain: $tenant->domain ?? null,
            sis_provider: $tenant->sis_provider,
            allow_password_auth: $tenant->allow_password_auth,
            allow_oidc_login: $tenant->allow_oidc_login,
        );
    }
}
