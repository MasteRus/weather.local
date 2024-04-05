<?php

namespace Database\Factories;

use App\Service\WeatherDataSource\OpenMeteoSource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Parameter>
 */
class ParameterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'      => 'temperature',
            'source'    => OpenMeteoSource::SOURCE_NAME,
            'type'      => 'temperature',
            'valuetype' => 'float',
            'units'     => '°C',
        ];
    }
}
