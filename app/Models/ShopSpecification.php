<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopSpecification extends Model
{

    protected $table = "shop_specification";
    public $timestamps = false;
    const STATE_ON_SEARCH = 1;// 可以检索
    const STATE_NOT_SEARCH = 0;// 停止检索
    const STATE_ON_SEARCH_STRING = '可以检索';
    const STATE_NOT_SEARCH_STRING = '停止检索';

    public function spec_items()
    {
        return $this->hasMany(ShopSpecItem::class, 'spec_id','id');
    }

    // 是否上架
    public static function getSearchDispayMap()
    {
        return [
            self::STATE_ON_SEARCH => self::STATE_ON_SEARCH_STRING,
            self::STATE_NOT_SEARCH => self::STATE_NOT_SEARCH_STRING,
        ];
    }
}
