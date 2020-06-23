<?php
namespace Qiniu\Pili;

use \Qiniu\Pili\Utils;
use \Qiniu\Pili\HttpRequest;

class Stream
{
    private $_transport;
    private $_hub;
    private $_key;
    private $_baseURL;

    public function __construct($transport, $hub, $key)
    {
        $this->_transport = $transport;
        $this->_hub = $hub;
        $this->_key = $key;

        $cfg = Config::getInstance();
        $protocal = $cfg->USE_HTTPS === true ? "https" : "http";
        $this->_baseURL = sprintf("%s://%s/%s/hubs/%s/streams/%s", $protocal, $cfg->API_HOST, $cfg->API_VERSION, $this->_hub, Utils::base64UrlEncode($this->_key));
    }

    //获得流信息.
    /*
     * RETURN
     * @hub: Hub名.
     * @key: 流名.
     * @disableTill: 禁用结束的时间, 0 表示不禁用, -1 表示永久禁用.
     * @converts: 实时转码规格.
    */
    public function info()
    {
        $resp=$this->_transport->send(HttpRequest::GET, $this->_baseURL);
        $ret = array();
        $ret["hub"] = $this->_hub;
        $ret["key"] = $this->_key;
        $ret["disabledTill"] = $resp["disabledTill"];
        $ret["converts"] = $resp["converts"];
        return $ret;
    }

    //在一定期限内禁止一个流.
    /*
     * PARAM
     * @till: Unix 时间戳, 在这之前流均不可用.
     */
    public function disable($till = NULL)
    {
        $url = $this->_baseURL . "/disabled";
        if (empty($till)) {
            $params['disabledTill'] = -1;
        } else {
            $params['disabledTill'] = $till;
        }
        $body = json_encode($params);
        return $this->_transport->send(HttpRequest::POST, $url, $body);
    }

    //启用一个流.
    public function enable()
    {
        $url = $this->_baseURL . "/disabled";
        $params['disabledTill'] = 0;
        $body = json_encode($params);
        return $this->_transport->send(HttpRequest::POST, $url, $body);
    }

    //查询直播状态.
    /*
     * RETURN
     * @startAt: 直播开始的 Unix 时间戳, 0 表示当前没在直播.
     * @clientIP: 直播的客户端 IP.
     * @bps: 直播的码率.
     * @fps: 直播的帧率.
     */
    public function liveStatus()
    {
        $url = $this->_baseURL . "/live";
        return $this->_transport->send(HttpRequest::GET, $url);
    }

    //查询直播历史.
    /*
     * PARAM
     * @start: Unix 时间戳, 限定了查询的时间范围, 0 值表示不限定, 系统会返回所有时间的直播历史.
     * @end: Unix 时间戳, 限定了查询的时间范围, 0 值表示不限定, 系统会返回所有时间的直播历史.
     * RETURN
     * @items: 数组. 每个item包含一次推流的开始及结束时间.
     *   @start: Unix 时间戳, 直播开始时间.
     *   @end: Unix 时间戳, 直播结束时间.
     */
    public function historyActivity($start = NULL, $end = NULL)
    {
        $url = $this->_baseURL . "/historyrecord";
        $flag = "?";
        if (!empty($start)) {
            $url = $url . $flag . "start=" . $start;
            $flag = "&";
        }
        if (!empty($end)) {
            $url = $url . $flag . "end=" . $end;
        }
        return $this->_transport->send(HttpRequest::GET, $url);
    }

    //保存直播回放.
    /*
     * PARAM
     * @start: Unix 时间戳, 起始时间, 0 值表示不指定, 则不限制起始时间.
     * @end: Unix 时间戳, 结束时间, 0 值表示当前时间.
     * RETURN
     * @fname: 保存到bucket里的文件名, 由系统生成.
     */
    public function save($start = NULL, $end = NULL)
    {
        $url = $this->_baseURL . "/saveas";
        if (!empty($start)) {
            $params['start'] = $start;
        }
        if (!empty($end)) {
            $params['end'] = $end;
        }
        $body = json_encode($params);
        return $this->_transport->send(HttpRequest::POST, $url, $body);
    }

    //灵活度更高的保存直播回放.
    /*
     * PARAM
     * @fname: 保存的文件名, 不指定会随机生成.
     * @start: Unix 时间戳, 起始时间, 0 值表示不指定, 则不限制起始时间.
     * @end: Unix 时间戳, 结束时间, 0 值表示当前时间.
     * @format: 保存的文件格式, 默认为m3u8.
     * @pipeline: dora 的私有队列, 不指定则用默认队列.
     * @notify: 保存成功后的回调地址.
     * @expireDays: 对应ts文件的过期时间.
     *   -1 表示不修改ts文件的expire属性.
     *   0  表示修改ts文件生命周期为永久保存.
     *   >0 表示修改ts文件的的生命周期为ExpireDays.
     * RETURN
     * @fname: 保存到bucket里的文件名.
     * @persistentID: 异步模式时，持久化异步处理任务ID，通常用不到该字段.
     */
    public function saveas($options = NULL)
    {
        $url = $this->_baseURL . "/saveas";
        if (!empty($options)) {
            $body = json_encode($options);
        } else {
            $body = NULL;
        }
        return $this->_transport->send(HttpRequest::POST, $url, $body);
    }

    //对流进行截图并保存.
    /*
     * PARAM
     * @fname: 保存的文件名, 不指定会随机生成.
     * @time: Unix 时间戳, 保存的时间点, 默认为当前时间.
     * @format: 保存的文件格式, 默认为jpg.
     * RETURN
     * @fname: 保存到bucket里的文件名.
     */
    public function snapshot($options = NULL)
    {
        $url = $this->_baseURL . "/snapshot";
        if (!empty($options)) {
            $body = json_encode($options);
        } else {
            $body = NULL;
        }
        return $this->_transport->send(HttpRequest::POST, $url, $body);
    }

    //更改流的实时转码规格
    /*
     * PARAM
     * @profiles: 实时转码规格. array("480p", "720p")
     */
    public function updateConverts($profiles)
    {
        $url = $this->_baseURL . "/converts";
        $params['converts'] = $profiles;
        $body = json_encode($params);
        return $this->_transport->send(HttpRequest::POST, $url, $body);
    }


}

?>
