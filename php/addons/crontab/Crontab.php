<?php

namespace addons\crontab;

use app\common\library\Menu;
use think\Addons;
use think\Loader;

/**
 * 定时任务
 */
class Crontab extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = [
            [
                'name'    => 'general/crontab',
                'title'   => '定时任务',
                'icon'    => 'fa fa-tasks',
                'remark'  => '按照设定的时间进行任务的执行,目前支持三种任务:请求URL、执行SQL、执行Shell。',
                'sublist' => [
                    ['name' => 'general/crontab/index', 'title' => '查看'],
                    ['name' => 'general/crontab/add', 'title' => '添加'],
                    ['name' => 'general/crontab/edit', 'title' => '编辑 '],
                    ['name' => 'general/crontab/del', 'title' => '删除'],
                    ['name' => 'general/crontab/multi', 'title' => '批量更新'],
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
        Menu::delete('general/crontab');
        return true;
    }

    /**
     * 插件启用方法
     */
    public function enable()
    {
        Menu::enable('general/crontab');
    }

    /**
     * 插件禁用方法
     */
    public function disable()
    {
        Menu::disable('general/crontab');
    }

    /**
     * 添加命名空间
     */
    public function appInit()
    {
        //添加命名空间
        if (!class_exists('\Cron\CronExpression')) {
            Loader::addNamespace('Cron', ADDON_PATH . 'crontab' . DS . 'library' . DS . 'Cron' . DS);
        }
    }

}
