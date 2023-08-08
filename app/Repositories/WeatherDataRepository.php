<?php

namespace App\Repositories;

use App\Models\Location;
use App\Models\WeatherData;
use Illuminate\Support\Facades\DB;

class WeatherDataRepository
{
    /**
     * @param string $startDate
     * @param string $finishDate
     * @param Location $location
     * @param string $source
     * @param null $key
     */
    public function purgeOldData(
        string $startDate,
        string $finishDate,
        Location $location,
        string $source,
        $key = null
    ): void {
        $query = '
DELETE wd FROM weather_data wd
LEFT JOIN parameters p ON p.id = wd.parameter_id
WHERE wd.`date` BETWEEN ? AND ?
AND (wd.`location_id` = ? and wd.`source` = ?)
AND p.name = ?';

        DB::delete($query, [$startDate, $finishDate, $location->id, $source, $key]);
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

    /**
     * @param Location $location
     * @param array $params
     * @return array
     */
    public function getAverageData(Location $location, array $params): array
    {
        $startDate = $params['start_date'];
        $finishDate = $params['finish_date'];

        $query = '
SELECT p.name as parameter, AVG(wd.value) as avgvalue
FROM weather_data wd
LEFT JOIN parameters p on wd.parameter_id = p.id
WHERE wd.`date` BETWEEN ? AND ?
GROUP BY wd.parameter_id
';
        $result = DB::select($query, [$startDate, $finishDate]);
        return $result;
    }
}
