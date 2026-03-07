<?php

namespace App\Models;

use App\Models\Contracts\ExistsInSis;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $tenant_id
 * @property int $school_id
 * @property string $name
 * @property int $sis_id
 * @property string|null $course_number
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property string $sis_key
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Section> $sections
 * @property-read int|null $sections_count
 * @property-read \App\Models\Tenant $tenant
 *
 * @method static \Database\Factories\CourseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course filter(array $filters = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course search(string $search)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereCourseNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereSisId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereSisKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Course extends Model implements ExistsInSis
{
    use BelongsToTenant;
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $guarded = [];

    /**
     * @param  array<string, mixed>  $filters
     */
    public function scopeFilter(Builder $builder, array $filters = []): void
    {
        $builder->when($filters['search'] ?? null, function (Builder $builder, string $search) {
            $builder->search($search);
        })->orderBy($filters['sort'] ?? 'name', $filters['dir'] ?? 'asc');
    }

    public function scopeSearch(Builder $builder, string $search): void
    {
        $builder->where(function (Builder $builder) use ($search) {
            $builder->where('course_number', 'ilike', "%{$search}%")
                ->orWhere('name', 'ilike', "%{$search}%");
        });
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function syncFromSis(): static
    {
        return $this->tenant->getSisProvider()
            ->syncCourse($this);
    }
}
