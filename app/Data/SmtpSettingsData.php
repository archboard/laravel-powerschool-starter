<?php

namespace App\Data;

use App\Models\Tenant;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class SmtpSettingsData extends Data
{
    public function __construct(
        public ?string $host,
        public ?int $port,
        public ?string $username,
        public ?string $password,
        public ?string $from_name,
        public ?string $from_address,
        public ?string $encryption,
    ) {}

    /**
     * @return array<string, array<int, string>>
     */
    public static function rules(): array
    {
        return [
            'host' => ['required'],
            'port' => ['required', 'numeric'],
            'from_name' => ['required'],
            'from_address' => ['required', 'email'],
            'encryption' => ['nullable', Rule::in(['tls', 'ssl'])],
        ];
    }

    public static function fromTenant(Tenant $tenant): self
    {
        return new self(
            host: $tenant->smtp_config->get('host'),
            port: ($port = $tenant->smtp_config->get('port')) !== null ? (int) $port : null,
            username: $tenant->smtp_config->get('username'),
            password: $tenant->smtp_config->get('password'),
            from_name: $tenant->smtp_config->get('from_name'),
            from_address: $tenant->smtp_config->get('from_address'),
            encryption: $tenant->smtp_config->get('encryption'),
        );
    }
}
