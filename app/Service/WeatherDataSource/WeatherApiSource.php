<?php

namespace App\Service\WeatherDataSource;

use App\Jobs\WeatherApiHistoricalParserJob;
use App\Models\Location;
use App\Models\Parameter;
use App\Models\WeatherData;
use App\Repositories\ParameterRepository;
use App\Repositories\WeatherDataRepository;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class WeatherApiSource implements WeatherSourceInterface
{
    public const SOURCE_NAME = 'weatherapi.com';

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
//                $service = new WeatherApiParser((new ParameterRepository()), new WeatherDataRepository());
//                $service->parse($response, $startDate, $finishDate, $location);
                WeatherApiHistoricalParserJob::dispatch($response, $startDate, $finishDate, $location);
            }
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
        $url = $this->getBaseUrl() . '?'.http_build_query(
                [
                    'dt' => $startDate,
                    'end_dt' => $finishDate,
                    'key' => config('weather-datasources.weatherapi.apikey'),
                    'q' => $location->latitude . ',' . $location->longitude,
                ]
            );

        return Http::get($url);
    }

    public function getBaseUrl(): string
    {
        return config(
                'weather-datasources.weatherapi.history_url'
            );
    }

    /**
     * @param int|string $key
     * @return mixed
     */
    protected function findParam(int|string $key)
    {
        $param = Parameter::firstOrCreate(
            [
                'name'      => $key,
                'valuetype' => 'float'
            ]
        );

        return $param;
    }

    /**
     * @param string $startDate
     * @param string $finishDate
     * @param Location $location
     */
    protected function purgeOldData(string $startDate, string $finishDate, Location $location): void
    {
        WeatherData::whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $finishDate)->where(
                [
                    'source'      => self::SOURCE_NAME,
                    'location_id' => $location->id
                ]
            )->delete();
    }

    /**
     * @param int $count
     * @param Location $location
     * @param mixed $param
     * @param mixed $value
     * @param $times
     */
    protected function insertData(int $count, Location $location, mixed $param, mixed $value, $times): void
    {
        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $data[] = [
                'location_id'  => $location->id,
                'parameter_id' => $param->id,
                'value'        => $value[$i],
                'date'         => $times[$i],
                'source'       => self::SOURCE_NAME
            ];
        }
        WeatherData::insert($data);
    }
}
