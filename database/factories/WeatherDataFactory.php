<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\Parameter;
use App\Service\WeatherDataSource\OpenMeteoSource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WeatherData>
 */
class WeatherDataFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'value'        => 'temperature',
            'source'       => OpenMeteoSource::SOURCE_NAME,
            'date'         => '2020-01-01 00:00:01',
            'location_id'  => self::factoryForModel(Location::class)->create()->getKey(),
            'parameter_id' => self::factoryForModel(Parameter::class)->create()->getKey(),
        ];
    }
}
