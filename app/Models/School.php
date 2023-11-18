<?php

namespace App\Models;

use App\Models\Contracts\ExistsInSis;
use App\Traits\BelongsToTenant;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperSchool
 */
class School extends Model implements ExistsInSis
{
    use HasFactory;
    use BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function scopeActive(Builder $builder): void
    {
        $builder->where('active', true);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['staff_id']);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function syncFromSis(): static
    {
        $provider = $this->tenant->getSisProvider();

        return $provider->syncSchool($this);
    }
}
