<?php

namespace App\Models;

use App\Models\Contracts\ExistsInSis;
use App\Traits\BelongsToSchool;
use App\Traits\BelongsToTenant;
use App\Traits\HasFirstAndLastName;
use Carbon\CarbonImmutable;
use Database\Factories\StudentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property int $tenant_id
 * @property int $school_id
 * @property int $sis_id
 * @property string|null $student_number
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $email
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property string $sis_key
 * @property CarbonImmutable|null $deleted_at
 * @property int|null $grade_level
 * @property-read Collection<int, User> $contacts
 * @property-read int|null $contacts_count
 * @property-read mixed $last_first
 * @property-read mixed $name
 * @property-read School $school
 * @property-read Collection<int, Section> $sections
 * @property-read int|null $sections_count
 * @property-read Tenant $tenant
 *
 * @method static \Database\Factories\StudentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student filter(array<string, mixed> $filters = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student search(string $search)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereGradeLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereSisId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereSisKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereStudentNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Student withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Student extends Model implements ExistsInSis
{
    use BelongsToSchool;
    use BelongsToTenant;

    /** @use HasFactory<StudentFactory> */
    use HasFactory;

    use HasFirstAndLastName;
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $guarded = [];

    /**
     * @param  Builder<Student>  $builder
     * @param  array<string, mixed>  $filters
     */
    public function scopeFilter(Builder $builder, array $filters = []): void
    {
        $sort = $filters['sort'] ?? 'last_name';
        $dir = $filters['dir'] ?? 'asc';

        $builder->when($filters['search'] ?? null, function (Builder $builder, string $search) {
            $builder->search($search);
        })->when($filters['grade'] ?? null, function (Builder $builder, $grade) {
            $builder->whereIn('grade_level', Arr::wrap($grade));
        });

        $builder->orderBy($sort, $dir);
    }

    /**
     * @param  Builder<Student>  $builder
     */
    public function scopeSearch(Builder $builder, string $search): void
    {
        $builder->where(function (Builder $builder) use ($search) {
            $builder->where('first_name', 'ilike', "%{$search}%")
                ->orWhere('last_name', 'ilike', "%{$search}%")
                ->orWhere(DB::raw("(first_name || ' ' || last_name)"), 'ilike', "%{$search}%")
                ->orWhere('student_number', 'ilike', "%{$search}%")
                ->orWhere('email', 'ilike', "%{$search}%");
        });
    }

    /**
     * @return BelongsToMany<Section, $this>
     */
    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(Section::class);
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['relationship']);
    }

    public function syncFromSis(): static
    {
        $this->tenant->getSisProvider()?->syncStudent($this);

        return $this;
    }
}
