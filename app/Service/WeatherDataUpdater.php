<?php

namespace App\Service;

use App\Events\GetNewWeatherDataEvent;
use App\Models\Location;

class WeatherDataUpdater
{
    public function getData(string $startDate, string $finishDate): void
    {
        foreach (Location::all() as $loc) {
            GetNewWeatherDataEvent::dispatch($loc, $startDate, $finishDate);
        }
    }
}
