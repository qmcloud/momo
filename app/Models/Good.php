<?php

namespace App\Models;

use App\Http\Resources\GoodDetail;
use Illuminate\Database\Eloquent\Model;

class Good extends Model
{
    const STATE_OFFSHELF = 0;
    const STATE_NORMAL = 1;
    const STATE_BANNED = 2;
    const STATE_OFFSHELF_STRING = '下架';
    const STATE_NORMAL_STRING = '正常';
    const STATE_BANNED_STRING = '禁售';
    //
    public function classes()
    {
        return $this->belongsTo(Classes::class);
    }

    public static function getStateDispayMap()
    {
        return [
            self::STATE_OFFSHELF => self::STATE_OFFSHELF_STRING,
            self::STATE_NORMAL => self::STATE_NORMAL_STRING,
            self::STATE_BANNED => self::STATE_BANNED_STRING,
        ];
    }

    public function getGoodsDescriptionPicturesAttribute($pictures)
    {
        if (is_string($pictures)) {
            return json_decode($pictures, true);
        }

        return $pictures;
    }

    public function setGoodsDescriptionPicturesAttribute($pictures)
    {
        if (is_array($pictures)) {
            $this->attributes['goods_description_pictures'] = json_encode($pictures);
        }
    }

    public function getGoodsCarouselAttribute($pictures)
    {
        if (is_string($pictures)) {
            return json_decode($pictures, true);
        }

        return $pictures;
    }

    public function setGoodsCarouselAttribute($pictures)
    {
        if (is_array($pictures)) {
            $this->attributes['goods_carousel'] = json_encode($pictures);
        }
    }

    public static function getAllGoodsID()
    {
        $allGoods = self::all('id')->toArray();
        $results = [];
        foreach ($allGoods as $eachGood) {
            $results[$eachGood['id']] = $eachGood['id'];
        }
        return $results;
    }

    public static function getGoodInfoByID($id)
    {
        $result = Good::find($id);
        if ($result) {
            return new GoodDetail($result);
        } else {
            return false;
        }
    }
}
