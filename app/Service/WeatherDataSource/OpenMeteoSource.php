<?php

namespace App\Service\WeatherDataSource;

use App\Models\Location;
use App\Repositories\ParameterRepository;
use App\Repositories\WeatherDataRepository;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class OpenMeteoSource implements WeatherSourceInterface
{
    public const SOURCE_NAME = 'open-meteo';

    private WeatherDataRepository $weatherDataRepository;
    private ParameterRepository $parameterRepository;

    /**
     * @param WeatherDataRepository $weatherDataRepository
     */
    public function __construct(WeatherDataRepository $weatherDataRepository, ParameterRepository $parameterRepository)
    {
        $this->weatherDataRepository = $weatherDataRepository;
        $this->parameterRepository = $parameterRepository;
    }

    /**
     * @param string $startDate
     * @param string $finishDate
     * @param Location $location
     */
    public function getData(string $startDate, string $finishDate, Location $location)
    {
        try {
            $response = $this->get($startDate, $finishDate, $location->latitude, $location->longitude)->json();

            $times = $response['daily']['time'];
            foreach ($response['daily'] as $key => $value) {
                if ($key == 'time') {
                    continue;
                }
                $param = $this->parameterRepository->findOrCreate($key);
                $count = count($value);

                $this->weatherDataRepository->purgeOldData($startDate, $finishDate, $location, self::SOURCE_NAME, $key);
                $this->weatherDataRepository->insertData($count, $location, $param, $value, $times, self::SOURCE_NAME);
            }
        } catch (\Throwable $e) {
            Log::error('Http client Exception ', ['error' => $e]);

            throw new BadRequestException('Service cannot receive rates. Try again later or contact administrator');
        }
    }

    /**
     * @param string $startDate
     * @param string $finishDate
     * @param string $latitude
     * @param string $longitude
     * @return Response
     */
    protected function get(
        string $startDate,
        string $finishDate,
        string $latitude,
        string $longitude
    ): Response {
        $url = $this->getBaseUrl() . '?' . http_build_query(
                [
                    'start_date' => $startDate,
                    'end_date'   => $finishDate,
                    'timezone'   => 'GMT',
                    'latitude'   => $latitude,
                    'longitude'  => $longitude,
                    'daily'      => implode(',', config('weather-datasources.open-meteo.layers')),
                ]
            );

        return Http::get($url);
    }

    public function getBaseUrl(): string
    {
        return config('weather-datasources.open-meteo.historical_url');
    }
}
