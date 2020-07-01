<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopCollect extends Model
{
    const STATE_ATTENTION = 1;
    const STATE_NOT_ATTENTION = 0;
    const STATE_ATTENTION_STRING = '关注';
    const STATE_NOT_ATTENTION_STRING = '未关注';

    //
    protected $table = "shop_collect";
    public $timestamps = false;

    public function shop_goods()
    {
        return $this->hasOne(ShopGoods::class,'id','value_id');
    }

    public static function getStateDisplayMap()
    {
        return [
            self::STATE_ATTENTION => self::STATE_ATTENTION_STRING,
            self::STATE_NOT_ATTENTION => self::STATE_NOT_ATTENTION_STRING
        ];
    }

    public static function getCollectDetail($where){
        return static::where($where)->first();
    }

    public static function getList($where){
        return static::where(array_merge(['is_attention'=>static::STATE_ATTENTION],$where))->orderBy('id','desc')->get();
    }
}
