<?php

namespace addons\bdfanyi;

use app\common\library\Menu;
use think\Addons;

/**
 * 插件
 */
class Bdfanyi extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = [
            [
                'name'    => 'bdfanyi',
                'title'   => '百度翻译',
                'icon'    => 'fa fa-file-text-o',
                'remark'  => '常用于管理百度翻译',
                'sublist' => [
                    ['name' => 'bdfanyi/index', 'title' => '查看'],
                    ['name' => 'bdfanyi/add', 'title' => '添加'],
                    ['name' => 'bdfanyi/edit', 'title' => '修改'],
                    ['name' => 'bdfanyi/del', 'title' => '删除'],
                    ['name' => 'bdfanyi/multi', 'title' => '批量更新'],
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
        Menu::delete('bdfanyi');
        return true;
    }

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {
        Menu::enable('bdfanyi');
        return true;
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {
        Menu::disable('bdfanyi');
        return true;
    }

}
