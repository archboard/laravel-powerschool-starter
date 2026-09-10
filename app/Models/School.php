<?php

namespace App\Models;

use App\Models\Contracts\ExistsInSis;
use App\Traits\BelongsToTenant;
use Carbon\CarbonImmutable;
use Database\Factories\SchoolFactory;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $tenant_id
 * @property int $sis_id
 * @property int|null $school_number
 * @property string $name
 * @property int|null $high_grade
 * @property int|null $low_grade
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property bool $active
 * @property string $sis_key
 * @property-read Collection<int, Course> $courses
 * @property-read int|null $courses_count
 * @property-read Collection<int, Section> $sections
 * @property-read int|null $sections_count
 * @property-read Collection<int, Student> $students
 * @property-read int|null $students_count
 * @property-read Tenant $tenant
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School active()
 * @method static \Database\Factories\SchoolFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereHighGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereLowGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereSchoolNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereSisId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereSisKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class School extends Model implements ExistsInSis
{
    use BelongsToTenant;

    /** @use HasFactory<SchoolFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $guarded = [];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'active' => 'boolean',
    ];

    public function scopeActive(Builder $builder): void
    {
        $builder->where('active', true);
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['staff_id']);
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

    /**
     * @return HasMany<Student, $this>
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function syncFromSis(): static
    {
        $provider = $this->tenant->getSisProvider();
        $provider?->syncSchool($this);

        return $this;
    }

    public function syncStaff(): static
    {
        $provider = $this->tenant->getSisProvider();
        $provider?->syncSchoolStaff($this);

        return $this;
    }

    public function syncStudents(): static
    {
        $provider = $this->tenant->getSisProvider();
        $provider?->syncSchoolStudents($this);

        return $this;
    }

    public function syncCourses(): static
    {
        $provider = $this->tenant->getSisProvider();
        $provider?->syncSchoolCourses($this);

        return $this;
    }

    public function syncSections(): static
    {
        $provider = $this->tenant->getSisProvider();
        $provider?->syncSchoolSections($this);

        return $this;
    }
}
