<?php

namespace addons\epay\controller;

use addons\epay\library\Service;
use fast\Random;
use think\addons\Controller;
use Exception;

/**
 * 微信支付宝整合插件首页
 *
 * 此控制器仅用于开发展示说明和测试，请自行添加一个新的控制器进行处理返回和回调事件，同时删除此控制器文件
 *
 * Class Index
 * @package addons\epay\controller
 */
class Index extends Controller
{

    protected $layout = 'default';

    protected $config = [];

    public function _initialize()
    {
        parent::_initialize();
        if (!config("app_debug")) {
            $this->error("仅在开发环境下查看");
        }
    }

    public function index()
    {
        $this->view->assign("title", "微信支付宝整合");
        return $this->view->fetch();
    }

    /**
     * 体验，仅供开发测试
     */
    public function experience()
    {
        $amount = $this->request->request('amount');
        $type = $this->request->request('type');
        $method = $this->request->request('method');

        if (!$amount || $amount < 0) {
            $this->error("支付金额必须大于0");
        }

        if (!$type || !in_array($type, ['alipay', 'wechat'])) {
            $this->error("支付类型不能为空");
        }

        //订单号
        $out_trade_no = date("YmdHis") . mt_rand(100000, 999999);

        //订单标题
        $title = '测试订单';

        //回调链接
        $notifyurl = $this->request->root(true) . '/addons/epay/index/notifyx/paytype/' . $type;
        $returnurl = $this->request->root(true) . '/addons/epay/index/returnx/paytype/' . $type . '/out_trade_no/' . $out_trade_no;

        $response = Service::submitOrder($amount, $out_trade_no, $type, $title, $notifyurl, $returnurl, $method);

        return $response;
    }

    /**
     * 支付成功，仅供开发测试
     */
    public function notifyx()
    {
        $paytype = $this->request->param('paytype');
        $pay = Service::checkNotify($paytype);
        if (!$pay) {
            echo '签名错误';
            return;
        }
        $data = $pay->verify();
        try {
            $payamount = $paytype == 'alipay' ? $data['total_amount'] : $data['total_fee'] / 100;
            $out_trade_no = $data['out_trade_no'];

            //你可以在此编写订单逻辑
        } catch (Exception $e) {
        }

        //下面这句必须要执行,且在此之前不能有任何输出
        return $pay->success()->send();
    }

    /**
     * 支付返回，仅供开发测试
     */
    public function returnx()
    {
        $paytype = $this->request->param('paytype');
        $out_trade_no = $this->request->param('out_trade_no');
        $pay = Service::checkReturn($paytype);
        if (!$pay) {
            $this->error('签名错误', '');
        }

        //你可以在这里通过out_trade_no去验证订单状态
        //但是不可以在此编写订单逻辑！！！

        $this->success("请返回网站查看支付结果", addon_url("epay/index/index"));
    }

}
