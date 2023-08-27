<?php

namespace addons\lang;

use app\common\library\Menu;
use think\Addons;

/**
 * 插件
 */
class Lang extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = [
            [
                'name'    => 'lang',
                'title'   => '在线语言文件管理',
                'icon'    => 'fa fa-terminal',
                'sublist' => [
                    ['name' => 'lang/index', 'title' => '查看'],
                    ['name' => 'lang/add', 'title' => '添加'],
                    ['name' => 'lang/del', 'title' => '删除'],
                    ['name' => 'lang/edit', 'title' => '修改记录'],
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
        Menu::delete('lang');
        return true;
    }

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {
        Menu::enable('lang');
        return true;
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {
        Menu::disable('lang');
        return true;
    }

}
