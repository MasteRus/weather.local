<?php

namespace App\Providers;

use App\Repositories\IParameterRepository;
use App\Repositories\IWeatherDataRepository;
use App\Repositories\ParameterRepository;
use App\Repositories\WeatherDataRepository;
use App\Service\WeatherDataSource\OpenMeteoSource;
use App\Service\WeatherDataSource\WeatherApiSource;
use App\Service\WeatherDataSource\WeatherSourceInterface;
use Illuminate\Support\ServiceProvider;

class WeatherDataSourceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(IParameterRepository::class, function () {
            return new ParameterRepository();
        });
        $this->app->bind(IWeatherDataRepository::class, function () {
            return new WeatherDataRepository();
        });

        $this->app->bind(WeatherSourceInterface::class, WeatherApiSource::class);
        $this->app->bind(WeatherSourceInterface::class, OpenMeteoSource::class);
        $this->app->tag([WeatherApiSource::class, OpenMeteoSource::class], 'WeatherSource');
    }
}
