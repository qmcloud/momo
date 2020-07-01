<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopTopic extends Model
{
    const STATE_OFFSHOW = 0;
    const STATE_SHOW = 1;
    const STATE_OFFSHOW_STRING = '下架';
    const STATE_SHOW_STRING = '展示';

    protected $table = "shop_topic";

    public static function getStateDispayMap()
    {
        return [
            self::STATE_OFFSHOW => self::STATE_OFFSHOW_STRING,
            self::STATE_SHOW => self::STATE_SHOW_STRING,
        ];
    }


    public function getItemPicUrlAttribute($pictures)
    {
        if (is_string($pictures)) {
            return json_decode($pictures, true);
        }

        return $pictures;
    }

    public function setItemPicUrlAttribute($pictures)
    {
        if (is_array($pictures)) {
            $this->attributes['item_pic_url'] = json_encode($pictures);
        }
    }

    // 获取topic分页列表
    static public function getTopicListByPage($where = [], $page = 10)
    {
        return static::where(array_merge([
            ['is_show', '=', static::STATE_SHOW],
        ], $where))->orderBy('sort_order')->paginate();
    }

    // 获取topic详情
    static public function getTopicDetail($where = [])
    {
        return static::where(array_merge([
            ['is_show', '=', static::STATE_SHOW],
        ], $where))->orderBy('sort_order')->first();
    }
}
