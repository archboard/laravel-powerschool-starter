<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasFirstAndLastName
{
    /**
     * @return Attribute<string, never>
     */
    public function name(): Attribute
    {
        return Attribute::get(fn () => "{$this->first_name} {$this->last_name}");
    }

    /**
     * @return Attribute<string, never>
     */
    public function lastFirst(): Attribute
    {
        return Attribute::get(fn () => "{$this->last_name}, {$this->first_name}");
    }
}
