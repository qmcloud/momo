<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SpecialItem
 * adv title img link
 * @package App\Models
 */
class SpecialItem extends Model
{
    const IFSHOW_YES = 1;
    const IFSHOW_NO = 0;
    const IFSHOW_YES_STRING = '正常';
    const IFSHOW_NO_STRING = '禁用';
    //
    protected $table = 'special_item';


    public static function getIfShowDisplayMap()
    {
        return [
            self::IFSHOW_YES => self::IFSHOW_YES_STRING,
            self::IFSHOW_NO => self::IFSHOW_NO_STRING,
        ];
    }

    public static function getIfShowDisplayConfig()
    {
        return [
            'on' => [
                'value' => self::IFSHOW_YES,
                'text' => self::IFSHOW_YES_STRING,
            ],
            'off' => [
                'value' => self::IFSHOW_NO,
                'text' => self::IFSHOW_NO_STRING,
            ],

        ];
    }

    public function carousels()
    {
        return $this->hasMany(Carousel::class, 'spec_item_id');
    }

    public static function getItemTypes()
    {
        return [
            'adv' => '轮播', // 轮播
            'moduleA' => '模板A',
            'moduleB' => '模板B',// 商品
            'moduleC' => '模板C',// 专题滑块
            'moduleD' => '模板D',
        ];
    }

    public static function getItemDataTypes()
    {
        return [
            'goods' => '商品iD',
            'special' => '专题id',
            'link' => '链接',
        ];
    }
}
