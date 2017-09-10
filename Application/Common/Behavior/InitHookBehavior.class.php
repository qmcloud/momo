<?php
// +----------------------------------------------------------------------
// | QQ群274904994 [ 简单 高效 卓越 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 51zhibo.top All rights reserved.
// +----------------------------------------------------------------------
// | Author: 51zhibo.top
// +----------------------------------------------------------------------
namespace Common\Behavior;

use Think\Behavior;
use Think\Hook;

defined('THINK_PATH') or exit();
/**
 * 初始化钩子信息
 * @author 51zhibo.top
 */
class InitHookBehavior extends Behavior
{
    /**
     * 行为扩展的执行入口必须是run
     * @author 51zhibo.top
     */
    public function run(&$content)
    {
        // 安装模式下直接返回
        if (defined('BIND_MODULE') && BIND_MODULE === 'Install') {
            return;
        }

        // 添加插件配置
        $addon_config['ADDON_PATH']                   = './Addons/';
        $addon_config['AUTOLOAD_NAMESPACE']           = C('AUTOLOAD_NAMESPACE');
        $addon_config['AUTOLOAD_NAMESPACE']['Addons'] = $addon_config['ADDON_PATH'];
        C($addon_config);

        $data = S('hooks');
        if (!$data || APP_DEBUG === true) {
            $hooks = D('Admin/Hook')->getField('name,addons');
            foreach ($hooks as $key => $value) {
                if ($value) {
                    $map['status'] = 1;
                    $names         = explode(',', $value);
                    $map['name']   = array('IN', $names);
                    $data          = D('Admin/Addon')->where($map)->getField('id,name');
                    if ($data) {
                        $addons = array_intersect($names, $data);
                        Hook::add($key, array_map('get_addon_class', $addons));
                    }
                }
            }
            S('hooks', Hook::get());
        } else {
            Hook::import($data, false);
        }
    }
}
