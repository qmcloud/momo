<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopUserCoupon extends Model
{
    const STATUS_CAN_USE = 10;//可以使用
    const STATUS_USED = 20;//已使用
    const STATUS_CAN_USE_STRING = '立即使用';
    const STATUS_USED_STRING = '已使用';
    //
    protected $table = 'shop_user_coupon';

    // 根据用户的uid获取用户的优惠信息
    static public function getUserCouponInfoByUid($uid)
    {
        return static::with('getCoupon')->where([
            'uid' => $uid,
            'use_status' => static::STATUS_CAN_USE
        ])->get();
    }

    public static function getUseStatusMap()
    {
        return [
            self::STATUS_CAN_USE => self::STATUS_CAN_USE_STRING,
            self::STATUS_USED => self::STATUS_USED_STRING,
        ];
    }

    // 关联优惠模型
    public function getCoupon()
    {
        return $this->hasOne(ShopCoupon::class, 'id', 'coupon_id');
    }
}
