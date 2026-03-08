<?php

namespace App\Models;

use App\Enums\Sis;
use App\SisProviders\SisProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Multitenancy\Models\Tenant as TenantBase;

/**
 * @property int $id
 * @property string $name
 * @property Collection<string, mixed> $sis_config
 * @property string|null $domain
 * @property string|null $custom_domain
 * @property bool $allow_password_auth
 * @property bool $allow_oidc_login
 * @property string|null $subscription_started_at
 * @property string|null $subscription_expires_at
 * @property string|null $license
 * @property string|null $timezone
 * @property Sis|null $sis_provider
 * @property Collection<string, mixed> $smtp_config
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Course> $courses
 * @property-read int|null $courses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\School> $schools
 * @property-read int|null $schools_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Section> $sections
 * @property-read int|null $sections_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Student> $students
 * @property-read int|null $students_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 *
 * @method static \Spatie\Multitenancy\TenantCollection<int, static> all($columns = ['*'])
 * @method static \Database\Factories\TenantFactory factory($count = null, $state = [])
 * @method static \Spatie\Multitenancy\TenantCollection<int, static> get($columns = ['*'])
 * @method static Builder<static>|Tenant newModelQuery()
 * @method static Builder<static>|Tenant newQuery()
 * @method static Builder<static>|Tenant query()
 * @method static Builder<static>|Tenant whereAllowOidcLogin($value)
 * @method static Builder<static>|Tenant whereAllowPasswordAuth($value)
 * @method static Builder<static>|Tenant whereCreatedAt($value)
 * @method static Builder<static>|Tenant whereCustomDomain($value)
 * @method static Builder<static>|Tenant whereDomain($value)
 * @method static Builder<static>|Tenant whereId($value)
 * @method static Builder<static>|Tenant whereLicense($value)
 * @method static Builder<static>|Tenant whereName($value)
 * @method static Builder<static>|Tenant whereSisConfig($value)
 * @method static Builder<static>|Tenant whereSisProvider($value)
 * @method static Builder<static>|Tenant whereSmtpConfig($value)
 * @method static Builder<static>|Tenant whereSubscriptionExpiresAt($value)
 * @method static Builder<static>|Tenant whereSubscriptionStartedAt($value)
 * @method static Builder<static>|Tenant whereTimezone($value)
 * @method static Builder<static>|Tenant whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Tenant extends TenantBase
{
    /** @use HasFactory<\Database\Factories\TenantFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $guarded = [];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'sis_provider' => Sis::class,
        'sis_config' => 'encrypted:collection',
        'smtp_config' => 'encrypted:collection',
        'allow_password_auth' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::created(function (Tenant $tenant) {
            //
        });
    }

    /**
     * @return Attribute<string, never>
     */
    public function domain(): Attribute
    {
        return Attribute::get(fn ($value) => $value ?? request()->host());
    }

    /**
     * @return Attribute<Collection<string, mixed>, never>
     */
    public function sisConfig(): Attribute
    {
        /** @var Attribute<Collection<string, mixed>, never> $attribute */
        $attribute = Attribute::get(
            fn ($value): Collection => $value ? $this->castAttribute('sis_config', $value) : collect()
        );

        return $attribute;
    }

    /**
     * @return Attribute<Collection<string, mixed>, never>
     */
    public function smtpConfig(): Attribute
    {
        /** @var Attribute<Collection<string, mixed>, never> $attribute */
        $attribute = Attribute::get(function ($value): Collection {
            return $value
                ? $this->castAttribute('smtp_config', $value)
                : collect([
                    'host' => null,
                    'port' => null,
                    'username' => null,
                    'password' => null,
                    'from_name' => null,
                    'from_address' => null,
                    'encryption' => null,
                ]);
        });

        return $attribute;
    }

    /**
     * @return HasMany<School, $this>
     */
    public function schools(): HasMany
    {
        return $this->hasMany(School::class);
    }

    /**
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return HasMany<Student, $this>
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /**
     * @return HasMany<Course, $this>
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    /**
     * @return HasMany<Section, $this>
     */
    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    public static function fromRequest(Request $request): ?Tenant
    {
        return static::getByHost($request->getHost());
    }

    public static function fromRequestAndFallback(Request $request): Tenant
    {
        return static::fromRequest($request) ?? new self([
            'domain' => $request->getHost(),
            'sis_provider' => Sis::PS,
        ]);
    }

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
        return $this->getSisProvider()?->configured() ?? false;
    }

    public function getSisProvider(): ?SisProvider
    {
        return $this->sis_provider?->getProvider($this);
    }

    public function getSchoolFromSisId(School|int|string $sisId): School
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

    public function getConfigKey(string $configKey, ?string $key, mixed $defaultValue = null): mixed
    {
        return Arr::get(
            $this->$configKey,
            $key !== null ? (string) Str::replace("{$configKey}.", '', $key) : null,
            $defaultValue
        );
    }

    public function setConfigKey(string $configKey, ?string $key, mixed $value): static
    {
        $normalizedKey = (string) Str::replace("{$configKey}.", '', $key ?? '');
        $this->$configKey = [
            ...$this->$configKey,
            $normalizedKey => $value,
        ];

        return $this;
    }

    public function getInstallationFieldValue(?string $key): mixed
    {
        return $this->getConfigFieldValue('sis_config', $key);
    }

    public function getConfigFieldValue(string $configKey, ?string $key = null): mixed
    {
        if (! $key && Str::contains($configKey, '.')) {
            [$configKey, $key] = explode('.', $configKey);

            return $this->getConfigFieldValue($configKey, $key);
        }

        return $this->$configKey->get($key);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function getInstallationRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'domain' => ['required', Rule::unique('tenants', 'domain')->ignoreModel($this)],
            ...(config('app.cloud') ? [
                'custom_domain' => [
                    'nullable',
                    Rule::unique('tenants', 'domain')->ignoreModel($this),
                    Rule::unique('tenants', 'custom_domain')->ignoreModel($this),
                ],
            ] : []),
            ...$this->sis_provider?->getRules() ?? [],
        ];
    }
}
