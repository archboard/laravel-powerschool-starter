<?php

namespace App\Models;

use GrantHolle\Http\Resources\Traits\HasResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin IdeHelperStudent
 */
class Student extends Model
{
    use HasFactory;
    use HasResource;

    protected $guarded = [];

    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(Section::class);
    }
}
