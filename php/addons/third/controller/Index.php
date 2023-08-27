<?php

namespace addons\third\controller;

use addons\third\library\Application;
use addons\third\library\Service;
use addons\third\model\Third;
use think\addons\Controller;
use think\Config;
use think\Cookie;
use think\Hook;
use think\Lang;
use think\Session;

/**
 * 第三方登录插件
 */
class Index extends Controller
{
    protected $app = null;
    protected $options = [];

    public function _initialize()
    {
        parent::_initialize();
        $config = get_addon_config('third');
        $this->app = new Application($config);
    }

    /**
     * 插件首页
     */
    public function index()
    {
        if (!\app\admin\library\Auth::instance()->id) {
            $this->error('当前插件暂无前台页面');
        }
        $platformList = [];
        if ($this->auth->id) {
            $platformList = Third::where('user_id', $this->auth->id)->column('platform');
        }
        $this->view->assign('platformList', $platformList);
        return $this->view->fetch();
    }

    /**
     * 发起授权
     */
    public function connect()
    {
        $platform = $this->request->param('platform');
        $config = get_addon_config('third');
        if (!$config['status']) {
            $this->error("第三方登录已关闭");
        }
        $status = explode(',', $config['status']);
        if (!in_array($platform, $status)) {
            $this->error("该登录方式已关闭");
        }

        $url = $this->request->request('url', $this->request->server('HTTP_REFERER', '/', 'trim'), 'trim');
        if (!$this->app->{$platform}) {
            $this->error('参数错误');
        }
        if ($url) {
            Session::set("redirecturl", $url);
        }
        // 跳转到登录授权页面
        $this->redirect($this->app->{$platform}->getAuthorizeUrl());
        return;
    }

    /**
     * 通知回调
     */
    public function callback()
    {
        $auth = $this->auth;

        //监听注册登录注销的事件
        Hook::add('user_login_successed', function ($user) use ($auth) {
            $expire = input('post.keeplogin') ? 30 * 86400 : 0;
            Cookie::set('uid', $user->id, $expire);
            Cookie::set('token', $auth->getToken(), $expire);
        });
        Hook::add('user_register_successed', function ($user) use ($auth) {
            Cookie::set('uid', $user->id);
            Cookie::set('token', $auth->getToken());
        });
        Hook::add('user_logout_successed', function ($user) use ($auth) {
            Cookie::delete('uid');
            Cookie::delete('token');
        });
        $platform = $this->request->param('platform');

        // 成功后返回之前页面，但忽略登录/注册页面
        $url = Session::has("redirecturl") ? Session::pull("redirecturl") : url('index/user/index');
        $url = preg_match("/\/user\/(register|login|resetpwd)/i", $url) ? url('index/user/index') : $url;

        // 授权成功后的回调
        $userinfo = $this->app->{$platform}->getUserInfo();
        if (!$userinfo) {
            $this->error(__('操作失败'), $url);
        }

        Session::set("{$platform}-userinfo", $userinfo);
        //判断是否启用账号绑定
        $third = Third::get(['platform' => $platform, 'openid' => $userinfo['openid']]);
        if (!$third) {
            $config = get_addon_config('third');
            //要求绑定账号或会员当前是登录状态
            if ($config['bindaccount'] || $this->auth->id) {
                $this->redirect(url('index/third/prepare') . "?" . http_build_query(['platform' => $platform, 'url' => $url]));
            }
        }

        //直接登录
        $loginret = Service::connect($platform, $userinfo);
        if ($loginret) {
            $this->redirect($url);
        } else {
            $this->error("登录失败，请返回重试", $url);
        }
    }

    /**
     * 绑定账号
     */
    public function bind()
    {
        $platform = $this->request->request('platform', $this->request->param('platform', ''));
        $url = $this->request->get('url', $this->request->server('HTTP_REFERER', '', 'trim'), 'trim');
        $redirecturl = url("index/third/bind") . "?" . http_build_query(['platform' => $platform, 'url' => $url]);
        $this->redirect($redirecturl);
        return;
    }

    /**
     * 解绑账号
     */
    public function unbind()
    {
        $platform = $this->request->request('platform', $this->request->param('platform', ''));
        $url = $this->request->get('url', $this->request->server('HTTP_REFERER', '', 'trim'), 'trim');
        $redirecturl = url("index/third/unbind") . "?" . http_build_query(['platform' => $platform, 'url' => $url]);
        $this->redirect($redirecturl);
        return;
    }

}
