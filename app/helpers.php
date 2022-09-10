<?php

use Illuminate\Support\Collection;

if (!function_exists('timezones')) {
    /**
     * Gets a list of all timezones
     * or the formatted name of the given timezone
     *
     * @param string|null $timezone
     * @return Collection|string
     */
    function timezones(string $timezone = null): Collection|string {
        $zones = collect(DateTimeZone::listIdentifiers())
            ->mapWithKeys(function ($zoneId) {
                $zone = IntlTimeZone::createTimeZone($zoneId);
                $name = $zone->getDisplayName($zone->useDaylightTime());
                $cleaned = str_replace('_', ' ', $zoneId);

                return [
                    $zoneId => "{$cleaned} ({$name})",
                ];
            });

        if (is_null($timezone)) {
            return $zones;
        }

        return $zones->get($timezone);
    }
}
