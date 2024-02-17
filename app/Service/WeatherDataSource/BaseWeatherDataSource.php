<?php

namespace App\Service\WeatherDataSource;

use App\Events\GetNewWeatherDataEvent;
use App\Jobs\DataSourceReceiveDataJob;

class BaseWeatherDataSource
{
    public function dispatchEventJob(GetNewWeatherDataEvent $event): void
    {
        DataSourceReceiveDataJob::dispatch($event->startDate, $event->finishDate, $event->location, get_class($this));
    }
}
