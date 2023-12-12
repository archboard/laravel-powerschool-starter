<?php

namespace App\Models;

use App\Models\Contracts\ExistsInSis;
use App\Traits\BelongsToSchool;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * @mixin IdeHelperStudent
 */
class Student extends Model implements ExistsInSis
{
    use BelongsToTenant;
    use BelongsToSchool;
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function scopeFilter(Builder $builder, array $filters = []): void
    {
        $sort = $filters['sort'] ?? 'last_name';
        $dir = $filters['dir'] ?? 'asc';

        $builder->when($filters['search'] ?? null, function (Builder $builder, string $search) {
            $builder->where(function (Builder $builder) use ($search) {
                $builder->where('first_name', 'ilike', "%{$search}%")
                    ->orWhere('last_name', 'ilike', "%{$search}%")
                    ->orWhere(DB::raw("(first_name || ' ' || last_name)"), 'ilike', "%{$search}%")
                    ->orWhere('student_number', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%");
            });
        })->when($filters['grade'] ?? null, function (Builder $builder, $grade) {
            $builder->whereIn('grade_level', Arr::wrap($grade));
        });

        $builder->orderBy($sort, $dir);
    }

    public function name(): Attribute
    {
        return Attribute::get(fn () => "{$this->first_name} {$this->last_name}");
    }

    public function lastFirst(): Attribute
    {
        return Attribute::get(fn () => "{$this->last_name}, {$this->first_name}");
    }

    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(Section::class);
    }

    public function syncFromSis(): static
    {
        return $this->tenant->getSisProvider()
            ->syncStudent($this);
    }
}
