<?php

namespace App\Models\Subway;

use Illuminate\Database\Eloquent\Model;

class Stop extends Model
{
    protected $table = 'subway_stops';

    public function line()
    {
        return $this->belongsTo(Line::class);
    }
}
