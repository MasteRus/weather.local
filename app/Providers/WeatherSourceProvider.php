<?php

namespace App\Providers;

use App\Repositories\IParameterRepository;
use App\Repositories\IWeatherDataRepository;
use App\Repositories\ParameterRepository;
use App\Repositories\WeatherDataRepository;
use Illuminate\Support\ServiceProvider;

class WeatherSourceProvider extends ServiceProvider
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
    }
}
