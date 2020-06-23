<?php
namespace Qiniu\Pili;

use \Qiniu\Pili\HttpRequest;
use \Qiniu\Pili\Utils;


class Hub
{
    private $_hub;
    private $_baseURL;
    private $_transport;

    public function __construct($mac, $hubName)
    {
        $this->_hub = $hubName;
        $this->_transport = new Transport($mac);

        $cfg = Config::getInstance();
        $protocal = $cfg->USE_HTTPS === true ? "https" : "http";
        $this->_baseURL = $protocal . "://" . $cfg->API_HOST . "/v2/hubs/" . $this->_hub;
    }

    //创建一个流对象.
    /*
     * PARAM
     * @streamKey: 流名.
     * RETURN
     * 返回一个流对象.
     */
    public function create($streamKey)
    {
        $url = $this->_baseURL . "/streams";
        $params['key'] = $streamKey;
        $body = json_encode($params);
        try {
            $this->_transport->send(HttpRequest::POST, $url, $body);
        } catch (\Exception $e) {
            return $e;
        }

        return new Stream($this->_transport, $this->_hub, $streamKey);
    }

    //初始化一个流对象
    /*
     * PARAM
     * @streamKey: 流名.
     * RETURN
     * 返回一个流对象.
     */
    public function stream($streamKey)
    {
        return new Stream($this->_transport, $this->_hub, $streamKey);
    }

    private function _list($live, $prefix, $limit, $marker)
    {
        $url = sprintf("%s/streams?liveonly=%s&prefix=%s&limit=%d&marker=%s", $this->_baseURL, $live, $prefix, $limit, $marker);
        try {
            $ret = $this->_transport->send(HttpRequest::GET, $url);
        } catch (\Exception $e) {
            return $e;
        }
        $keys = array();
        foreach ($ret["items"] as $item) {
            array_push($keys, $item["key"]);
        }
        $ret = array();
        $ret["keys"] = $keys;
        $ret["omarker"] = $marker;
        return $ret;
    }

    //根据 prefix 遍历 Hub 的正在直播的流列表.
    /*
     * PARAM
     * @prefix: 流名的前缀.
     * @limit: 限定了一次最多可以返回的流个数, 实际返回的流个数可能小于这个 limit 值.
     * @marker: 是上一次遍历得到的流标.
     * RETURN
     * @keys: 流名的数组.
     * @omarker: 记录了此次遍历到的游标, 在下次请求时应该带上, 如果 omarker 为 "" 表示已经遍历完所有流.
     */
    public function listLiveStreams($prefix, $limit, $marker)
    {
        return $this->_list("true", $prefix, $limit, $marker);
    }

    //根据 prefix 遍历 Hub 的流列表.
    /*
     * PARAM
     * @prefix: 流名的前缀.
     * @limit: 限定了一次最多可以返回的流个数, 实际返回的流个数可能小于这个 limit 值.
     * @marker: 是上一次遍历得到的流标.
     * RETURN
     * @keys: 流名的数组.
     * @omarker: 记录了此次遍历到的游标, 在下次请求时应该带上, 如果 omarker 为 "" 表示已经遍历完所有流.
     */
    public function listStreams($prefix, $limit, $marker)
    {
        return $this->_list("false", $prefix, $limit, $marker);
    }

    //批量查询流直播信息.
    /*
     * PARAM
     * @streamKeys: 流名数组, 最大长度为100.
     * RETURN
     * @items: 数组. 每个item包含一个流的直播信息.
     *   @key: 流名.
     *   @startAt: 直播开始的 Unix 时间戳, 0 表示当前没在直播.
     *   @clientIP: 直播的客户端 IP.
     *   @bps: 直播的码率.
     *   @fps: 直播的帧率.
     */
    public function batchLiveStatus($streamKeys)
    {
        $url = $this->_baseURL . "/livestreams";
        $params['items'] = $streamKeys;
        $body = json_encode($params);
        return $this->_transport->send(HttpRequest::POST, $url, $body);
    }
}

//----------------url
//生成 RTMP 推流地址.
//expireAfterSeconds 表示 URL 在多久之后失效.
function RTMPPublishURL($domain, $hub, $streamKey, $expireAfterSeconds, $accessKey, $secretKey)
{
    $expire = time() + $expireAfterSeconds;
    $path = sprintf("/%s/%s?e=%d", $hub, $streamKey, $expire);
    $token = $accessKey . ":" . Utils::sign($secretKey, $path);
    return sprintf("rtmp://%s%s&token=%s", $domain, $path, $token);
}

//生成 RTMP 直播地址.
function RTMPPlayURL($domain, $hub, $streamKey)
{
    return sprintf("rtmp://%s/%s/%s", $domain, $hub, $streamKey);
}

//生成 HLS 直播地址.
function HLSPlayURL($domain, $hub, $streamKey)
{
    return sprintf("http://%s/%s/%s.m3u8", $domain, $hub, $streamKey);
}

//生成 HDL 直播地址.
function HDLPlayURL($domain, $hub, $streamKey)
{
    return sprintf("http://%s/%s/%s.flv", $domain, $hub, $streamKey);
}

//生成直播封面地址.
function SnapshotPlayURL($domain, $hub, $streamKey)
{
    return sprintf("http://%s/%s/%s.jpg", $domain, $hub, $streamKey);
}

?>
