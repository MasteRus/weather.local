<?php

namespace App\Http\Responses;

class LocationAverageWeatherResponse
{
    public string $startDate;
    public string $finishDate;
    public array $values;

    public function __construct(string $startDate, string $finishDate, array $values)
    {
        $this->startDate = $startDate;
        $this->finishDate = $finishDate;
        $this->values = $values;
    }
}
