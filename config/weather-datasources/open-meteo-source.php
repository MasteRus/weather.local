<?php

use App\Dictionaries\LayerDatatypes;

return [
    # Включает или отключает сбор с этого источника, обязательное поле
    'enabled'        => true,
    # Название слоя в источнике - тип слоя у нас, группировка данных из разных источников производится по типу слоя
    # обязательное поле
    'layers'         => [
        'temperature_2m_max'  => LayerDatatypes::TYPE_TEMPERATURE_MAX,
        'temperature_2m_min'  => LayerDatatypes::TYPE_TEMPERATURE_MIN,
        'temperature_2m_mean' => LayerDatatypes::TYPE_TEMPERATURE,
        'precipitation_sum'   => LayerDatatypes::TYPE_PRECIPITATION,
        'windspeed_10m_max'   => LayerDatatypes::TYPE_WIND_MAX,
    ],

    # Специфичные настройки для источника
    'historical_url' => env('DATA_SOURCE_OPEN_METEO_HISTORICAL', 'https://archive-api.open-meteo.com/v1/archive'),
    # Временно не используется
    'forecast_url'   => env('DATA_SOURCE_OPEN_METEO_FORECAST', 'https://api.open-meteo.com/v1/forecast'),
    'timezone'       => 'GMT',
];
