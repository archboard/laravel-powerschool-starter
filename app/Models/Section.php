<?php

namespace App\Models;

use App\Models\Contracts\ExistsInSis;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property int $tenant_id
 * @property int $school_id
 * @property int $course_id
 * @property int $user_id
 * @property int $sis_id
 * @property string|null $section_number
 * @property string|null $expression
 * @property string|null $external_expression
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property string $sis_key
 * @property-read \App\Models\Course $course
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Student> $students
 * @property-read int|null $students_count
 * @property-read \App\Models\Tenant $tenant
 *
 * @method static \Database\Factories\SectionFactory factory($count = null, $state = [])
 * @method static Builder<static>|Section filter(array $filters = [])
 * @method static Builder<static>|Section newModelQuery()
 * @method static Builder<static>|Section newQuery()
 * @method static Builder<static>|Section query()
 * @method static Builder<static>|Section search(string $search)
 * @method static Builder<static>|Section whereCourseId($value)
 * @method static Builder<static>|Section whereCreatedAt($value)
 * @method static Builder<static>|Section whereExpression($value)
 * @method static Builder<static>|Section whereExternalExpression($value)
 * @method static Builder<static>|Section whereId($value)
 * @method static Builder<static>|Section whereSchoolId($value)
 * @method static Builder<static>|Section whereSectionNumber($value)
 * @method static Builder<static>|Section whereSisId($value)
 * @method static Builder<static>|Section whereSisKey($value)
 * @method static Builder<static>|Section whereTenantId($value)
 * @method static Builder<static>|Section whereUpdatedAt($value)
 * @method static Builder<static>|Section whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Section extends Model implements ExistsInSis
{
    use BelongsToTenant;
    use HasFactory;

    protected $guarded = [];

    public function scopeFilter(Builder $builder, array $filters = []): void
    {
        $builder->select('sections.*')
            ->when($filters['search'] ?? null, function (Builder $builder, string $search) {
                $builder->search($search);
            })
            ->join('courses', 'courses.id', '=', 'sections.course_id')
            ->orderBy('courses.name');
    }

    public function scopeSearch(Builder $builder, string $search): void
    {
        $builder->where(function (Builder $builder) use ($search) {
            $builder->where('section_number', 'ilike', "%{$search}%")
                ->orWhereHas('course', function (Builder $builder) use ($search) {
                    $builder->search($search);
                });
        });
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function syncFromSis(): static
    {
        return $this->tenant->getSisProvider()
            ->syncSection($this);
    }
}
