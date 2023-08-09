<?php

namespace App\Service\WeatherDataSource;

use App\Events\GetNewWeatherDataEvent;
use App\Jobs\DataSourceReceiveDataJob;

class AbstractWeatherDataSource
{
    public function dispatchEventJob(GetNewWeatherDataEvent $event)
    {
        DataSourceReceiveDataJob::dispatch($event->startDate, $event->finishDate, $event->location, get_class($this));
    }
}
