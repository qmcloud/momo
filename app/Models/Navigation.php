<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\Navigation as NavigationResource;

class Navigation extends Model
{
    const IFSHOW_YES = 1;
    const IFSHOW_NO = 0;
    const IFSHOW_YES_STRING = '正常';
    const IFSHOW_NO_STRING = '禁用';
    //
    protected $table = 'navigation';

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
     * 获取链接类型
     */
    public static function getLinkTypes(){
        return [
            'special' => '专题',
            'link_url' => '链接地址',
        ];
    }
    /**
     * 获取首页导航数据列表
     * @param array $where
     * @return mixed
     */
    static public function getNavList($where = [])
    {
        return NavigationResource::collection(static::select('id', 'icon','nav_title','link_type','link_data')->where([
            ['if_show', '=', static::IFSHOW_YES]
        ])->orderBy('sort', 'DESC')->get());
    }
}
