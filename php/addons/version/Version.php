<?php

namespace addons\version;

use app\common\library\Menu;
use think\Addons;

/**
 * 版本管理插件
 */
class Version extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = [
            [
                'name'    => 'version',
                'title'   => '版本管理',
                'icon'    => 'fa fa-file-text-o',
                'remark'  => '常用于管理移动端应用版本更新',
                'sublist' => [
                    ['name' => 'version/index', 'title' => '查看'],
                    ['name' => 'version/add', 'title' => '添加'],
                    ['name' => 'version/edit', 'title' => '修改'],
                    ['name' => 'version/del', 'title' => '删除'],
                    ['name' => 'version/multi', 'title' => '批量更新'],
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
        Menu::delete('version');
        return true;
    }
    
    /**
     * 插件启用方法
     */
    public function enable()
    {
        Menu::enable('version');
    }

    /**
     * 插件禁用方法
     */
    public function disable()
    {
        Menu::disable('version');
    }

}
