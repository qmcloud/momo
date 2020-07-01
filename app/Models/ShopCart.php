<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopCart extends Model
{
    const STATE_CHECKED = 1;
    const STATE_NOT_CHECKED = 0;
    const STATE_CHECKED_STRING = '选中';
    const STATE_NOT_CHECKED_STRING = '正常';

    //
    protected $table = "shop_cart";
    public $timestamps = false;

    public function shop_goods()
    {
        return $this->hasOne(ShopGoods::class,'id','goods_id');
    }

    public static function getStateDisplayMap()
    {
        return [
            self::STATE_CHECKED => self::STATE_CHECKED_STRING,
            self::STATE_NOT_CHECKED => self::STATE_NOT_CHECKED_STRING
        ];
    }

    public function checked_products()
    {
        return $this->hasOne(ShopProduct::class, 'id','product_id');
    }

    public static function getGoodsCount($where){
        return static::where($where)->sum('number');
    }

    public static function getGoodsAmountCount($where){
        $cartData =  static::where($where)->get();
        $goodsTotalPrice = 0.00;
        if($cartData){
            foreach($cartData as $item){
                $goodsTotalPrice = PriceCalculate($goodsTotalPrice,'+',PriceCalculate($item['retail_price'],'*',$item['number']));
            }
        }
        return $goodsTotalPrice;
    }

    // 获取选中的商品
    public static function getCheckedGoodsList($uid){
        $where['uid'] = $uid;
        $where['checked'] = self::STATE_CHECKED;
        return static::with('shop_goods')->where($where)->get();
    }

}
