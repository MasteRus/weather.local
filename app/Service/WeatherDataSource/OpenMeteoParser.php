<?php

namespace App\Service\WeatherDataSource;

use App\Models\Location;
use App\Repositories\IParameterRepository;
use App\Repositories\IWeatherDataRepository;

class OpenMeteoParser
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
        $times = $response['daily']['time'] ?? [];
        $dailyData = $response['daily'] ?? [];
        $dailyUnits = $response['daily_units'] ?? [];
        $params = [];
        foreach ($dailyUnits as $key => $value) {
            if ($key == 'time') {
                continue;
            }
            $params[$key] = $this->parameterRepository->findOrCreate(OpenMeteoSource::SOURCE_NAME, $key, $value);
        }

        foreach ($dailyData as $key => $value) {
            if ($key == 'time') {
                continue;
            }
            $count = count($value);

            $this->weatherDataRepository->purgeOldData(
                $startDate,
                $finishDate,
                $location,
                OpenMeteoSource::SOURCE_NAME,
                $key
            );
            $this->weatherDataRepository->insertDataOpenMeteo(
                $count,
                $location,
                $params[$key],
                $value,
                $times,
                OpenMeteoSource::SOURCE_NAME
            );
        }
    }
}
