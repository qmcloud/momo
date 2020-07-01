<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopAddress extends Model
{
    const STATUS_ON = 2;
    const STATUS_OFF = 1;
    const DEFAULT_ON = 1;
    const DEFAULT_OFF = 0;

    //
    protected $table = "shop_address";

    static public function getList($where){
        $where = array_merge(['status'=>self::STATUS_ON],$where);
        return static::where($where)->get();
    }

    // 获取默认地址
    static public function getDefault($uid){
        $where = array_merge(['status'=>self::STATUS_ON,'is_default'=>self::DEFAULT_ON,'uid'=>$uid]);
        return static::where($where)->first();
    }

    // 获取单一地址
    static public function getOne($where){
        $where = array_merge(['status'=>self::STATUS_ON],$where);
        return static::where($where)->first();
    }
}
