<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopGoodsIssue extends Model
{
    //
    protected $table = "shop_goods_issue";

    public static function getGoodsIssue($where){
        return static::where($where)->get();
    }

}
