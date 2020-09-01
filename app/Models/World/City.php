<?php

namespace App\Models\World;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $primaryKey = 'ID';

    protected $table = 'world_city';

    public $timestamps = false;

    public function country()
    {
        return $this->belongsTo(Country::class, 'CountryCode', 'Code');
    }

    public static function options($id)
    {
        return static::where('ID', $id)->get()->map(function ($city) {

            return [$city->ID => $city->Name];

        })->flatten();
    }
}
