<?php
/*
* Copyright (c) 2017 Baidu.com, Inc. All Rights Reserved
*
* Licensed under the Apache License, Version 2.0 (the "License"); you may not
* use this file except in compliance with the License. You may obtain a copy of
* the License at
*
* Http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
* WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
* License for the specific language governing permissions and limitations under
* the License.
*/
namespace addons\bdtts\library\lib;
use addons\bdtts\library\lib\AipHttpClient;
use addons\bdtts\library\lib\AipBCEUtil;
/**
 * Aip Base 基类
 */
class AipBase {

    /**
     * 获取access token url
     * @var string
     */
    protected $accessTokenUrl = 'https://aip.baidubce.com/oauth/2.0/token';

     /**
     * 反馈接口
     * @var string
     */
    protected $reportUrl = 'https://aip.baidubce.com/rpc/2.0/feedback/v1/report';

    /**
     * appId
     * @var string
     */
    protected $appId = '';

    /**
     * apiKey
     * @var string
     */
    protected $apiKey = '';

    /**
     * secretKey
     * @var string
     */
    protected $secretKey = '';

    /**
     * 权限
     * @var array
     */
    protected $scope = 'brain_all_scope';

    /**
     * @param string $appId
     * @param string $apiKey
     * @param string $secretKey
     */
    public function __construct($appId, $apiKey, $secretKey){
        $this->appId = trim($appId);
        $this->apiKey = trim($apiKey);
        $this->secretKey = trim($secretKey);
        $this->isCloudUser = null;
        $this->client = new AipHttpClient();
        $this->version = '2_2_20';
        $this->proxies = array();
    }

    /**
     * 查看版本
     * @return string
     *
     */
    public function getVersion(){
        return $this->version;
    }

    /**
     * 连接超时
     * @param int $ms 毫秒
     */
    public function setConnectionTimeoutInMillis($ms){
        $this->client->setConnectionTimeoutInMillis($ms);
    }

    /**
     * 响应超时
     * @param int $ms 毫秒
     */
    public function setSocketTimeoutInMillis($ms){
        $this->client->setSocketTimeoutInMillis($ms);
    }

    /**
     * 代理
     * @param array $proxy
     * @return string
     *
     */
    public function setProxies($proxies){
        $this->client->setConf($proxies);
    }

    /**
     * 处理请求参数
     * @param  string $url
     * @param array $params
     * @param array $data
     * @param array $headers
     */
    protected function proccessRequest($url, &$params, &$data, $headers){
        $params['aipSdk'] = 'php';
        $params['aipSdkVersion'] = $this->version;
    }

    /**
     * Api 请求
     * @param  string $url
     * @param  mixed $data
     * @return mixed
     */
    protected function request($url, $data, $headers=array()){
        try{
            $result = $this->validate($url, $data);
            if($result !== true){
                return $result;
            }

            $params = array();
            $authObj = $this->auth();

            if($this->isCloudUser === false){
                $params['access_token'] = $authObj['access_token'];
            }

            // 特殊处理
            $this->proccessRequest($url, $params, $data, $headers);

            $headers = $this->getAuthHeaders('POST', $url, $params, $headers);
            $response = $this->client->post($url, $data, $params, $headers);

            $obj = $this->proccessResult($response['content']);

            if(!$this->isCloudUser && isset($obj['error_code']) && $obj['error_code'] == 110){
                $authObj = $this->auth(true);
                $params['access_token'] = $authObj['access_token'];
                $response = $this->client->post($url, $data, $params, $headers);
                $obj = $this->proccessResult($response['content']);
            }

            if(empty($obj) || !isset($obj['error_code'])){
                $this->writeAuthObj($authObj);
            }
        }catch(Exception $e){
            return array(
                'error_code' => 'SDK108',
                'error_msg' => 'connection or read data timeout',
            );
        }

        return $obj;
    }

    /**
     * Api 多个并发请求
     * @param  string $url
     * @param  mixed $data
     * @return mixed
     */
    protected function multi_request($url, $data){
        try{
            $params = array();
            $authObj = $this->auth();
            $headers = $this->getAuthHeaders('POST', $url);

            if($this->isCloudUser === false){
                $params['access_token'] = $authObj['access_token'];
            }

            $responses = $this->client->multi_post($url, $data, $params, $headers);

            $is_success = false;
            foreach($responses as $response){
                $obj = $this->proccessResult($response['content']);

                if(empty($obj) || !isset($obj['error_code'])){
                    $is_success = true;
                }

                if(!$this->isCloudUser && isset($obj['error_code']) && $obj['error_code'] == 110){
                    $authObj = $this->auth(true);
                    $params['access_token'] = $authObj['access_token'];
                    $responses = $this->client->post($url, $data, $params, $headers);
                    break;
                }
            }

            if($is_success){
                $this->writeAuthObj($authObj);
            }

            $objs = array();
            foreach($responses as $response){
                $objs[] = $this->proccessResult($response['content']);
            }

        }catch(Exception $e){
            return array(
                'error_code' => 'SDK108',
                'error_msg' => 'connection or read data timeout',
            );
        }

        return $objs;
    }

    /**
     * 格式检查
     * @param  string $url
     * @param  array $data
     * @return mix
     */
    protected function validate($url, &$data){
        return true;
    }

    /**
     * 格式化结果
     * @param $content string
     * @return mixed
     */
    protected function proccessResult($content){
        return json_decode($content, true);
    }

    /**
     * 返回 access token 路径
     * @return string
     */
    private function getAuthFilePath(){
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . md5($this->apiKey);
    }

    /**
     * 写入本地文件
     * @param  array $obj
     * @return void
     */
    private function writeAuthObj($obj){
        if($obj === null || (isset($obj['is_read']) && $obj['is_read'] === true)){
            return;
        }

        $obj['time'] = time();
        $obj['is_cloud_user'] = $this->isCloudUser;
        @file_put_contents($this->getAuthFilePath(), json_encode($obj));
    }

    /**
     * 读取本地缓存
     * @return array
     */
    private function readAuthObj(){
        $content = @file_get_contents($this->getAuthFilePath());
        if($content !== false){
            $obj = json_decode($content, true);
            $this->isCloudUser = $obj['is_cloud_user'];
            $obj['is_read'] = true;
            if($this->isCloudUser || $obj['time'] + $obj['expires_in'] - 30 > time()){
                return $obj;
            }
        }

        return null;
    }

    /**
     * 认证
     * @param bool $refresh 是否刷新
     * @return array
     */
    private function auth($refresh=false){

        //非过期刷新
        if(!$refresh){
            $obj = $this->readAuthObj();
            if(!empty($obj)){
                return $obj;
            }
        }

        $response = $this->client->get($this->accessTokenUrl, array(
            'grant_type' => 'client_credentials',
            'client_id' => $this->apiKey,
            'client_secret' => $this->secretKey,
        ));

        $obj = json_decode($response['content'], true);

        $this->isCloudUser = !$this->isPermission($obj);
        return $obj;
    }

    /**
     * 判断认证是否有权限
     * @param  array   $authObj
     * @return boolean
     */
    protected function isPermission($authObj)
    {
        if(empty($authObj) || !isset($authObj['scope'])){
            return false;
        }

        $scopes = explode(' ', $authObj['scope']);

        return in_array($this->scope, $scopes);
    }

    /**
     * @param  string $method HTTP method
     * @param  string $url
     * @param  array $param 参数
     * @return array
     */
    private function getAuthHeaders($method, $url, $params=array(), $headers=array()){

        //不是云的老用户则不用在header中签名 认证
        if($this->isCloudUser === false){
            return $headers;
        }

        $obj = parse_url($url);
        if(!empty($obj['query'])){
            foreach(explode('&', $obj['query']) as $kv){
                if(!empty($kv)){
                    list($k, $v) = explode('=', $kv, 2);
                    $params[$k] = $v;
                }
            }
        }

        //UTC 时间戳
        $timestamp = gmdate('Y-m-d\TH:i:s\Z');
        $headers['Host'] = isset($obj['port']) ? sprintf('%s:%s', $obj['host'], $obj['port']) : $obj['host'];
        $headers['x-bce-date'] = $timestamp;

        //签名
        $headers['authorization'] = AipSampleSigner::sign(array(
            'ak' => $this->apiKey,
            'sk' => $this->secretKey,
        ), $method, $obj['path'], $headers, $params, array(
            'timestamp' => $timestamp,
            'headersToSign' => array_keys($headers),
        ));

        return $headers;
    }

    /**
     * 反馈
     *
     * @param array $feedbacks
     * @return array
     */
    public function report($feedback){

        $data = array();

        $data['feedback'] = $feedback;

        return $this->request($this->reportUrl, $data);
    }

    /**
     * 通用接口
     * @param string $url
     * @param array $data
     * @param array header
     * @return array
     */
    public function post($url, $data, $headers=array()){
        return $this->request($url, $data, $headers);
    }

}
