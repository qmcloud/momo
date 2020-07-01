<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Order;
use Validator;
use App\Models\OrderGood;
use App\Logic\OrderLogic;


class OrderInfoController extends ApiController
{
    private $statusConfig = [
        [10], [22], [26, 32], [40]
    ];

    // 订单列表
    public function getMyOrderList(Request $request)
    {

        // 先获取当前登录的用户信息   
        $user_id = \Auth::user()->id;
        if (empty($user_id)) {
            return $this->failed('用户未登录', 401);
        }
        $condition['uid'] = $user_id;
        $conditionIn= $this->statusConfig[$request->input('status', 0)];
        $orders_info = Order::getOrderAndOrderGoodsList($condition,$conditionIn);

        $out_info = [];
        foreach ($orders_info as $k => &$order) {
            $out_info['logisticsMap'][$order->id] = [
                "address" => $order->addr_detail,
                "cityId" => $order->aid_c,
                "dateUpdate" => $order->updated_at->format('Y-m-d H:i:s'),
                "districtId" => $order->aid_a,
                "id" => $order->id,
                "linkMan" => $order->user_name,
                "mobile" => $order->user_mobile,
                "provinceId" => $order->aid_p,
                "status" => $order->status,
                "type" => 0
            ];
            $out_info['orderList'][] = [
                "amount" => $order->total_price,
                "amountLogistics" => 0.00,
                "amountReal" => $order->pay_price,
                "dateAdd" => $order->created_at->format('Y-m-d H:i:s'),
                "dateClose" => $order->pay_date,
                "goodsNumber" => $order->orderGoods->sum('num'),
                "hasRefund" => false,
                "id" => $order->id,
                "isPay" => false,
                "orderNumber" => $order->order_sn,
                "remark" => $order->remark,
                "status" => $order->status,
                "statusStr" => Order::getStatusDisplayMap()[$order->status],
                "type" => 0,
                "uid" => $order->uid,
            ];

            $order_goods = [];
            foreach ($order->orderGoods as $goods_k => $goods) {
                $order_goods[] = [
                    "amount" => $goods->goods_price,
                    "goodsId" => $goods->goods_id,
                    "goodsName" => $goods->goods_name,
                    "id" => $goods->id,
                    "number" => $goods->num,
                    "orderId" => $order->id,
                    "pic" => config('filesystems.disks.oss.url') . '/' . $goods->goods_image,
                    "uid" => $order->uid,
                ];
            }
            $out_info['goodsMap'][$order->id] = $order_goods;
        }
        if ($out_info) {
            // 返回订单信息
            return $this->success($out_info);
        }
        // 没有查到订单信息 返回空数据错误
        //add_my_log('Orderinfo','用户（id:'.\Auth::user()->id.'）没有符合条件的订单',1,'订单内容：没有符合条件的订单','OrderinfoController@list');
        return $this->failed('没有符合条件的订单', 401);
    }

    /**
     * 取消订单
     * @param Request $request
     */
    public function cancelOrder(Request $request)
    {
        // 验证规则
        $validator = Validator::make($request->all(),
            [
                'orderId' => 'required'
            ],
            [
                'orderId.required' => '订单号缺失'
            ]
        );
        if ($validator->fails()) {
            // add_my_log('Orderinfo','用户（id:'.\Auth::user()->id.'）操作订单不存在',1,'订单号：'.$order_id,'OrderinfoController@cancelOrder');
            return $this->failed($validator->errors(), 401);
        }
        $condition['id'] = $request->orderId;
        $condition['uid'] = \Auth::user()->id;
        $order_info = Order::where($condition)->first();
        $order_info_arr = $order_info->toArray();
        if (empty($order_info_arr)) {
            // add_my_log('Orderinfo','用户（id:'.\Auth::user()->id.'）操作订单不存在',1,'订单内容：空','OrderinfoController@cancelOrder');
            return $this->failed('订单不存在', 401);
        }
        $order_info->status = Order::STATUS_INVALID;
        $order_info->save();
        return $this->message('取消成功');
    }

    /**
     * 我的订单统计
     * @param Request $request
     */
    public function getMyOrderStatistics(Request $request)
    {
        // 先获取当前登录的用户信息
        $user_id = \Auth::user()->id;
        if (empty($user_id)) {
            return $this->failed('用户未登录', 401);
        }
        $statistics = [
            "count_id_no_transfer" => Order::countOrder(['uid' => $user_id, 'status' => Order::STATUS_ALREADY_PAID]),
            "count_id_close" => Order::countOrder(['uid' => $user_id, 'status' => Order::STATUS_INVALID]),
            "count_id_no_pay" => Order::countOrder(['uid' => $user_id, 'status' => Order::STATUS_WAIT_PAY]),
            "count_id_no_confirm" => Order::countOrder(['uid' => $user_id, 'status' => Order::STATUS_DELIVERING]),
            "count_id_success" => Order::countOrder(['uid' => $user_id, 'status' => Order::STATUS_COMPLETED])
        ];
        return $this->success($statistics);
    }
}