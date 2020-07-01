<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ActivityBargainHelp extends Model
{
    //
    protected $table = "activity_bargain_help";

    public function getCreatedAtAttribute($value)
    {
        return Carbon::createFromTimestamp(strtotime($value))
            // Leave this part off if you want to keep the property as
            // a Carbon object rather than always just returning a string
            ->toDateTimeString()
            ;
    }

}
