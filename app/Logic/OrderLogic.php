<?php
/**
 * User: sqc
 * Date: 18-6-28
 * Time: 下午5:12
 */

namespace App\Logic;

use App\User;
use Illuminate\Support\Facades\DB;
use App\Models\ShopOrder;
use App\Http\Resources\ShopOrder as ShopOrderResource;

class OrderLogic
{
    public  function getStatusDisplayMap()
    {
        return [
            '0' => '全部',
            ShopOrder::STATUS_WAIT_PAY => ShopOrder::STATUS_WAIT_PAY_STRING,
            ShopOrder::STATUS_ALREADY_PAID => ShopOrder::STATUS_ALREADY_PAID_STRING,
            ShopOrder::STATUS_COMPLETED => ShopOrder::STATUS_COMPLETED_STRING,
        ];
    }
    public function getOrderList($where){
        $list = ShopOrder::getOrderAndOrderGoodsList($where);
        return ShopOrderResource::collection($list);
    }

    public function getOrderDetail($where)
    {
        $info = ShopOrder::with('orderGoods')->where($where)->first();
        return new ShopOrderResource($info);
    }

    public  function orderCancel($where){
        return ShopOrder::where($where)->update(['order_status' => ShopOrder::STATUS_INVALID]);;
    }

    public function completeOrder($orderID)
    {
    }
}