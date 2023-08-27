<?php

namespace addons\address;

use think\Addons;

/**
 * 地址选择
 * @author [MiniLing] <[laozheyouxiang@163.com]>
 */
class Address extends Addons
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

}
