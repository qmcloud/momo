<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopAttributeCategory extends Model
{
    protected $table = "shop_attribute_category";
    public $timestamps = false;

    const STATE_ENABLED = 1;// 可用
    const STATE_NOT_ENABLED = 0;// 不可用
    const STATE_ENABLED_STRING = '可用';
    const STATE_NOT_ENABLED_STRING = '不可用';
    // 是否上架
    public static function getEnabledDispayMap()
    {
        return [
            self::STATE_ENABLED => self::STATE_ENABLED_STRING,
            self::STATE_NOT_ENABLED => self::STATE_NOT_ENABLED_STRING,
        ];
    }

}
