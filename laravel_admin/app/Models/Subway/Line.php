<?php

namespace App\Models\Subway;

use Illuminate\Database\Eloquent\Model;

class Line extends Model
{
    protected $table = 'subway_lines';

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function stops()
    {
        return $this->hasMany(Stop::class);
    }
}
