<?php

namespace App\Models\World;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $table = 'world_language';

    public $timestamps = false;

    public function country()
    {
        return $this->belongsTo(Country::class, 'CountryCode', 'Code');
    }
}
