<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\Special as SpecialResource;
class Special extends Model
{
    const IFSHOW_YES = 1;
    const IFSHOW_NO = 0;
    const IFSHOW_YES_STRING = '正常';
    const IFSHOW_NO_STRING = '禁用';
    //
    protected $table = 'special';

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

    /**
     * 获取首页专题导航数据列表
     * @param array $where
     * @return mixed
     */
    static public function getSpecialList($where = [])
    {
        return SpecialResource::collection(static::select('id', 'class_id','special_title','icon','link_url')->where([
            ['if_show', '=', static::IFSHOW_YES]
        ])->orderBy('sort', 'ASC')->get());
    }
}
