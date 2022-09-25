<?php

namespace App\Models;

use App\Enums\Role;
use App\Enums\Sis;
use App\Fields\FormField;
use App\Fields\FormFieldCollection;
use App\Rules\ValidLicense;
use App\SisProviders\SisProvider;
use GrantHolle\Http\Resources\Traits\HasResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Silber\Bouncer\BouncerFacade;
use Spatie\Multitenancy\Models\Tenant as TenantBase;

/**
 * @mixin IdeHelperTenant
 */
class Tenant extends TenantBase
{
    use HasFactory;
    use HasResource;

    protected $guarded = [];
    protected $casts = [
        'sis_provider' => Sis::class,
        'sis_config' => 'json',
        'allow_password_auth' => 'boolean',
    ];

    protected static function booted()
    {
        static::created(function (Tenant $tenant) {
            // Seed the roles and abilities for this tenant scope
            // BouncerFacade::scope()->to($tenant->id);
            BouncerFacade::allow(Role::DISTRICT_ADMIN->value)->everything();

            // Additional seeding as the project needs
        });
    }

    public function domain(): Attribute
    {
        return Attribute::get(fn ($value) => $value ?? request()->host());
    }

    public function sisProvider(): Attribute
    {
        return Attribute::get(fn ($value) => $value ? $this->castAttribute('sis_provider', $value) : Sis::PS);
    }

    public function sisConfig(): Attribute
    {
        return Attribute::get(fn ($value) => $value ? $this->castAttribute('sis_config', $value) : []);
    }

    public function schools(): HasMany
    {
        return $this->hasMany(School::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public static function fromRequest(Request $request): ?Tenant
    {
        return static::getByHost($request->getHost());
    }

    public static function fromRequestAndFallback(Request $request): Tenant
    {
        return static::fromRequest($request) ?? new static;
    }

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public static function getByHost(string $host): ?Tenant
    {
        return static::query()
            ->where('domain', $host)
            ->orWhere(function (Builder $builder) use ($host) {
                $builder->whereNotNull('custom_domain')
                    ->where('custom_domain', $host);
            })
            ->first();
    }

    public function installed(): bool
    {
        ray($this->getSisProvider());
        return $this->getSisProvider()?->configured() ?? false;
    }

    public function getSisProvider(): ?SisProvider
    {
        return $this->sis_provider?->getProvider($this);
    }

    public function getSchoolFromSisId($sisId): School
    {
        if ($sisId instanceof School) {
            return $sisId;
        }

        /** @var School $school */
        $school = $this->schools()
            ->where('sis_id', $sisId)
            ->firstOrFail();
        return $school;
    }

    public function toArray()
    {
        return [
            'name' => $this->name,
            'allow_password_auth' => $this->allow_password_auth,
        ];
    }

    public function getSisConfigKey(?string $key, mixed $defaultValue = null): mixed
    {
        return Arr::get(
            $this->sis_config,
            Str::replace('sis_config.', '', $key),
            $defaultValue
        );
    }

    public function setSisConfigKey(?string $key, mixed $value): static
    {
        $this->sis_config = [
            ...$this->sis_config,
            Str::replace('sis_config.', '', $key) => $value,
        ];

        return $this;
    }

    public function getInstallationFieldValue(?string $key): mixed
    {
        if (Str::startsWith($key, 'sis_config')) {
            return $this->getSisConfigKey($key);
        }

        return $this->getAttribute($key);
    }

    public function setInstallationFieldValue(?string $key, mixed $value): static
    {
        if (Str::startsWith($key, 'sis_config')) {
            return $this->setSisConfigKey($key, $value);
        }

        $this->setAttribute($key, $value);

        return $this;
    }

    public function getInstallationFields(): FormFieldCollection
    {
        return FormFieldCollection::make([
                'license' => FormField::make(__('License'))
                    ->rules(['required', 'uuid', new ValidLicense()]),
                'name' => FormField::make(__('Tenant name'))
                    ->rules(['required', 'string', 'max:255']),
                'domain' => FormField::make(__('Domain'))
                    ->disabled(config('app.cloud'))
                    ->rules([
                        'required',
                        Rule::unique('tenants', 'domain')->ignoreModel($this),
                    ]),
                ...(config('app.cloud')
                    ? ['custom_domain' => FormField::make(__('Custom domain'))
                        ->rules([
                            'nullable',
                            Rule::unique('tenants', 'domain')->ignoreModel($this),
                            Rule::unique('tenants', 'custom_domain')->ignoreModel($this),
                        ])]
                    : []),
                'email' => FormField::make(__('Email'))
                    ->help(__('This is the email address for a system admin and should match your email that is used in your SIS.'))
                    ->rules([
                        'required',
                        'email',
                    ]),
                ...$this->sis_provider?->getConfigFields() ?? collect()
            ])
            ->merge($this->sis_provider?->getConfigFields() ?? collect())
            ->map(fn (FormField $field, string $key) => $field
                ->withValue($this->getInstallationFieldValue($key))
                ->keyedBy($key)
            );
    }
}
