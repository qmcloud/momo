<?php

namespace addons\third\controller;

use addons\third\library\Application;
use app\common\controller\Api as commonApi;
use addons\third\library\Service;
use addons\third\model\Third;
use app\common\library\Sms;
use fast\Random;
use think\Lang;
use think\Config;
use think\Session;
use think\Validate;

/**
 * 第三方登录插件
 */
class Api extends commonApi
{
    protected $noNeedLogin = ['getAuthUrl', 'callback', 'account']; // 无需登录即可访问的方法，同时也无需鉴权了
    protected $noNeedRight = ['*']; // 无需鉴权即可访问的方法

    protected $app = null;
    protected $options = [];
    protected $config = null;

    public function _initialize()
    {
        //跨域检测
        check_cors_request();
        //设置session_id
        Config::set('session.id', $this->request->server("HTTP_SID"));

        parent::_initialize();
        $this->config = get_addon_config('third');
        $this->app = new Application($this->config);
    }

    /**
     * H5获取授权链接
     * @return void
     */
    public function getAuthUrl()
    {
        $url = $this->request->param('url', '', 'trim');
        $platform = $this->request->param('platform');
        if (!$url || !$platform || !isset($this->config[$platform])) {
            $this->error('参数错误');
        }
        $this->config[$platform]['callback'] = $url;
        $this->app = new Application($this->config); //
        if (!$this->app->{$platform}) {
            $this->error(__('Invalid parameters'));
        }
        $this->success('', $this->app->{$platform}->getAuthorizeUrl());
    }

    /**
     * 公众号:wechat 授权回调的请求【非第三方，自己的前端请求】
     * @return void
     */
    public function callback()
    {

        $platform = $this->request->param('platform');
        if (!$this->app->{$platform}) {
            $this->error(__('Invalid parameters'));
        }
        $userinfo = $this->app->{$platform}->getUserInfo($this->request->param());
        if (!$userinfo) {
            $this->error(__('操作失败'));
        }
        $userinfo['apptype'] = 'mp';
        $userinfo['platform'] = $platform;

        $third = [
            'avatar'   => $userinfo['userinfo']['avatar'],
            'nickname' => $userinfo['userinfo']['nickname']
        ];

        $user = null;
        if ($this->auth->isLogin() || Service::isBindThird($userinfo['platform'], $userinfo['openid'], $userinfo['apptype'], $userinfo['unionid'])) {
            Service::connect($userinfo['platform'], $userinfo);
            $user = $this->auth->getUserinfo();
        } else {
            $user = false;
            Session::set('third-userinfo', $userinfo);
        }
        $this->success("授权成功！", ['user' => $user, 'third' => $third]);
    }

    /**
     * 登录或创建账号
     */
    public function account()
    {

        if ($this->request->isPost()) {
            $params = Session::get('third-userinfo');
            $mobile = $this->request->post('mobile', '');
            $code = $this->request->post('code', $this->request->post('captcha'));
            $token = $this->request->post('__token__');
            $rule = [
                'mobile'    => 'require|regex:/^1\d{10}$/',
                '__token__' => 'require|token',
            ];
            $msg = [
                'mobile' => 'Mobile is incorrect',
            ];
            $data = [
                'mobile'    => $mobile,
                '__token__' => $token,
            ];
            $ret = Sms::check($mobile, $code, 'bind');
            if (!$ret) {
                $this->error(__('验证码错误'));
            }
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                $this->error(__($validate->getError()), ['__token__' => $this->request->token()]);
            }

            $userinfo = \app\common\model\User::where('mobile', $mobile)->find();
            if ($userinfo) {
                $result = $this->auth->direct($userinfo->id);
            } else {
                $result = $this->auth->register($mobile, Random::alnum(), '', $mobile, isset($params['userinfo']) ? $params['userinfo'] : []);
            }

            if ($result) {
                Service::connect($params['platform'], $params);
                $this->success(__('绑定账号成功'), ['userinfo' => $this->auth->getUserinfo()]);
            } else {
                $this->error($this->auth->getError(), ['__token__' => $this->request->token()]);
            }
        }
    }
}
