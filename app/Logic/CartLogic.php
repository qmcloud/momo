<?php
/**
 * sqc @小T科技 2018.03.06
 *
 *
 */
namespace App\Logic;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Resources\ShopCart as ShopCartResource;
use App\Http\Resources\ShopGoods as ShopGoodsResource;
use App\Models\ShopCart;
use App\Models\ShopProduct;
use App\Models\ShopGoods;

class CartLogic
{
    public function __construct()
    {

    }

    static public function addCart($goodsInfo, $number, $type = 0)
    {
        if (empty(\Auth::user()->id)) {
            $user_id = 0;
        } else {
            $user_id = \Auth::user()->id;
        }

        $newCart = new ShopCart();
        $where = [
            'goods_id' => $goodsInfo->id,
            'uid' => $user_id
        ];
        if ($goodsInfo->checked_products) {
            $where['product_id'] = $goodsInfo->checked_products->id;
        }
        $info = $newCart->where($where)->first();
        if (!empty($info->goods_id)) {
            $info->number = $info->number + $number;
            // 库存超额判断
            if ($info->number > $goodsInfo->goods_number) {
                $info->number = $goodsInfo->goods_number;
            }
            return $info->save();
        }
        // 如果选择了规格
        if ($goodsInfo->checked_products) {
            $newCart->goods_sn = $goodsInfo->checked_products->goods_sn;
            $newCart->retail_price = $goodsInfo->checked_products->retail_price;
            $newCart->product_id = $goodsInfo->checked_products->id;
        } else {
            $newCart->goods_sn = $goodsInfo->goods_sn;
            $newCart->retail_price = $goodsInfo->retail_price;
        }
        $newCart->uid = $user_id;
        $newCart->goods_id = $goodsInfo->id;
        $newCart->goods_name = $goodsInfo->goods_name;
        $newCart->market_price = $goodsInfo->counter_price;
        $newCart->number = $number;
        $newCart->list_pic_url = $goodsInfo->primary_pic_url;
        return $newCart->save();
    }

    // 获取购物车列表
    public static function getCartList($where)
    {
        $list = ShopCart::where($where)->get();
        $outData['cartList'] = ShopCartResource::collection($list);
        $outData['cartTotal']['checkedGoodsCount'] = ShopCart::getGoodsCount($where);
        $outData['cartTotal']['checkedGoodsAmount'] = ShopCart::getGoodsAmountCount($where);
        return $outData;
    }

    public static function getCheckedGoodsList($uid)
    {
        $cartList = ShopCart::getCheckedGoodsList($uid);
        $checkedGoodsList = ShopCartResource::collection($cartList);
        $goodsTotalPrice = 0.00;
        foreach ($checkedGoodsList as $goodsVal) {
            $goodsTotalPrice = PriceCalculate($goodsTotalPrice, '+', PriceCalculate($goodsVal['retail_price'], '*', $goodsVal['number']));
        }
        $freightPrice = array_sum(array_pluck($checkedGoodsList, 'freight_price'));
        return [
            'checkedGoodsList' => $checkedGoodsList,// 商品列表
            'goodsTotalPrice' => $goodsTotalPrice,// 商品总价格
            'freightPrice' => $freightPrice,// 商品运费总和
            'orderTotalPrice' => PriceCalculate($goodsTotalPrice, '+', $freightPrice)
        ];
    }

    public static function getBuyGoodsById($goodsId, $number = 1, $format = 1, $productId = '')
    {
        if ($productId) {
            $products = ShopProduct::where(['id' => $productId])->get()->keyBy('goods_id');
        }
        $goodsInfos = ShopGoods::getGoodsList(['id' => $goodsId]);
        foreach ($goodsInfos as $item_info) {
            $product_goods_spec_item_names = '';
            $product_retail_price = 0;
            if ($productId) {
                $product_goods_spec_item_names = $products[$item_info->id]['goods_spec_item_names'];
                $product_retail_price = $products[$item_info->id]['retail_price'];
            }
            $checkedGoodsList[] = [
                "goods_id" => $item_info->id,
                "product_id" => $productId,
                "goods_name" => $item_info->goods_name . ' ' . $product_goods_spec_item_names,
                "market_price" => $item_info->counter_price,
                "retail_price" => $product_retail_price ? $product_retail_price : $item_info->retail_price,
                "number" => $number,
                'freight_price' => $item_info->freight_price,
                "primary_pic_url" => $format ? config('filesystems.disks.oss.url') . '/' . $item_info->primary_pic_url : $item_info->primary_pic_url,
                "list_pic_url" => $format ? config('filesystems.disks.oss.url') . '/' . $item_info->primary_pic_url : $item_info->primary_pic_url,
            ];
        }
        $goodsTotalPrice = 0.00;
        foreach ($checkedGoodsList as $goodsVal) {
            $goodsTotalPrice = PriceCalculate($goodsTotalPrice, '+', PriceCalculate($goodsVal['retail_price'], '*', $number));
        }
        $freightPrice = array_sum(array_pluck($checkedGoodsList, 'freight_price'));
        return [
            'checkedGoodsList' => $checkedGoodsList,// 商品列表
            'goodsTotalPrice' => $goodsTotalPrice,// 商品总价格
            'freightPrice' => $freightPrice,// 商品运费总和
            'orderTotalPrice' => PriceCalculate($goodsTotalPrice, '+', $freightPrice)
        ];
    }

    // 清空购物车
    public static function clearCart($uid)
    {
        return ShopCart::where([
            'uid' => $uid,
            'checked' => ShopCart::STATE_CHECKED
        ])->delete();
    }

}
