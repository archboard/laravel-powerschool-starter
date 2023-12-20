<?php

namespace App\Traits;

use Carbon\FactoryImmutable as Factory;

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
