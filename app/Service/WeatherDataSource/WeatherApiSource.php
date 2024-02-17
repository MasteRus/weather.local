<?php

namespace App\Service\WeatherDataSource;

use App\Jobs\WeatherApiHistoricalGetDataJob;
use App\Jobs\WeatherApiHistoricalParserJob;
use App\Models\Location;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Throwable;

class WeatherApiSource extends BaseWeatherDataSource implements WeatherSourceInterface
{
    public const SOURCE_NAME = 'weatherapi.com';

    /**
     * @param string $startDate
     * @param string $finishDate
     * @param Location $location
     */
    public function getData(string $startDate, string $finishDate, Location $location): void
    {
        try {
            $response = $this->get($startDate, $finishDate, $location)->json() ?? null;
            $error = $response['error'] ?? false;

            if ($response && !$error) {
                WeatherApiHistoricalParserJob::dispatch($response, $startDate, $finishDate, $location);
            }
        } catch (Throwable $e) {
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
                    'dt'     => $startDate,
                    'end_dt' => $finishDate,
                    'key'    => config('weather-datasources.weather-api-source.apikey'),
                    'q'      => $location->latitude . ',' . $location->longitude,
                ]
            );

        return Http::get($url);
    }

    public function getBaseUrl(): string
    {
        return config('weather-datasources.weather-api-source.history_url');
    }

    public function dispatchGetResponseJob(string $startDate, string $finishDate, Location $location): void
    {
        WeatherApiHistoricalGetDataJob::dispatch($startDate, $finishDate, $location);
    }
}
