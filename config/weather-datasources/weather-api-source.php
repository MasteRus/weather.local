<?php

use App\Dictionaries\LayerDatatypes;

return [
    # Включает или отключает сбор с этого источника, обязательное поле
    'enabled'        => true,
    # Название слоя в источнике - тип слоя у нас, группировка данных из разных источников производится по типу слоя
    # обязательное поле
    'layers'       => [
        'avgtemp_c'      => LayerDatatypes::TYPE_TEMPERATURE,
        'mintemp_c'      => LayerDatatypes::TYPE_TEMPERATURE_MIN,
        'maxtemp_c'      => LayerDatatypes::TYPE_TEMPERATURE_MAX,
        'avghumidity'    => LayerDatatypes::TYPE_HUMIDITY,
        'totalprecip_mm' => LayerDatatypes::TYPE_PRECIPITATION,
    ],

    'apikey'       => env('DATA_SOURCE_WEATHER_API_KEY', ''),
    # Временно не используется
    'history_url'  => env('DATA_SOURCE_WEATHERAPI_HISTORICAL', 'http://api.weatherapi.com/v1/history.json'),
    'forecast_url' => env('DATA_SOURCE_WEATHERAPI_FORECAST', 'http://api.weatherapi.com/v1/forecast.json'),
];
