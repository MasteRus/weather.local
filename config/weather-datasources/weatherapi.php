<?php

return [
    'apikey'       => env('DATA_SOURCE_WEATHER_API_KEY', ''),
    'history_url'     => env('DATA_SOURCE_WEATHERAPI_HISTORICAL', 'http://api.weatherapi.com/v1/history.json'),
    'forecast_url' => env('DATA_SOURCE_WEATHERAPI_FORECAST', 'http://api.weatherapi.com/v1/forecast.json'),
    'layers'       => [
        'avgtemp_c',
        'mintemp_c',
        'maxtemp_c',
        'avghumidity',
        'totalprecip_mm',
    ],
];
