<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopProduct extends Model
{

    protected $table = "shop_product";
    public $timestamps = false;
    protected $fillable = ['goods_specification_ids', 'goods_sn', 'goods_number','retail_price','goods_specification_names','goods_spec_item_ids','goods_spec_item_names'];

    public function specifications()
    {
        return $this->hasMany(ShopGoodsSpecification::class, 'goods_id');
    }
}
