<?php

namespace App\Listeners;

use App\Events\GetNewWeatherDataEvent;
use App\Service\WeatherDataSource\WeatherSourceInterface;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Str;

class GetNewWeatherDataListener
{
    private iterable $sources;

    public function __construct(Container $container)
    {
        $this->sources = $container->tagged('WeatherSource');
    }

    /**
     * Handle the event.
     */
    public function handle(GetNewWeatherDataEvent $event): void
    {
        $sourcesConfigs = config('weather-datasources');
        /** @var WeatherSourceInterface $dataSource */
        foreach ($this->sources as $dataSource) {
            $configName = Str::kebab(Str::lcfirst(last(explode('\\', $dataSource::class))));
            $isEnabled = $sourcesConfigs[$configName]['enabled'] ?? false;
            if ($isEnabled) {
                $dataSource->dispatchEventJob($event);
            }
        }
    }
}
