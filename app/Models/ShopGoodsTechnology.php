<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopGoodsTechnology extends Model
{
    const STATE_ON_SALE = 1;// 上架中
    const STATE_NOT_SALE = 0;// 已下架

    public function shop_goods()
    {
        return $this->hasOne(ShopGoods::class, 'id','goods_id');
    }

    // 是否上架
    public static function getSaleDispayMap()
    {
        return [
            self::STATE_ON_SALE => self::STATE_ON_SALE_STRING,
            self::STATE_NOT_SALE => self::STATE_NOT_SALE_STRING,
        ];
    }

}
