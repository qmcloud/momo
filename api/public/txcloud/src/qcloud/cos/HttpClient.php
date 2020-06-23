<?php

namespace QCloud\Cos;

class HttpClient {
    private $httpInfo = '';
    private $curlHandler;

    /**
     * send http request
     * @param  array $request http请求信息
     *                   url        : 请求的url地址
     *                   method     : 请求方法，'get', 'post', 'put', 'delete', 'head'
     *                   data       : 请求数据，如有设置，则method为post
     *                   header     : 需要设置的http头部
     *                   host       : 请求头部host
     *                   timeout    : 请求超时时间
     *                   cert       : ca文件路径
     *                   ssl_version: SSL版本号
     * @return string    http请求响应
     */
    public function sendRequest($request) {
        if ($this->curlHandler) {
            if (function_exists('curl_reset')) {
                curl_reset($this->curlHandler);
            } else {
                $this->reset();
            }
        } else {
            $this->curlHandler = curl_init();
        }

        curl_setopt($this->curlHandler, CURLOPT_URL, $request['url']);

        $method = 'GET';
        if (isset($request['method']) &&
                in_array(strtolower($request['method']), array('get', 'post', 'put', 'delete', 'head'))) {
            $method = strtoupper($request['method']);
        } else if (isset($request['data'])) {
            $method = 'POST';
        }

        $header = isset($request['header']) ? $request['header'] : array();
        $header[] = 'Method:'.$method;
        $header[] = 'User-Agent:'.$this->getUserAgent();
        $header[] = 'Connection: keep-alive';

        isset($request['host']) && $header[] = 'Host:' . $request['host'];
        curl_setopt($this->curlHandler, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curlHandler, CURLOPT_CUSTOMREQUEST, $method);
        isset($request['timeout']) && curl_setopt($this->curlHandler, CURLOPT_TIMEOUT, $request['timeout']);

        if (isset($request['data']) && in_array($method, array('POST', 'PUT'))) {
            if (defined('CURLOPT_SAFE_UPLOAD')) {
                curl_setopt($this->curlHandler, CURLOPT_SAFE_UPLOAD, true);
            }

            curl_setopt($this->curlHandler, CURLOPT_POST, true);
            array_push($header, 'Expect: 100-continue');

            if (is_array($request['data'])) {
                $arr = Helper::buildCustomPostFields($request['data']);
                array_push($header, 'Content-Type: multipart/form-data; boundary=' . $arr[0]);
                curl_setopt($this->curlHandler, CURLOPT_POSTFIELDS, $arr[1]);
            } else {
                curl_setopt($this->curlHandler, CURLOPT_POSTFIELDS, $request['data']);
            }
        }
        curl_setopt($this->curlHandler, CURLOPT_HTTPHEADER, $header);

        $ssl = substr($request['url'], 0, 8) == "https://" ? true : false;
        if( isset($request['cert'])){
            curl_setopt($this->curlHandler, CURLOPT_SSL_VERIFYPEER,true);
            curl_setopt($this->curlHandler, CURLOPT_CAINFO, $request['cert']);
            curl_setopt($this->curlHandler, CURLOPT_SSL_VERIFYHOST,2);
            if (isset($request['ssl_version'])) {
                curl_setopt($this->curlHandler, CURLOPT_SSLVERSION, $request['ssl_version']);
            } else {
                curl_setopt($this->curlHandler, CURLOPT_SSLVERSION, 4);
            }
        }else if( $ssl ){
            curl_setopt($this->curlHandler, CURLOPT_SSL_VERIFYPEER,false);   //true any ca
            curl_setopt($this->curlHandler, CURLOPT_SSL_VERIFYHOST, 0);       //do not check
            if (isset($request['ssl_version'])) {
                curl_setopt($this->curlHandler, CURLOPT_SSLVERSION, $request['ssl_version']);
            } else {
                curl_setopt($this->curlHandler, CURLOPT_SSLVERSION, 4);
            }
        }
        $ret = curl_exec($this->curlHandler);
        $this->httpInfo = curl_getinfo($this->curlHandler);
        return $ret;
    }

    /**
     * 下载文件
     * @param  array $request http请求信息
     *                   url        : 请求的url地址
     *                   header     : 需要设置的http头部
     *                   host       : 请求头部host
     *                   timeout    : 请求超时时间
     *                   cert       : ca文件路径
     *                   ssl_version: SSL版本号
     * @param  string $dstPath 下载保存文件
     * @return bool    下载是否成功
     */
    public function download($request, $dstPath) {
        if ($this->curlHandler) {
            if (function_exists('curl_reset')) {
                curl_reset($this->curlHandler);
            } else {
                $this->reset();
            }
        } else {
            $this->curlHandler = curl_init();
        }

        curl_setopt($this->curlHandler, CURLOPT_URL, $request['url']);

        $method = 'GET';

        $header = isset($request['header']) ? $request['header'] : array();
        $header[] = 'Method:'.$method;
        $header[] = 'User-Agent:'.$this->getUserAgent();
        $header[] = 'Connection: keep-alive';

        isset($request['host']) && $header[] = 'Host:' . $request['host'];
        curl_setopt($this->curlHandler, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curlHandler, CURLOPT_CUSTOMREQUEST, $method);
        isset($request['timeout']) && curl_setopt($this->curlHandler, CURLOPT_TIMEOUT, $request['timeout']);

        if (isset($request['data']) && in_array($method, array('POST', 'PUT'))) {
            if (defined('CURLOPT_SAFE_UPLOAD')) {
                curl_setopt($this->curlHandler, CURLOPT_SAFE_UPLOAD, true);
            }

            curl_setopt($this->curlHandler, CURLOPT_POST, true);
            array_push($header, 'Expect: 100-continue');

            if (is_array($request['data'])) {
                $arr = Helper::buildCustomPostFields($request['data']);
                array_push($header, 'Content-Type: multipart/form-data; boundary=' . $arr[0]);
                curl_setopt($this->curlHandler, CURLOPT_POSTFIELDS, $arr[1]);
            } else {
                curl_setopt($this->curlHandler, CURLOPT_POSTFIELDS, $request['data']);
            }
        }
        curl_setopt($this->curlHandler, CURLOPT_HTTPHEADER, $header);

        $ssl = substr($request['url'], 0, 8) == "https://" ? true : false;
        if( isset($request['cert'])){
            curl_setopt($this->curlHandler, CURLOPT_SSL_VERIFYPEER,true);
            curl_setopt($this->curlHandler, CURLOPT_CAINFO, $request['cert']);
            curl_setopt($this->curlHandler, CURLOPT_SSL_VERIFYHOST,2);
            if (isset($request['ssl_version'])) {
                curl_setopt($this->curlHandler, CURLOPT_SSLVERSION, $request['ssl_version']);
            } else {
                curl_setopt($this->curlHandler, CURLOPT_SSLVERSION, 4);
            }
        }else if( $ssl ){
            curl_setopt($this->curlHandler, CURLOPT_SSL_VERIFYPEER,false);   //true any ca
            curl_setopt($this->curlHandler, CURLOPT_SSL_VERIFYHOST,1);       //check only host
            if (isset($request['ssl_version'])) {
                curl_setopt($this->curlHandler, CURLOPT_SSLVERSION, $request['ssl_version']);
            } else {
                curl_setopt($this->curlHandler, CURLOPT_SSLVERSION, 4);
            }
        }

        $fp = fopen($dstPath, 'wb');
        if (!$fp) {
            return array(
                'code' => Api::COSAPI_PARAMS_ERROR,
                'message' => "Cannot open file {$dstPath}"
            );
        }
        curl_setopt($this->curlHandler, CURLOPT_FILE, $fp);

        $ret = curl_exec($this->curlHandler);
        if (!$ret) {
            return array(
                'code' => Api::COSAPI_NETWORK_ERROR,
                'message' => "Download faild."
            );
        }
        $this->httpInfo = curl_getinfo($this->curlHandler);
        fclose($fp);
        return array(
            'code' => Api::COSAPI_SUCCESS,
            'message' => "Download success."
        );
    }

    public function info() {
        return $this->httpInfo;
    }

    private function getUserAgent() {
        return 'cos-php-sdk-' . Api::VERSION;
    }

    private function reset() {
        curl_setopt($this->curlHandler, CURLOPT_URL, '');
        curl_setopt($this->curlHandler, CURLOPT_HTTPHEADER, array());
        curl_setopt($this->curlHandler, CURLOPT_POSTFIELDS, array());
        curl_setopt($this->curlHandler, CURLOPT_TIMEOUT, 0);
        curl_setopt($this->curlHandler, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curlHandler, CURLOPT_SSL_VERIFYHOST, 0);
    }
}
