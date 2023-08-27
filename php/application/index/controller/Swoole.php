<?php

namespace app\index\controller;
use think\swoole\Server;
class Swoole extends Server
{
    protected $host = '0.0.0.0'; //监听所有地址
    protected $port = 9502; //监听9502端口
    protected $serverType = 'socket';//使用webscoket;默认是http
    protected $option = [
        'worker_num'=> 4, //设置启动的Worker进程数
        'daemonize'	=> false, //守护进程化（上线改为true）
        'backlog'	=> 128, //Listen队列长度
        'dispatch_mode' => 2, //固定模式，保证同一个连接发来的数据只会被同一个worker处理

        //心跳检测：每60秒遍历所有连接，强制关闭10分钟内没有向服务器发送任何数据的连接
        'heartbeat_check_interval' => 60,
        'heartbeat_idle_time' => 600
    ];

    //建立连接时回调函数
    public function onOpen($server,$req)
    {
        $fd = $req->fd;//客户端标识
        //省略给用户绑定fd逻辑......
        echo "用户{$fd}建立了连接,标识为{$fd}\n";
    }

    //接收数据时回调函数
    public function onMessage($server,$frame)
    {
        $fd = $frame->fd;
        $message = $frame->data;//客户端传递的消息
        $arr =  [];
        //仅推送给当前连接用户
        $server->push($fd, json_encode($arr));
    }

    //连接关闭时回调函数
    public function onClose($server,$fd)
    {
        echo "标识{$fd}关闭了连接\n";
    }
}
