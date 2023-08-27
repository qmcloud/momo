<?php

namespace addons\alisms;

use think\Addons;

/**
 * Alisms
 */
class Alisms extends Addons
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
     * 短信发送行为
     * @param   Sms     $params
     * @return  boolean
     */
    public function smsSend(&$params)
    {
        $config = get_addon_config('alisms');
        $alisms = new library\Alisms();
        $result = $alisms->mobile($params->mobile)
                ->template($config['template'][$params->event])
                ->param(['code' => $params->code])
                ->send();
        return $result;
    }

    /**
     * 短信发送通知
     * @param   array   $params
     * @return  boolean
     */
    public function smsNotice(&$params)
    {
        $alisms = library\Alisms::instance();
        $result = $alisms->mobile($params['mobile'])
                ->template($params['template'])
                ->param($params)
                ->send();
        return $result;
    }

    /**
     * 检测验证是否正确
     * @param   Sms     $params
     * @return  boolean
     */
    public function smsCheck(&$params)
    {
        return TRUE;
    }

}
