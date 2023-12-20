<?php

namespace App\Traits;

use Carbon\Factory;

trait HasTimezone
{
    public function getCarbonFactory(): Factory
    {
        return new Factory([
            'locale' => app()->getLocale(),
            'timezone' => $this->timezone,
        ]);
    }
}
