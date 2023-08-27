<?php

namespace addons\smsbao\library;

class Smsbao
{
    private $_params = [];
    protected $error = '';
    protected $config = [];
    protected static $instance = null;
    protected $statusStr = array(
        "0"  => "短信发送成功",
        "-1" => "参数不全",
        "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
        "30" => "密码错误",
        "40" => "账号不存在",
        "41" => "余额不足",
        "42" => "帐户已过期",
        "43" => "IP地址限制",
        "50" => "内容含有敏感词"
    );

    public function __construct($options = [])
    {
        if ($config = get_addon_config('smsbao')) {
            $this->config = array_merge($this->config, $config);
        }
        $this->config = array_merge($this->config, is_array($options) ? $options : []);
    }

    /**
     * 单例
     * @param array $options 参数
     * @return Smsbao
     */
    public static function instance($options = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($options);
        }
        return self::$instance;
    }

    /**
     * 立即发送短信
     *
     * @return boolean
     */
    public function send()
    {
        $this->error = '';
        $params = $this->_params();
        $postArr = array(
            'u' => $params['u'],
            'p' => $params['p'],
            'm' => $params['mobile'],
            'c' => $params['msg']
        );
        $options = [
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json; charset=utf-8'
            )
        ];
        $result = \fast\Http::sendRequest('http://api.smsbao.com/sms', $postArr, 'GET', $options);
        if ($result['ret']) {
            if (isset($result['msg']) && $result['msg'] == '0')
                return TRUE;
            $this->error = isset($this->statusStr[$result['msg']]) ? $this->statusStr[$result['msg']] : 'InvalidResult';
        } else {
            $this->error = $result['msg'];
        }
        return FALSE;
    }

    private function _params()
    {
        return array_merge([
            'u' => $this->config['username'],
            'p' => md5($this->config['password']),
        ], $this->_params);
    }

    /**
     * 获取错误信息
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 接收手机
     * @param   string $mobile 手机号码
     * @return Smsbao
     */
    public function mobile($mobile = '')
    {
        $this->_params['mobile'] = $mobile;
        return $this;
    }

    /**
     * 短信内容
     * @param   string $msg 短信内容
     * @return Smsbao
     */
    public function msg($msg = '')
    {
        $this->_params['msg'] = $this->config['sign'] . $msg;
        return $this;
    }
}