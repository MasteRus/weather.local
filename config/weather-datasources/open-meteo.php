<?php

return [
    'historical_url' => env('DATA_SOURCE_OPEN_METEO_HISTORICAL', 'https://archive-api.open-meteo.com/v1/archive'),
    'forecast_url'   => env('DATA_SOURCE_OPEN_METEO_FORECAST', 'https://api.open-meteo.com/v1/forecast'),
    'timezone'       => 'GMT',
    'layers'         => [
        'temperature_2m_max',
        'temperature_2m_min',
        'temperature_2m_mean',
        'precipitation_sum',
        'windspeed_10m_max',
        'windgusts_10m_max',
    ]
];
