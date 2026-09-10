<?php

namespace App\Models;

use App\Models\Contracts\ExistsInSis;
use App\Traits\BelongsToTenant;
use Carbon\CarbonImmutable;
use Database\Factories\SectionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property string $sis_key
 * @property-read Course $course
 * @property-read Collection<int, Student> $students
 * @property-read int|null $students_count
 * @property-read Tenant $tenant
 *
 * @method static \Database\Factories\SectionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section filter(array<string, mixed> $filters = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section search(string $search)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereExpression($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereExternalExpression($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereSectionNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereSisId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereSisKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Section extends Model implements ExistsInSis
{
    use BelongsToTenant;

    /** @use HasFactory<SectionFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $guarded = [];

    /**
     * @param  Builder<Section>  $builder
     * @param  array<string, mixed>  $filters
     */
    public function scopeFilter(Builder $builder, array $filters = []): void
    {
        $builder->select('sections.*')
            ->when($filters['search'] ?? null, function ($builder, string $search) {
                $builder->search($search);
            })
            ->join('courses', 'courses.id', '=', 'sections.course_id')
            ->orderBy('courses.name');
    }

    /**
     * @param  Builder<Section>  $builder
     */
    public function scopeSearch(Builder $builder, string $search): void
    {
        $builder->where(function ($builder) use ($search) {
            $builder->where('section_number', 'ilike', "%{$search}%")
                ->orWhereHas('course', function ($builder) use ($search) {
                    $builder->where('course_number', 'ilike', "%{$search}%")
                        ->orWhere('name', 'ilike', "%{$search}%");
                });
        });
    }

    /**
     * @return BelongsToMany<Student, $this>
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class);
    }

    /**
     * @return BelongsTo<Course, $this>
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function syncFromSis(): static
    {
        $this->tenant->getSisProvider()?->syncSection($this);

        return $this;
    }
}
