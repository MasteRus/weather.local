<?php

namespace App\Service\WeatherDataSource;

use App\Jobs\OpenMeteoHistoricalParserJob;
use App\Models\Location;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class OpenMeteoSource implements WeatherSourceInterface
{
    public const SOURCE_NAME = 'open-meteo';

    /**
     * @param string $startDate
     * @param string $finishDate
     * @param Location $location
     */
    public function getData(string $startDate, string $finishDate, Location $location)
    {
        try {
        $response = $this->get($startDate, $finishDate, $location)->json();
        OpenMeteoHistoricalParserJob::dispatch($response, $startDate, $finishDate, $location);
        } catch (\Throwable $e) {
            Log::error('Http client Exception ', ['error' => $e]);

            throw new BadRequestException('Service cannot receive rates. Try again later or contact administrator');
        }
    }

    /**
     * @param string $startDate
     * @param string $finishDate
     * @param Location $location
     * @return Response
     */
    protected function get(
        string $startDate,
        string $finishDate,
        Location $location
    ): Response {
        $url = $this->getBaseUrl() . '?' . http_build_query(
                [
                    'start_date' => $startDate,
                    'end_date'   => $finishDate,
                    'timezone'   => 'GMT',
                    'latitude'   => $location->latitude,
                    'longitude'  => $location->longitude,
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
