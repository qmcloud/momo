<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ActivityBargainJoin extends Model
{
    const STATE_ON = 1;
    const STATE_FAIL = 2;
    const STATE_OVER = 3;
    const STATE_ON_STRING = '参与中';
    const STATE_FAIL_STRING = '活动结束参与失败';
    const STATE_OVER_STRING = '活动结束参与成功';
    const ORDER_ADD = 1;
    const NOT_ORDER_ADD = 0;

    //
    protected $table = "activity_bargain_join";

    public function get_helps()
    {
        return $this->hasMany(ActivityBargainHelp::class,'join_id','id');
    }

    public function product()
    {
        return $this->hasOne(ShopProduct::class, 'id','product_id');
    }

    public function goods()
    {
        return $this->hasOne(ShopGoods::class, 'id','goods_id');
    }

    public function bargain()
    {
        return $this->hasOne(ActivityBargain::class, 'id','bargain_id');
    }


    public static function getStatusDisplayMap()
    {
        return [
            self::STATE_ON => self::STATE_ON_STRING,
            self::STATE_FAIL => self::STATE_FAIL_STRING,
            self::STATE_OVER => self::STATE_OVER_STRING,
        ];
    }
}
