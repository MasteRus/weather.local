<?php

namespace App\Service\WeatherDataSource;

use App\Models\Location;
use App\Models\Parameter;
use App\Models\WeatherData;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class OpenMeteoSource
{
    public const SOURCE_NAME = 'open-meteo';

    /**
     * @param string $startDate
     * @param string $finishDate
     * @param Location $location
     */
    public function getData(string $startDate, string $finishDate, Location $location)
    {
        $response = $this->get($startDate, $finishDate, $location->latitude, $location->longitude);
        $times = $response['daily']['time'];
        foreach ($response['daily'] as $key => $value) {
            if ($key == 'time') {
                continue;
            }
            $param = Parameter::firstOrCreate(
                [
                    'name'      => $key,
                    'valuetype' => 'float'
                ]
            );
            $count = count($value);

            WeatherData::whereDate('date', '>=', $startDate)
                ->whereDate('date', '<=', $finishDate)->where(
                    [
                        'source'      => self::SOURCE_NAME,
                        'location_id' => $location->id
                    ]
                )->delete();
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
        return config('weather-datasources.open-meteo.historical_url');;
    }
}
