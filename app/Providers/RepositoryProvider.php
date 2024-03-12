<?php

namespace App\Providers;

use App\Repositories\ILocationRepository;
use App\Repositories\IParameterRepository;
use App\Repositories\IWeatherDataRepository;
use App\Repositories\LocationRepository;
use App\Repositories\ParameterRepository;
use App\Repositories\WeatherDataRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(IParameterRepository::class, function () {
            return new ParameterRepository();
        });
        $this->app->bind(IWeatherDataRepository::class, function () {
            return new WeatherDataRepository();
        });
        $this->app->bind(ILocationRepository::class, function () {
            return new LocationRepository();
        });
    }
}
