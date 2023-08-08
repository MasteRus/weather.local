<?php

namespace App\Service\WeatherDataSource;

use App\Models\Location;
use App\Repositories\IParameterRepository;
use App\Repositories\IWeatherDataRepository;
use App\Repositories\ParameterRepository;
use App\Repositories\WeatherDataRepository;

class OpenMeteoParser
{
    private ParameterRepository $parameterRepository;
    private WeatherDataRepository $weatherDataRepository;

    /**
     * @param IParameterRepository $parameterRepository
     * @param IWeatherDataRepository $weatherDataRepository
     */
    public function __construct(IParameterRepository $parameterRepository, IWeatherDataRepository $weatherDataRepository)
    {
        $this->parameterRepository = $parameterRepository;
        $this->weatherDataRepository = $weatherDataRepository;
    }

    /**
     * @param array $response
     * @param string $startDate
     * @param string $finishDate
     * @param Location $location
     */
    public function parse(array $response, string $startDate, string $finishDate, Location $location): void
    {
        $times = $response['daily']['time'];
        foreach ($response['daily'] as $key => $value) {
            if ($key == 'time') {
                continue;
            }
            $param = $this->parameterRepository->findOrCreate($key);
            $count = count($value);

            $this->weatherDataRepository->purgeOldData($startDate, $finishDate, $location, OpenMeteoSource::SOURCE_NAME, $key);
            $this->weatherDataRepository->insertData($count, $location, $param, $value, $times, OpenMeteoSource::SOURCE_NAME);
        }
    }
}
