<?php

namespace addons\alisms\controller;

use think\addons\Controller;

/**
 * 二维码生成
 *
 */
class Index extends Controller
{

    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
    }

    // 
    public function index()
    {
        return $this->view->fetch();
    }

    public function send()
    {
        $mobile = $this->request->post('mobile');
        $template = $this->request->post('template');
        $sign = $this->request->post('sign');
        $param = (array) json_decode($this->request->post('param'));
        $alisms = new \addons\alisms\library\Alisms();
        $ret = $alisms->mobile($mobile)
                ->template($template)
                ->sign($sign)
                ->param($param)
                ->send();
        if ($ret)
        {
            $this->success("发送成功");
        }
        else
        {
            $this->error("发送失败！失败原因：" . $alisms->getError());
        }
    }

}
