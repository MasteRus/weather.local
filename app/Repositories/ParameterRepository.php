<?php

namespace App\Repositories;

use App\Models\Parameter;

class ParameterRepository implements IParameterRepository
{
    /**
     * @param string $source
     * @param string $key
     * @param string|null $value
     * @return Parameter|null
     */
    public function findOrCreate(string $source, string $key, string $value = null): ?Parameter
    {
        $attributes = [
            'name'      => $key,
            'source'    => $source,
            'valuetype' => 'float',
        ];
        if ($value) {
            $attributes['units'] = $value;
        }

        return Parameter::firstOrCreate($attributes);
    }
}
