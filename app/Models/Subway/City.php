<?php

namespace App\Models\Subway;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'subway_cities';

    public function lines()
    {
        return $this->hasMany(Line::class);
    }
}
