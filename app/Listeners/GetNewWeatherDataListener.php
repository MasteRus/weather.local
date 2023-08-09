<?php

namespace App\Listeners;

use App\Events\GetNewWeatherDataEvent;
use App\Exceptions\DataSourceMisconfigurationException;
use App\Service\WeatherDataSource\AbstractWeatherDataSource;
use App\Service\WeatherDataSource\OpenMeteoSource;
use App\Service\WeatherDataSource\WeatherSourceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GetNewWeatherDataListener
{

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(GetNewWeatherDataEvent $event): void
    {
        $sources = config('weather-datasources');
        foreach ($sources as $name => $config) {
            if ($config['enabled'] ?? false) {
                $class = 'App\\Service\\WeatherDataSource\\' . Str::studly($name).'Source';
                if (!class_exists($class)) {
                    Log::error('DataSource '. $name . ' misconfiguration error');
                    continue;
                }

                /** @var AbstractWeatherDataSource $dataSource */
                $dataSource = new $class();
                if (!($dataSource instanceof WeatherSourceInterface) || !($dataSource instanceof AbstractWeatherDataSource)) {
                    Log::error('DataSource '. $name . ' Is not instance of source');
                    continue;
                }
                $dataSource->dispatchEventJob($event);
            }
        }
    }
}
