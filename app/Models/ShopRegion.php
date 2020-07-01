<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopRegion extends Model
{

    //
    protected $table = "shop_region";
    public $timestamps = false;

    static public function getList($where){
        return static::where($where)->get();
    }

    static public function getOne($where){
        return static::select(['name','id','type'])->where($where)->first();
    }
}
