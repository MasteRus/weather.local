<?php

namespace App\Service\WeatherDataSource;

use App\Jobs\OpenMeteoHistoricalGetDataJob;
use App\Jobs\OpenMeteoHistoricalParserJob;
use App\Models\Location;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class OpenMeteoSource extends AbstractWeatherDataSource implements WeatherSourceInterface
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
            $response = $this->get($startDate, $finishDate, $location)->json() ?? null;
            $error = $response['error'] ?? false;
            if ($response && !$error) {
                OpenMeteoHistoricalParserJob::dispatch($response, $startDate, $finishDate, $location);
            }
        } catch (\Throwable $e) {
            Log::error('Http client Exception ', ['error' => $e]);

            throw new BadRequestException('Service ' . self::SOURCE_NAME . ' bad response');
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
                    'daily'      => implode(',', array_keys(config('weather-datasources.open-meteo.layers'))),
                ]
            );

        $response = Http::get($url);

        return $response;
    }

    public function getBaseUrl(): string
    {
        return config('weather-datasources.open-meteo.historical_url');
    }

    public function dispatchGetResponseJob(string $startDate, string $finishDate, Location $location)
    {
        OpenMeteoHistoricalGetDataJob::dispatch($startDate, $finishDate, $location);
    }
}
