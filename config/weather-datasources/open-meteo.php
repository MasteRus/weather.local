<?php

return [
    # Включает или отключает сбор с этого источника
    'enabled'        => true,
    'historical_url' => env('DATA_SOURCE_OPEN_METEO_HISTORICAL', 'https://archive-api.open-meteo.com/v1/archive'),
    # Временно не используется
    'forecast_url'   => env('DATA_SOURCE_OPEN_METEO_FORECAST', 'https://api.open-meteo.com/v1/forecast'),
    'timezone'       => 'GMT',
    // Название слоя в источнике - тип слоя у нас, группировка данных из разных источников производится по типу слоя
    'layers'         => [
        'temperature_2m_max'  => 'temperature_max',
        'temperature_2m_min'  => 'temperature_min',
        'temperature_2m_mean' => 'temperature',
        'precipitation_sum'   => 'precipitation',
        'windspeed_10m_max'   => 'wind_min',
        'windgusts_10m_max'   => 'wind_max',
    ]
];
