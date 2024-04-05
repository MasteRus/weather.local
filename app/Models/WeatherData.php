<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WeatherData extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'location_id',
        'parameter_id',
        'value',
        'date',
    ];

    public function parameter() : HasOne
    {
        return $this->hasOne(Parameter::class);
    }

    public function location() : HasOne
    {
        return $this->hasOne(Location::class);
    }
}
