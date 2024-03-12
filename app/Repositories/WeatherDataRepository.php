<?php

namespace App\Repositories;

use App\Models\Location;
use App\Models\WeatherData;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class WeatherDataRepository implements IWeatherDataRepository
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
';
        $params = [$startDate, $finishDate, $location->id, $source];
        if ($key) {
            $query .= 'AND p.name = ?';
            $params[] = $key;
        }

        DB::delete($query, $params);
    }

    /**
     * @param int $count
     * @param Location $location
     * @param mixed $param
     * @param mixed $value
     * @param array $times
     * @param string $source
     */
    public function insertDataOpenMeteo(
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

    public function insertDataWeatherApi(array $values): void
    {
        WeatherData::insert($values);
    }

    /**
     * @param Location $location
     * @param array $params
     * @return Collection
     */
    public function getAverageData(Location $location, array $params): Collection
    {
        $startDate = $params['start_date'];
        $finishDate = $params['finish_date'];

        $query =  WeatherData::select('p.type as parameter', DB::raw('AVG(wd.value) as avgvalue'))
            ->from('weather_data as wd')
            ->leftJoin('parameters as p', 'wd.parameter_id', '=', 'p.id')
            ->whereBetween('wd.date', [$startDate, $finishDate])
            ->where('wd.location_id', $location->id)
            ->groupBy('wd.parameter_id', 'p.type');

        return $query->get();
    }
}
