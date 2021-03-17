<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use GrantHolle\Http\Resources\Traits\HasResource;
use GrantHolle\PowerSchool\Api\Facades\PowerSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @mixin IdeHelperSchool
 */
class School extends Model
{
    use HasFactory;
    use BelongsToTenant;
    use HasResource;

    protected $guarded = [];

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

    public static function getFromPowerSchool(array $ids = []): Collection
    {
        $psSchools = PowerSchool::endpoint('/ws/v1/district/school')
            ->get();

        return collect($psSchools->schools->school);
    }

    public function syncFromPowerSchool(): static
    {
        $psSchool = PowerSchool::endpoint("/ws/v1/school/{$this->dcid}")
            ->get();

        $this->update([
            'name' => $psSchool->name,
            'sis_id' => $psSchool->id,
            'school_number' => $psSchool->school_number,
            'high_grade' => $psSchool->high_grade,
            'low_grade' => $psSchool->low_grade,
        ]);

        return $this;
    }
}
