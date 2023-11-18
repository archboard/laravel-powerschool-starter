<?php

namespace App\Models;

use App\Models\Contracts\ExistsInSis;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin IdeHelperStudent
 */
class Student extends Model implements ExistsInSis
{
    use HasFactory;
    use BelongsToTenant;

    protected $guarded = [];

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
