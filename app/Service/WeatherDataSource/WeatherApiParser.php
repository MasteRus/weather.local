<?php

namespace App\Service\WeatherDataSource;

use App\Models\Location;
use App\Repositories\IParameterRepository;
use App\Repositories\IWeatherDataRepository;

class WeatherApiParser
{
    private IParameterRepository $parameterRepository;
    private IWeatherDataRepository $weatherDataRepository;

    /**
     * @param IParameterRepository $parameterRepository
     * @param IWeatherDataRepository $weatherDataRepository
     */
    public function __construct(
        IParameterRepository $parameterRepository,
        IWeatherDataRepository $weatherDataRepository
    ) {
        $this->parameterRepository = $parameterRepository;
        $this->weatherDataRepository = $weatherDataRepository;
    }

    /**
     * @param array|null $response
     * @param string $startDate
     * @param string $finishDate
     * @param Location $location
     */
    public function parse(?array $response, string $startDate, string $finishDate, Location $location): void
    {
        $layers = config('weather-datasources.weather-api.layers');
        $dailyData = $response['forecast']['forecastday'] ?? [];
        if (!$dailyData) {
            return ;
        }

        $paramsArr = [];
        if (count($dailyData)) {
            foreach ($layers as $layer => $layerType) {
                $paramsArr[$layer] = $this->parameterRepository->findOrCreate(WeatherApiSource::SOURCE_NAME, $layer, null,$layerType);
            }
        }

        $this->weatherDataRepository->purgeOldData(
            $startDate,
            $finishDate,
            $location,
            WeatherApiSource::SOURCE_NAME
        );

        $values = [];
        foreach ($dailyData as $value) {
            foreach ($layers as $layer => $layerType) {
                $values[] = [
                    'location_id'  => $location->id,
                    'parameter_id' => $paramsArr[$layer]->id,
                    'value'        => $value['day'][$layer],//@TODO
                    'date'         => $value['date'],       //@TODO
                    'source'       => WeatherApiSource::SOURCE_NAME,
                ];
            }
        }

        $this->weatherDataRepository->insertDataWeatherApi($values);
    }
}
