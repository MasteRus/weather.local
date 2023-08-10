<?php

namespace App\Service\WeatherDataSource;

use App\Jobs\OpenMeteoHistoricalParserJob;
use App\Jobs\WeatherApiHistoricalParserJob;
use App\Models\Location;
use App\Repositories\IParameterRepository;
use App\Repositories\IWeatherDataRepository;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class WeatherApiSourceTest extends TestCase
{

    private const RESPONSE_VALIDATION_ERROR = '{
    "error": {
        "code": 1007,
        "message": "dt or end_dt parameter should be in yyyy-MM-dd format and on or after 1st Jan, 2010 (2010-01-01)."
    }
}';
    private const RESPONSE_SUCCESS = '{
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
        yield 'success response' => [self::RESPONSE_SUCCESS, true];
        yield 'error response' => [self::RESPONSE_VALIDATION_ERROR, false];
    }

    /**
     * test_getBaseUrl
     * DdataProviderResponses
     */

    public function test_getBaseUrl(): void
    {
        $source = new WeatherApiSource();
        Config::set('weather-datasources.weather-api.historical_url', 'http://api.weatherapi.com/v1/history.json');
        $url = $source->getBaseUrl();
        $this->assertEquals('http://api.weatherapi.com/v1/history.json', $url);
    }

    /** @dataProvider dataProviderResponses */
    public function test_getData(string $response, bool $success): void
    {
        Config::set('weather-datasources.weather-api.historical_url', 'http://api.weatherapi.com/v1/history.json');
        Queue::fake();
        Http::fake(Http::response($response));

        $location = $this->partialMock(Location::class);

        $source = new WeatherApiSource();
        $source->getData('2023-01-01', '2023-01-30', $location);

        if ($success) {
            Queue::assertPushed(WeatherApiHistoricalParserJob::class);
        } else {
            Queue::assertNotPushed(WeatherApiHistoricalParserJob::class);
        }
    }
}
