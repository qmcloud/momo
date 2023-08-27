<?php

namespace addons\captcha;

use app\common\library\Menu;
use think\Addons;

/**
 * 动态验证码插件
 */
class Captcha extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        
        return true;
    }

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {
        
        return true;
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {
        
        return true;
    }

    /**
     * 实现钩子方法
     * @return mixed
     */
    public function appInit($param)
    {
        \think\Route::get('captcha/[:id]', "\\think\\addons\\Route@execute?addon=captcha&controller=index&action=build");
    }

}
