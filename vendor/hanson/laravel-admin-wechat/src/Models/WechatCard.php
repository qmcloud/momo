<?php


namespace Hanson\LaravelAdminWechat\Models;


use Hanson\LaravelAdminWechat\Exceptions\WechatException;
use Hanson\LaravelAdminWechat\Facades\ConfigService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class WechatCard extends Model
{
    protected $guarded = [];

    protected $casts = ['sku' => 'array', 'date_info' => 'array'];

    protected $appends = ['card_type_readable', 'code_type_readable'];

    const CARD_TYPE_MAP = [
        'GROUPON' => '团购券',
        'DISCOUNT' => '折扣券',
        'GIFT' => '礼品券',
        'CASH' => '代金券',
        'GENERAL_COUPON' => '通用券',
        'MEMBER_CARD' => '会员卡',
        'SCENIC_TICKET' => '景点门票',
        'MOVIE_TICKET' => '电影票',
        'BOARDING_PASS' => '飞机票',
        'MEETING_TICKET' => '会议门票',
        'BUS_TICKET' => '汽车票',
    ];

    const CODE_TYPE_MAP = [
        'CODE_TYPE_TEXT' => '文本 ',
        'CODE_TYPE_BARCODE' => '一维码 ',
        'CODE_TYPE_QRCODE' => ' 二维码',
        'CODE_TYPE_ONLY_QRCODE' => '二维码无code显示',
        'CODE_TYPE_ONLY_BARCODE' => '一维码无code显示',
    ];

    const DATE_INFO_TYPE_MAP = [
        'DATE_TYPE_FIX_TIME_RANGE' => '固定日期区间',
        'DATE_TYPE_FIX_TERM' => '固定时长',
        'DATE_TYPE_PERMANENT' => '永久有效',
    ];

    public function getCardTypeReadableAttribute()
    {
        if ($cardType = $this->attributes['card_type']) {
            return self::CARD_TYPE_MAP[$cardType];
        }
    }

    public function getCodeTypeReadableAttribute()
    {
        if ($cardType = $this->attributes['card_type']) {
            return self::CARD_TYPE_MAP[$cardType];
        }
    }
}
