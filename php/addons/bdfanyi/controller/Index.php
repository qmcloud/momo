<?php

namespace addons\bdfanyi\controller;

use think\addons\Controller;

// use think\Request;
class Index extends Controller
{
    public $apiurl = 'https://fanyi-api.baidu.com/api/trans/vip/translate';

    public function index()
    {
        $q = $this->request->param('q');
        $from = $this->request->param('from');
        $to = $this->request->param('to');
        /*
        *获取配置
        */
        $config = get_addon_config('bdfanyi');
        if (empty($config)) {
            return $this->error('请先配置appid和key');
        }
        if (empty($q) || empty($from) || empty($to)) {
            return $this->error('请先提交必填参数');
        }
        $args = array(
            'q'     => $q,
            'appid' => $config['appid'],
            'salt'  => rand(10000, 99999),
            'from'  => $from,
            'to'    => $to,

        );
        $args['sign'] = $this->buildSign($q, $config['appid'], $args['salt'], $config['seckey']);

        $urlstr = '';
        foreach ($args as $k => $v) {
            $urlstr .= $k . '=' . $v . '&';
        }
        $urlstr = rtrim($urlstr, '&');
        $url = $this->apiurl . '?' . $urlstr;


        $result = $this->curl_post($this->apiurl, $args);

        $result_arr = json_decode($result, true);
        $result_arr['url'] = ($url);
        if (isset($result_arr['trans_result'])) {

            $res = ['code' => 1, 'data' => $result_arr, 'msg' => '成功', 'wait' => 3];
            return json_encode($res, JSON_UNESCAPED_SLASHES);
        } else {
            if (isset($result_arr['error_code'])) {
                return $this->error('错误信息：' . $result_arr['error_code']);
            } else {
                return $this->error('接口出现错误');
            }
        }
    }

    //获取地址
    public function getapiurl()
    {

        $q = $this->request->param('q');
        $from = $this->request->param('from');
        $to = $this->request->param('to');
        /*
        *获取配置
        */
        $config = get_addon_config('bdfanyi');
        if (empty($config)) {
            return $this->error('请先配置appid和key');
        }
        $args = array(
            'q'     => $q,
            'appid' => $config['appid'],
            'salt'  => rand(10000, 99999),
            'from'  => $from,
            'to'    => $to,

        );
        $args['sign'] = $this->buildSign($q, $config['appid'], $args['salt'], $config['seckey']);
        $urlstr = '';
        foreach ($args as $k => $v) {
            $urlstr .= $k . '=' . $v . '&';
        }
        $urlstr = rtrim($urlstr, '&');
        $url = $this->apiurl . '?' . $urlstr;

        $trueurl = addon_url('bdfanyi/index/index', ['q' => $q, 'from' => $from, 'to' => $to], true, true);

        $res = ['code' => 1, 'data' => ['url' => $url, 'trueurl' => $trueurl], 'msg' => '成功', 'wait' => 3];
        return json_encode($res, JSON_UNESCAPED_SLASHES);
        // return json($res);
    }

    // CurlPOST数据提交-----------------------------------------
    public function curl_post($url, $data = '', $heads = array('application/x-www-form-urlencoded'), $cookie = '')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36');
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        // curl_setopt($ch, CURLINFO_CONTENT_LENGTH_UPLOAD,strlen($data));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        curl_setopt($ch, CURLOPT_POST, 1);
        $data = $this->convert($data);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        if (!empty($cookie)) {
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }
        if (count($heads) > 0) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $heads);
        }
        $response = curl_exec($ch);
        if (curl_errno($ch)) {//出错则显示错误信息
            return curl_error($ch);
        }
        curl_close($ch); //关闭curl链接
        return $response;//显示返回信息
    }

    public function buildSign($query, $appID, $salt, $secKey)
    {
        $str = $appID . $query . $salt . $secKey;
        $ret = md5($str);
        return $ret;
    }

    public function convert(&$args)
    {
        $data = '';
        if (is_array($args)) {
            foreach ($args as $key => $val) {
                if (is_array($val)) {
                    foreach ($val as $k => $v) {
                        $data .= $key . '[' . $k . ']=' . rawurlencode($v) . '&';
                    }
                } else {
                    $data .= "$key=" . rawurlencode($val) . "&";
                }
            }
            return trim($data, "&");
        }
        return $args;
    }
}
