<?php

namespace App\Service\WeatherDataSource;

use App\Events\GetNewWeatherDataEvent;
use App\Models\Location;

interface WeatherSourceInterface
{
    public function getData(string $startDate, string $finishDate, Location $location);

    public function getBaseUrl(): string;

    public function dispatchGetResponseJob(string $startDate, string $finishDate, Location $location);

    public function dispatchEventJob(GetNewWeatherDataEvent $event): void;
}
