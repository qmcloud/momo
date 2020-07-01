<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Logic\ShopCouponLogic;
use App\Http\Resources\ShopCouponCenter;
use App\Http\Resources\ShopMyCoupon;
use Illuminate\Support\Facades\Validator;

class ShopCouponController extends ApiController
{
    // 获取领券中心优惠券列表
    public function getCouponList(Request $request)
    {
        // 先获取当前登录的用户信息
        if (empty(\Auth::user())) {
            return $this->failed('用户未登录', 401);
        }
        $user_id = \Auth::user()->id;
        return ShopCouponCenter::collection(ShopCouponLogic::getCouponListForCenter($user_id))->additional(['code' => 200]);
    }

    // 获取我的优惠券列表
    public function getMyCouponList(Request $request)
    {
        // 先获取当前登录的用户信息
        if (empty(\Auth::user())) {
            return $this->failed('用户未登录', 401);
        }
        $user_id = \Auth::user()->id;
        return ShopMyCoupon::collection(ShopCouponLogic::getCouponListByUid($user_id))->additional(['code' => 200]);
    }

    // 获取我的优惠券列表
    public function getCoupon(Request $request)
    {
        // 先获取当前登录的用户信息
        if (empty(\Auth::user())) {
            return $this->failed('用户未登录', 401);
        }
        // 参数校验
        $validator = Validator::make($request->all(),
            [
                'id' => 'required',
            ],
            [
                'id.required' => '参数缺失',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 403);
        }
        $user_id = \Auth::user()->id;
        $re = ShopCouponLogic::rewardCouponById($user_id,$request->id);
        if(!$re['error']){
            return $this->success($re['data']);
        };
        return $this->failed($re['error'], 403);
    }


}