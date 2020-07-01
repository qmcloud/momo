<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopCoupon extends Model
{
	static public $send_types = [
		1 => '直接领取',
	];
	const STATUS_ON = 1;
	const STATUS_OFF = 0;
	const REWARD_DIRECT = 1;

	const ESPIRE_TYPE_DAY  = 1; // 领取后N天过期
	const ESPIRE_TYPE_DATE  = 2; // 指定有效期

	const TYPE_REDUCE = 1; // 满减
	const TYPE_DISCOUNT = 2;// 折扣

    //
    protected $table = "shop_coupon";

    /*
    	获取状态信息
    */
    static public function getStatusDispayMap(){
    	return [
    		static::STATUS_ON =>'可领取',
    		static::STATUS_OFF =>'已下架',
    	];
    }

    /*
    	获取状态信息
    */
    static public function getExpireDispayMap(){
    	return [
    		static::ESPIRE_TYPE_DAY =>'领取后N天过期',
    		static::ESPIRE_TYPE_DATE =>'指定有效期',
    	];
    }

	/*
    	获取状态信息
    */
    static public function getTypeDispayMap(){
    	return [
    		static::TYPE_REDUCE =>'满减',
    		static::TYPE_DISCOUNT =>'折扣',
    	];
    }

    // 关联优惠模型
    public function getUserCoupon()
    {
        return $this->hasOne(ShopUserCoupon::class, 'coupon_id', 'id');
    }

}
