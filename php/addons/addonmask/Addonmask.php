<?php

namespace addons\addonmask;

use app\common\library\Menu;
use think\Addons;
use think\Route;

/**
 * 插件
 */
class Addonmask extends Addons
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
     * 启动时, 动态修改配置以及路由注册
     * 
     * 开启后该钩子在任何代码执行时都会触发,所以使用完毕后强烈建议关闭插件!!!
     */
    public function appInit(){
        config('fastadmin.api_url','');
        $addconf = get_addon_config('Addonmask');
        $rewrite = $addconf['myrewrite'];
        Route::rule($rewrite);
    }
}
