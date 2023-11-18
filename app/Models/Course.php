<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperCourse
 */
class Course extends Model
{
    use HasFactory;
    use BelongsToTenant;

    protected $guarded = [];

    public function syncFromSis(): static
    {
        return $this->tenant->getSisProvider()
            ->syncCourse($this);
    }
}
