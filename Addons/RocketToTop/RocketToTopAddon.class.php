<?php
// +----------------------------------------------------------------------
// | QQ群274904994 [ 简单 高效 卓越 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 51zhibo.top All rights reserved.
// +----------------------------------------------------------------------
// | Author: 51zhibo.top
// +----------------------------------------------------------------------
namespace Addons\RocketToTop;

use Common\Controller\Addon;

/**
 * 小火箭返回顶部
 * @51zhibo.top
 */
class RocketToTopAddon extends Addon
{
    /**
     * 插件信息
     * @author 51zhibo.top
     */
    public $info = array(
        'name'        => 'RocketToTop',
        'title'       => '小火箭返回顶部',
        'description' => '小火箭返回顶部',
        'status'      => '1',
        'author'      => 'OpenCMF',
        'version'     => '1.3.0',
    );

    /**
     * 插件安装方法
     * @author 51zhibo.top
     */
    public function install()
    {
        return true;
    }

    /**
     * 插件卸载方法
     * @author 51zhibo.top
     */
    public function uninstall()
    {
        return true;
    }

    /**
     * 实现的PageFooter钩子方法
     * @author 51zhibo.top
     */
    public function PageFooter($param)
    {
        $addons_config = $this->getConfig();
        if ($addons_config['status']) {
            $this->display('rocket');
        }
    }
}
