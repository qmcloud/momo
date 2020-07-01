<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ShopCart;
use App\Logic\AddressLogic;
use App\Logic\OrderLogic;
use App\Models\ShopOrder;

class MyOrderController extends ApiController
{


    // 订单列表
    public function orderList(Request $request)
    {
        // 先获取当前登录的用户信息
        if (empty(\Auth::user())) {
            return $this->failed('用户未登录', 401);
        }
        $user_id = \Auth::user()->id;
        $orderLogic = new OrderLogic();
        $where['uid'] = $user_id;
        if($request->input('statusTab')){
            $where['order_status'] = $request->input('statusTab');
        }
        if($request->input('v2')){
            $orderList['list'] = $orderLogic->getOrderList($where);
            $orderList['statusType'] = $orderLogic->getStatusDisplayMap();
        }else{
            $orderList = $orderLogic->getOrderList($where);
        }
        return $this->success($orderList);
    }

    // 订单详情
    public function orderDetail(Request $request)
    {
        // 先获取当前登录的用户信息
        if (empty(\Auth::user())) {
            return $this->failed('用户未登录', 401);
        }
        // 参数校验
        $validator = Validator::make($request->all(),
            [
                'orderId' => 'required',
            ],
            [
                'orderId.required' => '订单id参数缺失',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 403);
        }

        $user_id = \Auth::user()->id;
        $orderLogic = new OrderLogic();
        $where['uid'] = $user_id;
        $where['id'] = $request->orderId;
        $orderInfo = $orderLogic->getOrderDetail($where);
        return $this->success($orderInfo);
    }

    // 取消订单
    public function orderCancel(Request $request)
    {
        // 先获取当前登录的用户信息
        if (empty(\Auth::user())) {
            return $this->failed('用户未登录', 401);
        }
        $user_id = \Auth::user()->id;
        // 参数校验
        $validator = Validator::make($request->all(),
            [
                'orderId' => 'required',
            ],
            [
                'orderId.required' => '订单id参数缺失',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 403);
        }
        $where['uid'] = $user_id;
        $where['id'] = $request->orderId;
        $orderLogic = new OrderLogic();
        $re = $orderLogic->orderCancel($where);
        if($re){
            return $this->message('操作成功');
        }
    }

    // 物流详情
    public function orderExpress(Request $request)
    {
        // 先获取当前登录的用户信息
        if (empty(\Auth::user())) {
            return $this->failed('用户未登录', 401);
        }
        $user_id = \Auth::user()->id;
    }

}