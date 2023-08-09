<?php

namespace App\Service\WeatherDataSource;

use App\Models\Location;
use App\Models\Parameter;
use App\Repositories\IParameterRepository;
use App\Repositories\IWeatherDataRepository;
use Mockery\MockInterface;
use Tests\TestCase;


class WeatherApiParserTest extends TestCase
{
    const RESPONSE_VALIDATION_ERROR = '{
    "reason": "End-date must be larger or equals than start-date",
    "error": true
}';
    const RESPONSE_SUCCESS = '{
    "location": {
        "name": "Pano Polemidia",
        "region": "Limassol",
        "country": "Cyprus",
        "lat": 34.71,
        "lon": 33.02,
        "tz_id": "Asia/Nicosia",
        "localtime_epoch": 1691588210,
        "localtime": "2023-08-09 16:36"
    },
    "forecast": {
        "forecastday": [
            {
                "date": "2023-01-31",
                "date_epoch": 1675123200,
                "day": {
                    "maxtemp_c": 15.8,
                    "maxtemp_f": 60.4,
                    "mintemp_c": 9.7,
                    "mintemp_f": 49.5,
                    "avgtemp_c": 12.1,
                    "avgtemp_f": 53.8,
                    "maxwind_mph": 21.7,
                    "maxwind_kph": 34.9,
                    "totalprecip_mm": 0.0,
                    "totalprecip_in": 0.0,
                    "avgvis_km": 9.7,
                    "avgvis_miles": 6.0,
                    "avghumidity": 66.0,
                    "condition": {
                        "text": "Overcast",
                        "icon": "//cdn.weatherapi.com/weather/64x64/day/122.png",
                        "code": 1009
                    },
                    "uv": 4.0
                },
                "astro": {
                    "sunrise": "06:47 AM",
                    "sunset": "05:16 PM",
                    "moonrise": "12:36 PM",
                    "moonset": "02:43 AM",
                    "moon_phase": "Waxing Gibbous",
                    "moon_illumination": "73"
                },
                "hour": []
            }
        ]
    }
}';

    public static function dataProviderResponses(): iterable
    {
        yield 'success response' => [self::RESPONSE_SUCCESS, '2023-01-01', '2023-01-07', 1, 1, 5];
        yield 'failed response' => ['{}', '2023-01-01', '2023-01-07', 0, 0, 0];
        yield 'Null' => [null, '2023-01-01', '2023-01-07', 0, 0, 0];
    }

    /** @dataProvider dataProviderResponses */
    public function testParse(?string $response, string $startDate, string $finishDate, int $callPurgeTime, int $callInsertDataTime, int $callInsertParamsTime): void
    {
        $mockParameter = $this->partialMock(Parameter::class);

        $weatherDataRepositoryMock = $this->partialMock(
            IWeatherDataRepository::class,
            function (MockInterface $mock) use ($callPurgeTime, $callInsertDataTime) {
                $mock->shouldReceive('purgeOldData')->times($callPurgeTime);
                $mock->shouldReceive('insertDataWeatherApi')->times($callInsertDataTime);
            }
        );

        $parameterRepositoryMock = $this->partialMock(
            IParameterRepository::class,
            function (MockInterface $mock) use ($callInsertParamsTime, $mockParameter) {
                $mock->shouldReceive('findOrCreate')->times($callInsertParamsTime)->andReturn($mockParameter);
            }
        );

        $location = $this->partialMock(Location::class);

        $service = new WeatherApiParser($parameterRepositoryMock, $weatherDataRepositoryMock);
        $service->parse(json_decode($response, true), $startDate, $finishDate, $location);
    }
}
