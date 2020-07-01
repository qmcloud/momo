<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopGoodsAttribute extends Model
{
    protected $table = "shop_goods_attribute";
    public $timestamps = false;
    protected $fillable = ['goods_id', 'attribute_id', 'value'];

    /**
     * 获取属性信息
     */
    public function get_attribute()
    {
        return $this->hasOne(ShopAttribute::class, 'id','attribute_id');
    }

    public static function getGoodsAttribute($where){
        return static::where($where)->get();
    }

}
