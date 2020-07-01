<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class ShopFeedback extends Model
{
    const STATUS_ON = 1;
    const STATUS_ACCEPT = 2;
    const STATUS_IGNORE = 0;

    const STATUS_ON_STRING = '待审阅';
    const STATUS_ACCEPT_STRING = '反馈采纳';
    const STATUS_IGNORE_STRING = '反馈忽略';
    public static $Option = [
        0=>'请选择',
        1=>'产品建议',
        2=>'功能异常',
        3=>'功能改进',
        4=>'商品购买流程',
        5=>'订单相关模块',
        6=>'展示效果',
        7=>'其他',
    ];
    protected $table = 'shop_feedback';

    // 反馈状态管理
    public static function getStatusDispayMap()
    {
        return [
            self::STATUS_ON => self::STATUS_ON_STRING,
            self::STATUS_ACCEPT => self::STATUS_ACCEPT_STRING,
            self::STATUS_IGNORE => self::STATUS_IGNORE_STRING,
        ];
    }
}