<?php

namespace App\Repositories;

use App\Models\Parameter;

class ParameterRepository implements IParameterRepository
{
    /**
     * @param string $key
     * @return mixed
     */
    public function findOrCreate(string $key)
    {
        $param = Parameter::firstOrCreate(
            [
                'name' => $key,
                'valuetype' => 'float'
            ]
        );

        return $param;
    }
}
