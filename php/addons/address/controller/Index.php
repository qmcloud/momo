<?php

namespace addons\address\controller;

use think\addons\Controller;
use think\Config;
use think\Hook;

class Index extends Controller
{

    // 首页
    public function index()
    {
        // 语言检测
        $lang = $this->request->langset();
        $lang = preg_match("/^([a-zA-Z\-_]{2,10})\$/i", $lang) ? $lang : 'zh-cn';

        $site = Config::get("site");

        // 配置信息
        $config = [
            'site'           => array_intersect_key($site, array_flip(['name', 'cdnurl', 'version', 'timezone', 'languages'])),
            'upload'         => null,
            'modulename'     => 'addons',
            'controllername' => 'index',
            'actionname'     => 'index',
            'jsname'         => 'addons/address',
            'moduleurl'      => '',
            'language'       => $lang
        ];
        $config = array_merge($config, Config::get("view_replace_str"));

        // 配置信息后
        Hook::listen("config_init", $config);
        // 加载当前控制器语言包
        $this->view->assign('site', $site);
        $this->view->assign('config', $config);

        return $this->view->fetch();
    }

    // 选择地址
    public function select()
    {
        $config = get_addon_config('address');
        $lng = $this->request->get('lng');
        $lat = $this->request->get('lat');
        $lng = $lng ? $lng : $config['lng'];
        $lat = $lat ? $lat : $config['lat'];
        $this->view->assign('lng', $lng);
        $this->view->assign('lat', $lat);
        $maptype = $config['maptype'];
        if (!isset($config[$maptype . 'key']) || !$config[$maptype . 'key']) {
            $this->error("请在配置中配置对应类型地图的密钥");
        }
        return $this->view->fetch('index/' . $maptype);
    }

}
