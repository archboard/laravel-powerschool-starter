<?php

namespace App\Models;

use App\Models\Contracts\ExistsInSis;
use App\Models\Contracts\Filterable;
use App\Services\Filters\BaseFilter;
use App\Services\Filters\TextFilter;
use App\Traits\BelongsToTenant;
use App\Traits\HasFilters;
use Carbon\CarbonImmutable;
use Database\Factories\SectionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
 * @property-read string $display
 * @property-read Collection<int, Student> $students
 * @property-read int|null $students_count
 * @property-read User $teacher
 * @property-read string|null $teacher_display
 * @property-read Tenant $tenant
 *
 * @method static \Database\Factories\SectionFactory factory($count = null, $state = [])
 * @method static Builder<static>|Section filter(\Illuminate\Support\Collection<array-key, mixed>|array<array-key, mixed> $data)
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
class Section extends Model implements ExistsInSis, Filterable
{
    use BelongsToTenant;

    /** @use HasFactory<SectionFactory> */
    use HasFactory;

    use HasFilters;

    protected $guarded = [];

    /** @param Builder<static> $builder */
    public function scopeSearch(Builder $builder, string $search): void
    {
        $builder->where(function (Builder $builder) use ($search) {
            $builder->where('section_number', 'ilike', "%{$search}%")
                ->orWhereHas('course', function ($builder) use ($search) {
                    $builder->where('course_number', 'ilike', "%{$search}%")
                        ->orWhere('name', 'ilike', "%{$search}%");
                });
        });
    }

    /** @return Attribute<string|null, never> */
    public function teacherDisplay(): Attribute
    {
        return Attribute::get(function (): ?string {
            return $this->relationLoaded('teacher') ? $this->teacher->name : null;
        });
    }

    /** @return Attribute<string, never> */
    public function display(): Attribute
    {
        return Attribute::get(function (): string {
            if ($this->relationLoaded('course')) {
                return "{$this->course->name} ({$this->section_number})";
            }

            return $this->section_number ?? '';
        });
    }

    /** @return BelongsToMany<Student, $this> */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class);
    }

    /** @return BelongsTo<Course, $this> */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /** @return BelongsTo<User, $this> */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function syncFromSis(): static
    {
        $this->tenant->getSisProvider()?->syncSection($this);

        return $this;
    }

    /** @return array<int, BaseFilter> */
    public function filters(): array
    {
        return [
            TextFilter::make('search', __('Search'))
                ->hide()
                ->using($this->applySearchFilter(...)),
        ];
    }

    /** @param Builder<Section> $builder */
    protected function applySearchFilter(Builder $builder, string $search): void
    {
        $builder->search($search);
    }
}
