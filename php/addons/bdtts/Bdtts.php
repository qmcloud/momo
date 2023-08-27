<?php

namespace addons\bdtts;

use app\common\library\Menu;
use think\Addons;

/**
 * 插件
 */
class Bdtts extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = [
            [
                'name'    => 'bdtts',
                'title'   => '百度语音合成',
                'icon'    => 'fa fa-file-text-o',
                'remark'  => '常用于百度语音合成',
                'sublist' => [
                    ['name' => 'bdtts/index', 'title' => '查看'],
                    ['name' => 'bdtts/add', 'title' => '添加'],
                    ['name' => 'bdtts/edit', 'title' => '修改'],
                    ['name' => 'bdtts/del', 'title' => '删除'],
                    ['name' => 'bdtts/multi', 'title' => '批量更新'],
                ]
            ]
        ];

        Menu::create($menu);

        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        Menu::delete('bdtts');
        return true;
    }

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {
        Menu::enable('bdtts');
        return true;
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {
        Menu::disable('bdtts');
        return true;
    }



}
