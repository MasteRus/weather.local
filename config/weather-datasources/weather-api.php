<?php

return [
    'enabled'      => true,
    'apikey'       => env('DATA_SOURCE_WEATHER_API_KEY', ''),
    'history_url'  => env('DATA_SOURCE_WEATHERAPI_HISTORICAL', 'http://api.weatherapi.com/v1/history.json'),
    'forecast_url' => env('DATA_SOURCE_WEATHERAPI_FORECAST', 'http://api.weatherapi.com/v1/forecast.json'),
    // Название слоя в источнике - тип слоя у нас
    'layers'       => [
        'avgtemp_c'      => 'temperature',
        'mintemp_c'      => 'temperature_min',
        'maxtemp_c'      => 'temperature_max',
        'avghumidity'    => 'humidity',
        'totalprecip_mm' => 'precipitation',
    ],
];
