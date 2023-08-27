<?php

namespace addons\epay;

use think\Addons;
use think\Config;
use think\Loader;

/**
 * 微信支付宝整合插件
 */
class Epay extends Addons
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
     * 添加命名空间
     */
    public function appInit()
    {
        //添加命名空间
        if (!class_exists('\Yansongda\Pay\Pay')) {
            Loader::addNamespace('Yansongda\Pay', ADDON_PATH . 'epay' . DS . 'library' . DS . 'Yansongda' . DS . 'Pay' . DS);
        }
        if (!class_exists('\Yansongda\Supports\Logger')) {
            Loader::addNamespace('Yansongda\Supports', ADDON_PATH . 'epay' . DS . 'library' . DS . 'Yansongda' . DS . 'Supports' . DS);
        }
    }

}
