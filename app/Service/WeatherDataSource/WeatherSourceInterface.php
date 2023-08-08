<?php

namespace App\Service\WeatherDataSource;

use App\Models\Location;

interface WeatherSourceInterface
{
    public function getData(string $startDate, string $finishDate, Location $location);

    public function getBaseUrl(): string;
}
