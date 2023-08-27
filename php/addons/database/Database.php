<?php

namespace addons\database;

use app\common\library\Menu;
use think\Addons;

/**
 * 数据库插件
 */
class Database extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = [
            [
                'name'    => 'general/database',
                'title'   => '数据库管理',
                'icon'    => 'fa fa-database',
                'remark'  => '可进行一些简单的数据库表优化或修复，查看表结构和数据，也可以进行SQL语句的操作',
                'sublist' => [
                    ['name' => 'general/database/index', 'title' => '查看'],
                    ['name' => 'general/database/query', 'title' => '查询'],
                    ['name' => 'general/database/backup', 'title' => '备份'],
                    ['name' => 'general/database/restore', 'title' => '恢复'],
                ]
            ]
        ];
        Menu::create($menu, 'general');
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {

        Menu::delete('general/database');
        return true;
    }

    /**
     * 插件启用方法
     */
    public function enable()
    {
        Menu::enable('general/database');
    }

    /**
     * 插件禁用方法
     */
    public function disable()
    {
        Menu::disable('general/database');
    }

}
