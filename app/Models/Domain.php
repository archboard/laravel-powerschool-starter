<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperDomain
 */
class Domain extends Model
{
    public function hostname(): Attribute
    {
        return Attribute::set(fn ($value) => strtolower($value));
    }
}
