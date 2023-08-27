<?php

namespace addons\simditor;

use app\common\library\Menu;
use think\Addons;

/**
 * 插件
 */
class Simditor extends Addons
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

    public function upgrade()
    {
        return true;
    }

    /**
     * @param $params
     */
    public function configInit(&$params)
    {
        $config = $this->getConfig();
        $params['simditor'] = ['classname' => $config['classname'] ?? '.editor'];
    }

}
