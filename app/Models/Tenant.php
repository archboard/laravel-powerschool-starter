<?php

namespace App\Models;

use App\Enums\Sis;
use App\SisProviders\SisProvider;
use GrantHolle\Http\Resources\Traits\HasResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
            BouncerFacade::allow(User::DISTRICT_ADMIN)->everything();

            // Additional seeding as the project needs
        });
    }

    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class);
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

    public function sisProvider(): SisProvider
    {
        return new $this->sis_provider
            ?->getProvider($this);
    }

    public function getSchoolFromSisId($sisId): School
    {
        if ($sisId instanceof School) {
            return $sisId;
        }

        /** @var School $school */
        $school = $this->schools()->where('sis_id', $sisId)->firstOrFail();
        return $school;
    }

    public function toArray()
    {
        return [
            'name' => $this->name,
            'ps_url' => $this->ps_url,
            'ps_client_id' => $this->ps_client_id,
            'ps_secret' => $this->ps_secret,
            'allow_password_auth' => $this->allow_password_auth,
        ];
    }
}
