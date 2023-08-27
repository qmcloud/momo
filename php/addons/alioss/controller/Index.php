<?php

namespace addons\alioss\controller;

use app\common\model\Attachment;
use think\addons\Controller;

/**
 * Ucloud
 *
 */
class Index extends Controller
{

    public function index()
    {
        $this->error("当前插件暂无前台页面");
    }

    public function params()
    {
        $name = $this->request->post('name');
        $md5 = $this->request->post('md5');
        $auth = new \addons\alioss\library\Auth();
        $params = $auth->params($name, $md5);
        $this->success('', null, $params);
        return;
    }

    public function notify()
    {
        $size = $this->request->post('size');
        $name = $this->request->post('name');
        $md5 = $this->request->post('md5');
        $type = $this->request->post('type');
        $signature = $this->request->post('signature');
        $policy = $this->request->post('policy');
        $url = $this->request->post('url');
        $suffix = substr($name, stripos($name, '.') + 1);
        $auth = new \addons\alioss\library\Auth();
        if ($auth->check($signature, $policy))
        {
            $attachment = Attachment::getBySha1($md5);
            if (!$attachment)
            {
                $params = array(
                    'filesize'    => $size,
                    'imagewidth'  => 0,
                    'imageheight' => 0,
                    'imagetype'   => $suffix,
                    'imageframes' => 0,
                    'mimetype'    => $type,
                    'url'         => $url,
                    'uploadtime'  => time(),
                    'storage'     => 'alioss',
                    'sha1'        => $md5,
                );
                Attachment::create($params);
            }
            $this->success();
        }
        else
        {
            $this->error(__('You have no permission'));
        }
        return;
    }

}
