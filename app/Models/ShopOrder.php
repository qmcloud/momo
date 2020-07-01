<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ShopOrder extends Model
{
    const STATUS_INVALID = 0;//订单关闭或无效，用户取消也置0
    const STATUS_WAIT_PAY = 10;//订单待支付
    const STATUS_ALREADY_PAID = 22;//订单支付完成
    const STATUS_DELIVERING = 32;//确认配送
    const STATUS_COMPLETED = 40;//订单收货完成
    const STATUS_INVALID_STRING = '订单无效';//订单关闭或无效，用户取消也置0
    const STATUS_WAIT_PAY_STRING = '待支付';//订单待支付
    const STATUS_ALREADY_PAID_STRING = '支付完成';//订单支付完成
    const STATUS_DELIVERING_STRING = '已配送';//确认配送
    const STATUS_COMPLETED_STRING = '已收货';//订单收货完成

    const SHIPING_STATUS_WAIT_SEND = 0;// 待发货
    const SHIPING_STATUS_SEND = 10;// 已发货
    const SHIPING_STATUS_SENDED = 20;// 已收货

    // 核验订单状态
    const PAY_WAIT = 0;// 待支付下单
    const PAY_WAIT_CHECK = 1;// 已支付下单 待核验
    const PAY_CHECKED_OK = 2;// 核验完成
    const PAY_CHECKED_ERROR = 4;// 核验失败或出现问题

    //
    protected $table = "shop_order";
    protected $fillable = [
        'order_sn',
        'uid',
        'order_status',
        'shipping_status',
        'pay_status',
        'consignee',
        'country',
        'province',
        'city',
        'district',
        'address',
        'mobile',
        'postscript',
        'pay_name',
        'pay_id',
        'actual_price',
        'order_price',
        'goods_price',
        'add_time',
        'confirm_time',
        'pay_time',
        'freight_price',
        'callback_status',
        'coupon_id',
        'coupon_price',
        'trade_no'
    ];

    public static function getStatusDisplayMap()
    {
        return [
            self::STATUS_INVALID => self::STATUS_INVALID_STRING,
            self::STATUS_WAIT_PAY => self::STATUS_WAIT_PAY_STRING,
            self::STATUS_ALREADY_PAID => self::STATUS_ALREADY_PAID_STRING,
            self::STATUS_DELIVERING => self::STATUS_DELIVERING_STRING,
            self::STATUS_COMPLETED => self::STATUS_COMPLETED_STRING,
        ];
    }

    // 关联订单商品
    public function orderGoods()
    {
        return $this->hasMany(ShopOrderGoods::class, 'order_id');
    }

    /**
     * 获取订单 及 订单商品列表
     */
    public static function getOrderAndOrderGoodsList($condition)
    {
        return static::with('orderGoods')->where($condition)->orderBy('id', 'desc')->get();
    }

    /**
     * 获取订单数量
     */
    public static function countOrder($condition)
    {
        $n = static::where($condition)->count();
        return $n ? $n : 0;
    }

    public function getAddTimeAttribute($value)
    {
        return Carbon::createFromTimestamp(strtotime($value))
            // Leave this part off if you want to keep the property as
            // a Carbon object rather than always just returning a string
            ->toDateTimeString();
    }
}
