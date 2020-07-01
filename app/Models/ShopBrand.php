<?php

namespace App\Models;

use App\Http\Resources\ShopBrand as ShopBrandResource;
use Illuminate\Database\Eloquent\Model;

class ShopBrand extends Model
{
    const STATE_OFFSHOW = 0;
    const STATE_SHOW = 1;
    const STATE_OFFSHOW_STRING = '下架';
    const STATE_SHOW_STRING = '正常';
    const NEW_ADD = 1;
    const NOT_NEW_ADD = 0;
    const NEW_ADD_STRING = '新增';
    const NOT_NEW_ADD_STRING = '非新增';

    protected $table = "shop_brand";
    //
    public function classes()
    {
        return $this->belongsTo(Classes::class);
    }

    public static function getStateDispayMap()
    {
        return [
            self::STATE_OFFSHOW => self::STATE_OFFSHOW_STRING,
            self::STATE_SHOW => self::STATE_SHOW_STRING,
        ];
    }
    public static function getTypeStateDispayMap()
    {
        return [
            self::NEW_ADD => self::NEW_ADD_STRING,
            self::NOT_NEW_ADD => self::NOT_NEW_ADD_STRING,
        ];
    }

    public function getListPicUrlAttribute($pictures)
    {
        if (is_string($pictures)) {
            return json_decode($pictures, true);
        }

        return $pictures;
    }

    public function setListPicUrlAttribute($pictures)
    {
        if (is_array($pictures)) {
            $this->attributes['list_pic_url'] = json_encode($pictures);
        }
    }

    public static function getAllClasses($noRoot = false)
    {
        $classes = static::all(['id', 'name']);

        $result = [];

        if (!$noRoot) {
            $result = [
                0 => 'root'
            ];
        } else {
            $result = [
                0 => 'null'
            ];
        }
        foreach ($classes as $eachClass) {
            $result[$eachClass->id] = $eachClass->name;
        }

        return $result;
    }

    public static function getDetail($where){
        return new ShopBrandResource(static::where($where)->first());
    }
}
