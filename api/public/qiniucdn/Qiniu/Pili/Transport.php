<?php
namespace Qiniu\Pili;

use \Qiniu\Pili\Utils;
use \Qiniu\Pili\HttpRequest;

final class Transport
{
    private $_mac;

    public function __construct($mac)
    {
        $this->_mac = $mac;
    }

    public function send($method, $url, $body = NULL)
    {
        $headers = $this->_setHeaders($method, $url, $body);
        $response = HttpRequest::send($method, $url, $body, $headers);
        return $response->body;
    }

    private function _setHeaders($method, $url, $body = NULL)
    {
        if ($method != HttpRequest::GET) {
            $cType = 'application/json';
        } else {
            $cType = NULL;
        }
        $macToken = $this->_mac->MACToken($method, $url, $cType, $body);
        $ua = Utils::getUserAgent(Config::SDK_USER_AGENT, Config::SDK_VERSION);
        return array(
            'Content-Type' => $cType,
            'User-Agent' => $ua,
            'Authorization' => $macToken,
        );
    }
}

?>
