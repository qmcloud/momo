<?php

namespace addons\banip;

use app\common\library\Menu;
use think\Addons;
use think\Config;

load_trait('controller/Jump');

/**
 * 插件
 */
class Banip extends Addons
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
        Menu::delete('banip');
        return true;
    }

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {
        Menu::enable('banip');
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {
        Menu::disable('banip');
    }

    /**
     * 应用初始化
     * @return mixed
     */
    use \traits\controller\Jump;

    public function appInit()
    {
        // 当前插件的配置信息，配置信息存在当前目录的config.php文件中，见下方
        $config = $this->getConfig();
        $site = Config::get('site');
        $forbiddenip = $site['forbiddenip'];
        //当前时间，后期用到
        $time = time();
        // 客户端IP
        $client_ip = request()->ip();
        if ($config['status'] && $forbiddenip) {
            $IP = explode("\r\n", $forbiddenip);
            $ban = false;
            foreach ($IP as $k => $ip) {
                if (in_array($client_ip, array('127.0.0.1'))) {
                    break;
                }
                if ($ip == $client_ip) {
                    $ban = true;
                    break;
                }
                if (preg_match("/^" . str_replace('*', '[0-9]{1,3}', $ip) . "$/", $client_ip)) {
                    $ban = true;
                    break;
                }
            }
            if ($ban) {
                Config::set('default_return_type', $config['type']);
                $this->error($client_ip . $config['msg'], '', [$client_ip]);
            }
        }
    }

}
