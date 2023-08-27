<?php

namespace addons\epay\controller;

use addons\epay\library\Service;
use addons\epay\library\Wechat;
use addons\third\model\Third;
use app\common\library\Auth;
use think\addons\Controller;
use think\Response;
use think\Session;
use Yansongda\Pay\Exceptions\GatewayException;
use Yansongda\Pay\Pay;

/**
 * API接口控制器
 *
 * @package addons\epay\controller
 */
class Api extends Controller
{

    protected $layout = 'default';
    protected $config = [];

    /**
     * 默认方法
     */
    public function index()
    {
        return;
    }

    /**
     * 外部提交
     */
    public function submit()
    {
        $this->request->filter('trim');
        $out_trade_no = $this->request->request("out_trade_no");
        $title = $this->request->request("title");
        $amount = $this->request->request('amount');
        $type = $this->request->request('type');
        $method = $this->request->request('method', 'web');
        $openid = $this->request->request('openid', '');
        $auth_code = $this->request->request('auth_code', '');
        $notifyurl = $this->request->request('notifyurl', '');
        $returnurl = $this->request->request('returnurl', '');

        if (!$amount || $amount < 0) {
            $this->error("支付金额必须大于0");
        }

        if (!$type || !in_array($type, ['alipay', 'wechat'])) {
            $this->error("支付类型错误");
        }

        $params = [
            'type'         => $type,
            'out_trade_no' => $out_trade_no,
            'title'        => $title,
            'amount'       => $amount,
            'method'       => $method,
            'openid'       => $openid,
            'auth_code'    => $auth_code,
            'notifyurl'    => $notifyurl,
            'returnurl'    => $returnurl,
        ];
        return Service::submitOrder($params);
    }

    /**
     * 微信支付(公众号支付&PC扫码支付)
     * @return string
     */
    public function wechat()
    {
        $config = Service::getConfig('wechat');

        $isWechat = stripos($this->request->server('HTTP_USER_AGENT'), 'MicroMessenger') !== false;
        $isMobile = $this->request->isMobile();
        $this->view->assign("isWechat", $isWechat);
        $this->view->assign("isMobile", $isMobile);

        //发起PC支付(Scan支付)(PC扫码模式)
        if ($this->request->isAjax()) {
            $pay = Pay::wechat($config);
            $orderid = $this->request->post("orderid");
            try {
                $result = $pay->find($orderid, 'scan');
                if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
                    $this->success("", "", ['status' => $result['trade_state']]);
                } else {
                    $this->error("查询失败");
                }
            } catch (GatewayException $e) {
                $this->error("查询失败");
            }
        }

        $orderData = Session::get("wechatorderdata");
        if (!$orderData) {
            $this->error("请求参数错误");
        }
        if ($isWechat && $isMobile) {
            //发起公众号(jsapi支付),openid必须

            //如果没有openid，则自动去获取openid
            if (!isset($orderData['openid']) || !$orderData['openid']) {
                $orderData['openid'] = Service::getOpenid();
            }

            $orderData['method'] = 'mp';
            $type = 'jsapi';
            $payData = Service::submitOrder($orderData);
            if (!isset($payData['paySign'])) {
                $this->error("创建订单失败，请返回重试", "");
            }
        } else {
            $orderData['method'] = 'scan';
            $type = 'pc';
            $payData = Service::submitOrder($orderData);
            if (!isset($payData['code_url'])) {
                $this->error("创建订单失败，请返回重试", "");
            }
        }
        $this->view->assign("orderData", $orderData);
        $this->view->assign("payData", $payData);
        $this->view->assign("type", $type);

        $this->view->assign("title", "微信支付");
        return $this->view->fetch();
    }

    /**
     * 支付宝支付(PC扫码支付)
     * @return string
     */
    public function alipay()
    {
        $config = Service::getConfig('alipay');

        $isWechat = stripos($this->request->server('HTTP_USER_AGENT'), 'MicroMessenger') !== false;
        $isMobile = $this->request->isMobile();
        $this->view->assign("isWechat", $isWechat);
        $this->view->assign("isMobile", $isMobile);

        if ($this->request->isAjax()) {
            $orderid = $this->request->post("orderid");
            $pay = Pay::alipay($config);
            try {
                $result = $pay->find($orderid, 'scan');
                if ($result['code'] == '10000' && $result['trade_status'] == 'TRADE_SUCCESS') {
                    $this->success("", "", ['status' => $result['trade_status']]);
                } else {
                    $this->error("查询失败");
                }
            } catch (GatewayException $e) {
                $this->error("查询失败");
            }
        }

        //发起PC支付(Scan支付)(PC扫码模式)
        $orderData = Session::get("alipayorderdata");
        if (!$orderData) {
            $this->error("请求参数错误");
        }

        $orderData['method'] = 'scan';
        $payData = Service::submitOrder($orderData);
        if (!isset($payData['qr_code'])) {
            $this->error("创建订单失败，请返回重试");
        }

        $type = 'pc';
        $this->view->assign("orderData", $orderData);
        $this->view->assign("payData", $payData);
        $this->view->assign("type", $type);
        $this->view->assign("title", "支付宝支付");
        return $this->view->fetch();
    }

    /**
     * 支付成功回调
     */
    public function notifyx()
    {
        $type = $this->request->param('type');
        $pay = \addons\epay\library\Service::checkNotify($type);
        if (!$pay) {
            echo '签名错误';
            return;
        }
        $data = $pay->verify();

        //你可以在这里你的业务处理逻辑,比如处理你的订单状态、给会员加余额等等功能
        //下面这句必须要执行,且在此之前不能有任何输出
        return $pay->success()->send();
    }

    /**
     * 支付成功返回
     */
    public function returnx()
    {
        $type = $this->request->param('type');
        if (Service::checkReturn($type)) {
            echo '签名错误';
            return;
        }

        //你可以在这里定义你的提示信息,但切记不可在此编写逻辑
        $this->success("恭喜你！支付成功!", addon_url("epay/index/index"));

        return;
    }

}
