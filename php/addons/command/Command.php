<?php

namespace addons\command;

use app\common\library\Menu;
use think\Addons;

/**
 * 在线命令插件
 */
class Command extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = [
            [
                'name'    => 'command',
                'title'   => '在线命令管理',
                'icon'    => 'fa fa-terminal',
                'sublist' => [
                    ['name' => 'command/index', 'title' => '查看'],
                    ['name' => 'command/add', 'title' => '添加'],
                    ['name' => 'command/detail', 'title' => '详情'],
                    ['name' => 'command/command', 'title' => '生成并执行命令'],
                    ['name' => 'command/execute', 'title' => '再次执行命令'],
                    ['name' => 'command/del', 'title' => '删除'],
                    ['name' => 'command/multi', 'title' => '批量更新'],
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
        Menu::delete('command');
        return true;
    }

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {
        Menu::enable('command');
        return true;
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {
        Menu::disable('command');
        return true;
    }

}
