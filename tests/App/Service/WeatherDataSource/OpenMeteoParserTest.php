<?php

namespace App\Service\WeatherDataSource;

use App\Models\Location;
use App\Repositories\IParameterRepository;
use App\Repositories\IWeatherDataRepository;
use Mockery\MockInterface;
use Tests\TestCase;


class OpenMeteoParserTest extends TestCase
{
    const RESPONSE_VALIDATION_ERROR = '{
    "reason": "End-date must be larger or equals than start-date",
    "error": true
}';
    const RESPONSE_SUCCESS = '{
    "latitude": 33.1,
    "longitude": 33.0,
    "generationtime_ms": 0.4659891128540039,
    "utc_offset_seconds": 0,
    "timezone": "GMT",
    "timezone_abbreviation": "GMT",
    "elevation": 0.0,
    "daily_units": {
        "time": "iso8601",
        "temperature_2m_max": "°C",
        "temperature_2m_min": "°C",
        "temperature_2m_mean": "°C",
        "precipitation_sum": "mm",
        "windspeed_10m_max": "km/h",
        "windgusts_10m_max": "km/h"
    },
    "daily": {
        "time": [
            "2023-01-01",
            "2023-01-02",
            "2023-01-03",
            "2023-01-04",
            "2023-01-05",
            "2023-01-06",
            "2023-01-07"
        ],
        "temperature_2m_max": [
            19.4,
            19.3,
            18.8,
            18.7,
            18.6,
            18.7,
            18.4
        ],
        "temperature_2m_min": [
            18.3,
            18.3,
            17.8,
            17.6,
            16.7,
            17.0,
            17.1
        ],
        "temperature_2m_mean": [
            18.8,
            18.7,
            18.4,
            18.2,
            17.4,
            17.8,
            18.1
        ],
        "precipitation_sum": [
            0.10,
            0.00,
            0.60,
            0.20,
            0.20,
            1.00,
            0.10
        ],
        "windspeed_10m_max": [
            37.9,
            39.4,
            35.7,
            23.9,
            26.6,
            26.3,
            20.3
        ],
        "windgusts_10m_max": [
            51.8,
            53.3,
            49.3,
            33.5,
            35.6,
            35.6,
            31.0
        ]
    }
}';

    public static function dataProviderResponses(): iterable
    {
        yield 'success response' => [self::RESPONSE_SUCCESS, '2023-01-01', '2023-01-07', 6];
        yield 'failed response' => ['{}', '2023-01-01', '2023-01-07', 0];
        yield 'Null' => [null, '2023-01-01', '2023-01-07', 0];
    }

    /** @dataProvider dataProviderResponses */
    public function testParse(?string $response, string $startDate, string $finishDate, int $times): void
    {
        $weatherDataRepositoryMock = $this->partialMock(
            IWeatherDataRepository::class,
            function (MockInterface $mock) use ($times) {
                $mock->shouldReceive('purgeOldData')->times($times);
                $mock->shouldReceive('insertData')->times($times);
            }
        );

        $parameterRepositoryMock = $this->partialMock(
            IParameterRepository::class,
            function (MockInterface $mock) use ($times) {
                $mock->shouldReceive('findOrCreate')->times($times);
            }
        );

        $location = $this->partialMock(Location::class);

        $service = new OpenMeteoParser($parameterRepositoryMock, $weatherDataRepositoryMock);
        $service->parse(json_decode($response, true), $startDate, $finishDate, $location);
    }
}
