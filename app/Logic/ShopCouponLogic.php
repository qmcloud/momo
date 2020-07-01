<?php
/**
 * sqc @小T科技 2018.12.24
 *
 *
 */
namespace App\Logic;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\ShopCoupon;
use App\Models\ShopUserCoupon;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class ShopCouponLogic
{

    public function __construct()
    {

    }

    // 领券中心
    static public function getCouponListForCenter($userID, $where = [])
    {
        $where = array_merge([
            'status' => ShopCoupon::STATUS_ON,
            'send_type' => ShopCoupon::REWARD_DIRECT,
        ], $where);
        $list = ShopCoupon::with(['getUserCoupon' => function ($query) use ($userID) {
            $query->where('uid', '=', $userID);
        }])->where($where)->orderBy('sort_order','desc')->get();
        return $list;
    }

    // 获取用户的领取的优惠
    static public function getCouponListByUid($uid)
    {
        $where = [
            'uid' => $uid,
            'use_status' => ShopUserCoupon::STATUS_CAN_USE
        ];
        $list = ShopUserCoupon::has('getCoupon')->where($where)->get();
        return $list;
    }

    // 根據优惠券id领券
    static public function rewardCouponById($uid, $id)
    {
        DB::beginTransaction();
        try {
            $now = Carbon::now();
            // 查询
            $coupon = ShopCoupon::find($id);
            if (empty($coupon) || !isset($coupon->id)) {
                throw new \Exception('优惠券不存在');
            }
            if ($now > $coupon->send_end_date || $now < $coupon->send_start_date) {
                throw new \Exception('现在优惠券暂不可领取');
            }
            if ($coupon->reward_num >= $coupon->total_num) {
                throw new \Exception('优惠券库存不够了');
            }
            // 查询用户领取状况
            $reward = ShopUserCoupon::where(['uid' => $uid, 'coupon_id' => $id])->first();
            if ($reward && $reward->coupon_number >= $coupon->limit_num) {
                // 领取个数达到上限
                throw new \Exception('领取个数达到上限');
            }
            if (empty($reward)) {
                $reward = new ShopUserCoupon();
                $reward->coupon_id = $id;
                $reward->coupon_number = 1;
                $reward->uid = $uid;
                $reward->reward_time = Carbon::now();
                $reward->use_status = ShopUserCoupon::STATUS_CAN_USE;
                $reward->start_time = Carbon::now();
                if ($coupon->expire_type == ShopCoupon::ESPIRE_TYPE_DAY) {
                    $reward->end_time = Carbon::now()->addDays($coupon->expire_day);
                } else {
                    $reward->end_time = $coupon->use_end_date;
                }
            } else {
                $reward->use_status = ShopUserCoupon::STATUS_CAN_USE;
                $reward->coupon_number += 1;
            }
            //第1步 领取
            $re1 = $reward->save();
            if (!$re1) {
                throw new \Exception('领取失败');
            }
            $re2 = $coupon->increment('reward_num', 1);
            if (!$re2) {
                throw new \Exception('数据保存失败');
            }
            DB::commit();
            return ['error' => 0,'data'=>['can_get_num'=>($coupon->limit_num - $reward->coupon_number)]];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage(),'data'=>[]];
        }

    }

    // 根据订单数据获取可以使用的优惠券
    static public function getAvailableCouponListByGoodsPrice($goodsTotalPrice,$uid)
    {
        $list = self::getCouponListByUid($uid);
        $avalib_list = [];// 可以使用的优惠券
        $error = [];
        foreach($list as $item){
            try{
                self::validateCoupon($item->getCoupon,1,$goodsTotalPrice);
                self::validateUserCoupon($item,1);
                $avalib_list[] = $item;
            }catch (\Exception $e){
            }
        }
        return collect($avalib_list);
    }

    // 获取选中的优惠信息
    static public function checkedCoupon($uid,$couponid,$goodsTotalPrice){
        if(empty($couponid)){
            return ['checkedCoupon'=>[],'couponPrice'=>0.00];
        }
        $where = [
            'uid' => $uid,
            'coupon_id' => $couponid,
            'use_status' => ShopUserCoupon::STATUS_CAN_USE
        ];
        $couponInfo = ShopUserCoupon::has('getCoupon')->where($where)->first();
        try{
            self::validateCoupon($couponInfo->getCoupon,1,$goodsTotalPrice);
            self::validateUserCoupon($couponInfo,1);
            $price = self::calculate($couponInfo['getCoupon'],$goodsTotalPrice);
            return ['checkedCoupon'=>$couponInfo,'couponPrice'=>$price];
        }catch (\Exception $e){
            return ['checkedCoupon'=>[],'couponPrice'=>0.00];
        }

    }

    // 使用优惠劵
    static public function useCoupon($uid,$couponid){
        $where = [
            'uid' => $uid,
            'coupon_id' => $couponid,
            'use_status' => ShopUserCoupon::STATUS_CAN_USE
        ];
        $couponInfo = ShopUserCoupon::where($where)->first();
        if(empty($couponInfo)){
            throw new \Exception('没有该优惠');
        }
        if($couponInfo->coupon_number <=0){
            throw new \Exception('已使用该优惠');
        }
        $couponInfo->coupon_number -= 1;
        if($couponInfo->coupon_number<=0){
            $couponInfo->use_status = ShopUserCoupon::STATUS_USED;// 使用完成标记
        }
        $re = $couponInfo->save();
        if(!$re){
            throw new \Exception('优惠券使用失败');
        }
    }

    // 计算优惠价格
    static private function calculate($coupon,$goodsTotalPrice = 0.00){
        $couponPrice = 0.00;
        switch ($coupon['type']) {
            case ShopCoupon::TYPE_REDUCE:
                $tmp = PriceCalculate($goodsTotalPrice, '-', $coupon['type_money']);
                if($tmp > 0){
                    $couponPrice = $coupon['type_money'];
                }
                break;

            default:
                $tmp = 100 - $coupon['type_money'];
                if($tmp < 0 ){
                    $tmp = 0;
                }
                $tmp1 = PriceCalculate($goodsTotalPrice, '*', $tmp);
                $couponPrice = PriceCalculate($tmp1, '/', 100,1);
                break;
        }
        return $couponPrice;
    }

    // 验证优惠信息
    static private function validateCoupon($coupon,$is_use =1,$goodsTotalPrice = 0.00){
        if(empty($coupon)){
            throw new \Exception('查无该优惠');
        }
        $tmp = self::calculate($coupon,$goodsTotalPrice);
        if($tmp<=0){
            throw new \Exception('不足以使用该优惠');
        }
        if($coupon['status'] != ShopCoupon::STATUS_ON){
            throw new \Exception('该优惠券暂不可以使用');
        }
        // 使用优惠券列表需要校验的数据
        if($is_use){
            if($goodsTotalPrice>$coupon['max_amount'] || $goodsTotalPrice<$coupon['min_amount']){
                throw new \Exception('使用价格区间不符合');
            }
        }
    }

    // 验证用户优惠信息
    static private function validateUserCoupon($user_coupon,$is_use =1){
        $now = Carbon::now();
        // 使用优惠券列表需要校验的数据
        if($is_use){
            if($user_coupon['coupon_number']<=0){
                throw new \Exception('已使用该优惠');
            }
            if($user_coupon['use_status'] != ShopUserCoupon::STATUS_CAN_USE){
                throw new \Exception('该优惠券您已经使用或失效');
            }
            if($now < $user_coupon['start_time'] || $now > $user_coupon['end_time']){
                throw new \Exception('目前你还不能使用该优惠');
            }
        }
    }


}
