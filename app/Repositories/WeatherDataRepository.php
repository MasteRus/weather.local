<?php

namespace App\Repositories;

use App\Models\Location;
use App\Models\WeatherData;

class WeatherDataRepository
{
    /**
     * @param string $startDate
     * @param string $finishDate
     * @param Location $location
     * @param string $source
     */
    public function purgeOldData(string $startDate, string $finishDate, Location $location, string $source): void
    {
        WeatherData::whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $finishDate)->where(
                [
                    'source'      => $source,
                    'location_id' => $location->id
                ]
            )->delete();
    }

    /**
     * @param int $count
     * @param Location $location
     * @param mixed $param
     * @param mixed $value
     * @param array $times
     * @param string $source
     */
    public function insertData(
        int $count,
        Location $location,
        mixed $param,
        mixed $value,
        array $times,
        string $source
    ): void {
        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $data[] = [
                'location_id'  => $location->id,
                'parameter_id' => $param->id,
                'value'        => $value[$i],
                'date'         => $times[$i],
                'source'       => $source
            ];
        }
        WeatherData::insert($data);
    }
}
