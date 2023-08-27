<?php

namespace addons\alioss\library;

class Auth
{

    public function __construct()
    {
        
    }

    public function params($name, $md5, $callback = true)
    {
        $config = get_addon_config('alioss');
        $callback_param = array(
            'callbackUrl'      => isset($config['notifyurl']) ? $config['notifyurl'] : '',
            'callbackBody'     => 'filename=${object}&size=${size}&mimeType=${mimeType}&height=${imageInfo.height}&width=${imageInfo.width}',
            'callbackBodyType' => "application/x-www-form-urlencoded"
        );

        $base64_callback_body = base64_encode(json_encode($callback_param));

        $now = time();
        $end = $now + $config['expire']; //设置该policy超时时间是10s. 即这个policy过了这个有效时间，将不能访问
        $expiration = $this->gmt_iso8601($end);

        preg_match('/(\d+)(\w+)/', $config['maxsize'], $matches);
        $type = strtolower($matches[2]);
        $typeDict = ['b' => 0, 'k' => 1, 'kb' => 1, 'm' => 2, 'mb' => 2, 'gb' => 3, 'g' => 3];
        $size = (int) $config['maxsize'] * pow(1024, isset($typeDict[$type]) ? $typeDict[$type] : 0);

        //最大文件大小.用户可以自己设置
        $condition = array(0 => 'content-length-range', 1 => 0, 2 => $size);
        $conditions[] = $condition;

        //表示用户上传的数据,必须是以$dir开始, 不然上传会失败,这一步不是必须项,只是为了安全起见,防止用户通过policy上传到别人的目录
        //$start = array(0 => 'starts-with', 1 => '$key', 2 => $dir);
        //$conditions[] = $start;

        $arr = array('expiration' => $expiration, 'conditions' => $conditions);

        $policy = base64_encode(json_encode($arr));
        $signature = base64_encode(hash_hmac('sha1', $policy, $config['app_key'], true));

        $suffix = substr($name, stripos($name, '.') + 1);
        $search = ['{year}', '{mon}', '{month}', '{day}', '{filemd5}', '{suffix}', '{.suffix}'];
        $replace = [date("Y"), date("m"), date("m"), date("d"), $md5, $suffix, '.' . $suffix];
        $key = ltrim(str_replace($search, $replace, $config['savekey']), '/');

        $response = array();
        $response['id'] = $config['app_id'];
        $response['key'] = $key;
        $response['policy'] = $policy;
        $response['signature'] = $signature;
        $response['expire'] = $end;
        $response['callback'] = '';
        return $response;
    }

    public function check($signature, $policy)
    {
        $config = get_addon_config('alioss');
        $sign = base64_encode(hash_hmac('sha1', $policy, $config['app_key'], true));
        return $signature == $sign;
    }

    private function gmt_iso8601($time)
    {
        $dtStr = date("c", $time);
        $mydatetime = new \DateTime($dtStr);
        $expiration = $mydatetime->format(\DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration . "Z";
    }

}
