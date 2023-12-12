<?php

namespace App\Models;

use App\Models\Contracts\ExistsInSis;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperCourse
 */
class Course extends Model implements ExistsInSis
{
    use BelongsToTenant;
    use HasFactory;

    protected $guarded = [];

    public function syncFromSis(): static
    {
        return $this->tenant->getSisProvider()
            ->syncCourse($this);
    }
}
