<?php

namespace addons\epay\library;

use fast\Http;
use think\Cache;
use think\Session;

/**
 * 微信授权
 *
 */
class Wechat
{
    private $app_id = '';
    private $app_secret = '';
    private $scope = 'snsapi_userinfo';

    public function __construct($app_id, $app_secret)
    {
        $this->app_id = $app_id;
        $this->app_secret = $app_secret;
    }

    /**
     * 获取微信授权链接
     *
     * @return string
     */
    public function getAuthorizeUrl()
    {
        $redirect_uri = addon_url('epay/api/wechat', [], true, true);
        $redirect_uri = urlencode($redirect_uri);
        $state = \fast\Random::alnum();
        Session::set('state', $state);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->app_id}&redirect_uri={$redirect_uri}&response_type=code&scope={$this->scope}&state={$state}#wechat_redirect";
    }

    /**
     * 获取微信openid
     *
     * @return mixed|string
     */
    public function getOpenid()
    {
        $openid = Session::get('openid');
        if (!$openid) {
            if (!isset($_GET['code'])) {
                $url = $this->getAuthorizeUrl();

                Header("Location: $url");
                exit();
            } else {
                $state = Session::get('state');
                if ($state == $_GET['state']) {
                    $code = $_GET['code'];
                    $token = $this->getAccessToken($code);
                    if (!isset($token['openid']) && isset($token['errmsg'])) {
                        exception($token['errmsg']);
                    }
                    $openid = isset($token['openid']) ? $token['openid'] : '';
                    if ($openid) {
                        Session::set("openid", $openid);
                    }
                }
            }
        }
        return $openid;
    }

    /**
     * 获取授权token网页授权
     *
     * @param string $code
     * @return mixed|string
     */
    public function getAccessToken($code = '')
    {
        $params = [
            'appid'      => $this->app_id,
            'secret'     => $this->app_secret,
            'code'       => $code,
            'grant_type' => 'authorization_code'
        ];
        $ret = Http::sendRequest('https://api.weixin.qq.com/sns/oauth2/access_token', $params, 'GET');
        if ($ret['ret']) {
            $ar = json_decode($ret['msg'], true);
            return $ar;
        }
        return [];
    }

    public function getJsticket($code = '')
    {
        $jsticket = Session::get('jsticket');
        if (!$jsticket) {
            $token = $this->getAccessToken($code);
            $params = [
                'access_token' => 'token',
                'type'         => 'jsapi',
            ];
            $ret = Http::sendRequest('https://api.weixin.qq.com/cgi-bin/ticket/getticket', $params, 'GET');
            if ($ret['ret']) {
                $ar = json_decode($ret['msg'], true);
                return $ar;
            }
        }
        return $jsticket;
    }
}
