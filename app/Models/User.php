<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use GrantHolle\Http\Resources\Traits\HasResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Silber\Bouncer\Database\HasRolesAndAbilities;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use HasResource;
    use HasRolesAndAbilities;
    use BelongsToTenant;

    const TEACHER = 'teacher';
    const DISTRICT_ADMIN = 'district admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * Gets the users who have an ability directly or through a role
     *
     * @param Builder $query
     * @param string $ability
     */
    public function scopeWhereCan(Builder $query, string $ability)
    {
        $query->where(function ($query) use ($ability) {
            // direct
            $query->whereHas('abilities', function ($query) use ($ability) {
                $query->byName($ability);
            });
            // through roles
            $query->orWhereHas('roles', function ($query) use ($ability) {
                 $query->whereHas('abilities', function ($query) use ($ability) {
                     $query->byName($ability);
                 });
             });
         });
    }

    public function getSchoolPermissionsAttribute(): array
    {
        return $this->getPermissionsForSchool();
    }

    public function schools(): BelongsToMany
    {
        return $this->belongsToMany(School::class)
            ->withPivot(['staff_id']);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function getPermissionsForSchool(School $school = null): array
    {
        $school = $school ?? $this->school;

        return [
            [
                'label' => __('Change school settings'),
                'permission' => 'change settings',
                'selected' => $this->can('change settings', $school),
            ],
        ];
    }
}
