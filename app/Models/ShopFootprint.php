<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\ShopFootprint as ShopFootprintResource;

class ShopFootprint extends Model
{
    const MAX_SHOW_NUM = 20;
    protected $table = "shop_footprint";

    public function shop_goods()
    {
        return $this->hasOne(ShopGoods::class,'id','goods_id');
    }

    static public function getList($where){
        return ShopFootprintResource::collection(
            static::where($where)->take(static::MAX_SHOW_NUM)
                ->orderBy('id', 'desc')
//                ->groupBy('goods_id')
                ->get()
        );
    }

}
