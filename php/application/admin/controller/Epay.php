<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Config;

class Epay extends Backend
{
    protected $noNeedRight = ['upload'];

    /**
     * 上传本地证书
     * @return void
     */
    public function upload()
    {
        Config::set('default_return_type', 'json');

        $certname = $this->request->post('certname', '');
        $certPathArr = [
            'cert_client'         => '/addons/epay/certs/apiclient_cert.pem', //微信支付api
            'cert_key'            => '/addons/epay/certs/apiclient_key.pem', //微信支付api
            'app_cert_public_key' => '/addons/epay/certs/appCertPublicKey.crt',//应用公钥证书路径
            'alipay_root_cert'    => '/addons/epay/certs/alipayRootCert.crt', //支付宝根证书路径
            'ali_public_key'      => '/addons/epay/certs/alipayCertPublicKey.crt', //支付宝公钥证书路径
        ];
        if (!isset($certPathArr[$certname])) {
            $this->error("证书错误");
        }
        $url = $certPathArr[$certname];
        $file = $this->request->file('file');
        if (!$file) {
            $this->error("未上传文件");
        }
        $file->move(dirname(ROOT_PATH . $url), basename(ROOT_PATH . $url), true);
        $this->success(__('上传成功'), '', ['url' => $url]);
    }
}
