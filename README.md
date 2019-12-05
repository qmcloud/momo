# PHP 打造 直播短视频社交系统
**技术交流群：**

![二维码](https://github.com/DOUBLE-Baller/momo/blob/master/11111.png?raw=true) 

----------------

### 系统截图
![](https://github.com/DOUBLE-Baller/momo/blob/master/1289798-20191111142706873-2058201438.png?raw=true)

----------------

### 入门推荐书籍

* [FFmpeg从入门到精通](https://book.douban.com/subject/30178432/) - 强烈推荐
* [直播系统开发：基于Nginx与Nginx-rtmp-module](https://book.douban.com/subject/30423374/)
* [WebRTC权威指南](https://book.douban.com/subject/26915289/)
* [CDN技术详解](https://book.douban.com/subject/10759173/)

### 技术结构
# 前端
- **基于VUE+UNI-APP技术开发的集小视频/IM聊天/直播等功能于一体的微直播项目。界面仿制抖音|火山小视频/陌陌直播，支持编译到多端(H5、小程序、APP端) 且兼容效果一致。**

![](https://github.com/DOUBLE-Baller/momo/blob/master/1289798-20191111141512652-843587761.png?raw=true)

![](https://github.com/DOUBLE-Baller/momo/blob/master/1289798-20191111142553532-187838933.png?raw=true)

![](https://github.com/DOUBLE-Baller/momo/blob/master/1289798-20191111142613325-253123084.png?raw=true)

![](https://github.com/DOUBLE-Baller/momo/blob/master/1289798-20191111142853823-1326367501.png?raw=true)

![](https://github.com/DOUBLE-Baller/momo/blob/master/1289798-20191111143027700-207603792.png?raw=true)


- **仿抖音小视频效果**

**项目中小视频界面功能效果类似抖音/火山小视频，使用SWIPER组件实现上下滑动切换视频播放。**

![](https://github.com/DOUBLE-Baller/momo/blob/master/usjhmo1lwc.png?raw=true)

```html
<swiper :indicator-dots="false" :duration="200" :vertical="true" :current="videoIndex" @change="handleSlider" style="height: 100%;">
    <block v-for="(item,index) in vlist" :key="index">
        <swiper-item>
            <view class="uni_vdplayer">
                <video :id="'myVideo' + index" :ref="'myVideo' + index" class="player-video" :src="item.src" 
                :controls="false" :loop="true" :show-center-play-btn="false" objectFit="fill">
                </video>
                <!-- 中间播放按钮 -->
                <view class="vd-cover flexbox" @click="handleClicked(index)"><text v-if="!isPlay" class="iconfont icon-bofang"></text></view>
                <!-- 底部信息 -->
                <view class="vd-footToolbar flexbox flex_alignb">
                    <view class="vd-info flex1">
                        <view class="item at">
                            <view class="kw" v-for="(kwItem,kwIndex) in item.keyword" :key="kwIndex"><text class="bold fs_18 mr_5">＃</text> {{kwItem}}</view>
                        </view>
                        <view class="item subtext">{{item.subtitle}}</view>
                        <view class="item uinfo flexbox flex_alignc">
                            <image class="avator" :src="item.avator" mode="aspectFill" /><text class="name">{{item.author}}</text> <text class="btn-attention bg_linear1" :class="item.attention ? 'on' : ''" @tap="handleAttention(index)">{{item.attention ? '已关注' : '关注'}}</text>
                        </view>
                        <view class="item reply" @tap="handleVideoComment"><text class="iconfont icon-pinglun mr_5"></text> 写评论...</view>
                    </view>
                    <view class="vd-sidebar">
                        <view v-if="item.cart" class="ls cart flexbox bg_linear3" @tap="handleVideoCart(index)"><text class="iconfont icon-cart"></text></view>
                        <view class="ls" @tap="handleIsLike(index)"><text class="iconfont icon-like" :class="item.islike ? 'like' : ''"></text><text class="num">{{ item.likeNum+(item.islike ? 1: 0) }}</text></view>
                        <view class="ls" @tap="handleVideoComment"><text class="iconfont icon-liuyan"></text><text class="num">{{item.replyNum}}</text></view>
                        <view class="ls"><text class="iconfont icon-share"></text><text class="num">{{item.shareNum}}</text></view>
                    </view>
                </view>
            </view>
        </swiper-item>
    </block>
</swiper>
```

**视频滑动切换 播放、暂停 及单击/双击判断，商品及评论展示**

    <script>
        // 引入商品广告、评论
        import videoCart from '@/components/cp-video/cart.vue'
        import videoComment from '@/components/cp-video/comment'
        
        let timer = null
        export default {
            data() {
                return {
                    videoIndex: 0,
                    vlist: videoJson,
                    
                    isPlay: true,    //当前视频是否播放中
                    clickNum: 0,    //记录点击次数
                }
            },
            components: {
                videoCart, videoComment
            },
            onLoad(option) {
                this.videoIndex = parseInt(option.index)
            },
            onReady() {
                this.init()
            },
            methods: {
                init() {
                    this.videoContextList = []
                    for(var i = 0; i < this.vlist.length; i++) {
                        // this.videoContextList.push(this.$refs['myVideo' + i][0])
                        this.videoContextList.push(uni.createVideoContext('myVideo' + i, this));
                    }
                    
                    setTimeout(() => {
                        this.play(this.videoIndex)
                    }, 200)
                },
                
                // 滑动切换
                handleSlider(e) {
                    let curIndex = e.detail.current
                    if(this.videoIndex >= 0){
                        this.videoContextList[this.videoIndex].pause()
                        this.videoContextList[this.videoIndex].seek(0)
                        this.isPlay = false
                    }
                    if(curIndex === this.videoIndex + 1) {
                        this.videoContextList[this.videoIndex + 1].play()
                        this.isPlay = true
                    }else if(curIndex === this.videoIndex - 1) {
                        this.videoContextList[this.videoIndex - 1].play()
                        this.isPlay = true
                    }
                    this.videoIndex = curIndex
                },
                // 播放
                play(index) {
                    this.videoContextList[index].play()
                    this.isPlay = true
                },
                // 暂停
                pause(index) {
                    this.videoContextList[index].pause()
                    this.isPlay = false
                },
                // 点击视频事件
                handleClicked(index) {
                    if(timer){
                        clearTimeout(timer)
                    }
                    this.clickNum++
                    timer = setTimeout(() => {
                        if(this.clickNum >= 2){
                            console.log('双击视频')
                        }else{
                            console.log('单击视频')
                            if(this.isPlay){
                                this.pause(index)
                            }else{
                                this.play(index)
                            }
                        }
                        this.clickNum = 0
                    }, 300)
                },
                
                
                // 喜欢
                handleIsLike(index){
                    let vlist = this.vlist
                    vlist[index].islike =! vlist[index].islike
                    this.vlist = vlist
                },
                // 显示评论
                handleVideoComment() {
                    this.$refs.videoComment.show()
                },
                
                // 显示购物车
                handleVideoCart(index) {
                    this.$refs.videoCart.show(index)
                },
            }
        }
    </script>
------------
# 后端

**系统架构**

![](https://github.com/DOUBLE-Baller/momo/blob/master/20191120154941.jpg?raw=true)

**系统开发语言**
-  **PHP 视频互动系统由 WEB 系统、REDIS 服务、MYSQL 服务、视频服务、聊天服务、后台管理系统和定时监控组成，后台管理采用PHP 语言开发，所有服务提供横向扩展。**

1. WEB 系统提供页面、接口逻辑。
2. REDIS 服务提供数据的缓存、存储动态数据。
3. MYSQL 服务提供静态数据的存储。
4. 视频服务提供视频直播，傍路直播，转码、存储、点播等。
5. 聊天服务提供直播群聊，私聊，消息通知等。
6. 定时监控：监听主播异常掉线情况、直播消息推送等。


------------


**视频服务**

**直播配置**

**RTMP服务添加一个application这个名字可以任意起，也可以起多个名字，由于是直播我就叫做它live，如果打算弄多个序列的直播就可以live_cctv。**
    #user  nobody;
    worker_processes  1;
    
    #error_log  logs/error.log;
    #error_log  logs/error.log  notice;
    #error_log  logs/error.log  info;
    
    #pid        logs/nginx.pid;
    
    
    events {
        worker_connections  1024;
    }
    
    rtmp {  #RTMP server
        server {    
            listen 1935;  #server port
            chunk_size 4096;  #chunk_size
            # vod server
            application vod {
                play /mnt/hgfs/dn_class/vod; #media file position
            }
            # live server 1
        application live{ #Darren live first add
            live on;
        }
            # live server 2
        application live_cctv{ #Darren live  add
            live on;
        }
        }
    }
    ........
    其他配置不需理会

**在Ubuntu端用ffmpeg产生一个模拟直播源，向rtmp服务器推送**

**推流**

`ffmpeg -re -i /mnt/hgfs/dn_class/vod/35.mp4 -c copy -f flv rtmp://192.168.100.33/live/35`

*注意，源文件必须是H.264+AAC编码的*

**拉流**

`ffplay rtmp://192.168.100.33/live/35`

######点播配置

1. 建立媒体文件夹`/mnt/hgfs/dn_class/vod`
**把媒体文件 35.mp4复制到/mnt/hgfs/dn_class/vod目录下。
然后我们就可以开启一个视频点播的服务了。打开配置文件nginx.conf（路径/usr/local/nginx/conf/nginx.conf），添加RTMP的配置。**

```
    #user  nobody;
    worker_processes  1;
    
    #error_log  logs/error.log;
    #error_log  logs/error.log  notice;
    #error_log  logs/error.log  info;
    
    #pid        logs/nginx.pid;
    
    
    events {
        worker_connections  1024;
    }
    
    rtmp {  #RTMP server
        server {    
            listen 1935;  #server port
            chunk_size 4096;  #chunk_size
            application vod {
                play /mnt/hgfs/dn_class/vod; #media file position
            }
        }
    }
    ........
    其他配置不需理会
```			
------------


**聊天服务**

#### 特性
 * 轻量级
 * 高性能
 * 纯Golang实现
 * 支持单个、多个、单房间以及广播消息推送
 * 支持单个Key多个订阅者（可限制订阅者最大人数）
 * 心跳支持（应用心跳和tcp、keepalive）
 * 支持安全验证（未授权用户不能订阅）
 * 多协议支持（websocket，tcp）
 * 可拓扑的架构（job、logic模块可动态无限扩展）
 * 基于Kafka做异步消息推送

## 安装
### 一、安装依赖
```sh
$ yum -y install java-1.7.0-openjdk
```

### 二、安装Kafka消息队列服务

kafka在官网已经描述的非常详细，在这里就不过多说明，安装、启动请查看[这里](http://kafka.apache.org/documentation.html#quickstart).

### 三、搭建golang环境
1.下载源码(根据自己的系统下载对应的[安装包](http://golang.org/dl/))
```sh
$ cd /data/programfiles
$ wget -c --no-check-certificate https://storage.googleapis.com/golang/go1.5.2.linux-amd64.tar.gz
$ tar -xvf go1.5.2.linux-amd64.tar.gz -C /usr/local
```
2.配置GO环境变量
(这里我加在/etc/profile.d/golang.sh)
```sh
$ vi /etc/profile.d/golang.sh
# 将以下环境变量添加到profile最后面
export GOROOT=/usr/local/go
export PATH=$PATH:$GOROOT/bin
export GOPATH=/data/apps/go
$ source /etc/profile
```

### 四、部署goim
1.下载goim及依赖包
```sh
$ yum install hg
$ go get -u github.com/Terry-Mao/goim
$ mv $GOPATH/src/github.com/Terry-Mao/goim $GOPATH/src/goim
$ cd $GOPATH/src/goim
$ go get ./...
```

2.安装router、logic、comet、job模块(配置文件请依据实际机器环境配置)
```sh
$ cd $GOPATH/src/goim/router
$ go install
$ cp router-example.conf $GOPATH/bin/router.conf
$ cp router-log.xml $GOPATH/bin/
$ cd ../logic/
$ go install
$ cp logic-example.conf $GOPATH/bin/logic.conf
$ cp logic-log.xml $GOPATH/bin/
$ cd ../comet/
$ go install
$ cp comet-example.conf $GOPATH/bin/comet.conf
$ cp comet-log.xml $GOPATH/bin/
$ cd ../logic/job/
$ go install
$ cp job-example.conf $GOPATH/bin/job.conf
$ cp job-log.xml $GOPATH/bin/
```
到此所有的环境都搭建完成！

### 五、启动goim
```sh
$ cd /$GOPATH/bin
$ nohup $GOPATH/bin/router -c $GOPATH/bin/router.conf 2>&1 > /data/logs/goim/panic-router.log &
$ nohup $GOPATH/bin/logic -c $GOPATH/bin/logic.conf 2>&1 > /data/logs/goim/panic-logic.log &
$ nohup $GOPATH/bin/comet -c $GOPATH/bin/comet.conf 2>&1 > /data/logs/goim/panic-comet.log &
$ nohup $GOPATH/bin/job -c $GOPATH/bin/job.conf 2>&1 > /data/logs/goim/panic-job.log &
```
如果启动失败，默认配置可通过查看panic-xxx.log日志文件来排查各个模块问题.

### 六、测试
## Arch
![benchmark](https://github.com/DOUBLE-Baller/WebRTC_IM/raw/master/goim-server/docs/arch.png)

### Benchmark Server
| CPU | Memory | OS | Instance |
| :---- | :---- | :---- | :---- |
| Intel(R) Xeon(R) CPU E5-2630 v2 @ 2.60GHz  | DDR3 32GB | Debian GNU/Linux 8 | 1 |

### Benchmark Case
* Online: 1,000,000
* Duration: 15min
* Push Speed: 40/s (broadcast room)
* Push Message: {"test":1}
* Received calc mode: 1s per times, total 30 times

### Benchmark Resource
* CPU: 2000%~2300%
* Memory: 14GB
* GC Pause: 504ms
* Network: Incoming(450MBit/s), Outgoing(4.39GBit/s)

### Benchmark Result
* Received: 35,900,000/s

推送协议可查看[push http协议文档](./docs/push.md)

## 配置

TODO

## 例子

Websocket: [Websocket Client Demo](https://github.com/DOUBLE-Baller/WebRTC_IM/tree/master/im/examples/javascript)

Android: [Android](https://github.com/DOUBLE-Baller/WebRTC_IM/tree/master/goim-java-sdk)

iOS: [iOS](https://github.com/DOUBLE-Baller/WebRTC_IM/tree/master/goim-oc-sdk)

## 文档
[push http协议文档](./docs/push.md)推送接口

## 集群

### comet

comet 属于接入层，非常容易扩展，直接开启多个comet节点，修改配置文件中的base节点下的server.id修改成不同值（注意一定要保证不同的comet进程值唯一），前端接入可以使用LVS 或者 DNS来转发

### logic

logic 属于无状态的逻辑层，可以随意增加节点，使用nginx upstream来扩展http接口，内部rpc部分，可以使用LVS四层转发

### kafka

kafka 可以使用多broker，或者多partition来扩展队列

### router

router 属于有状态节点，logic可以使用一致性hash配置节点，增加多个router节点（目前还不支持动态扩容），提前预估好在线和压力情况

### job

job 根据kafka的partition来扩展多job工作方式，具体可以参考下kafka的partition负载


### 使用PHP+Swoole实现的网页即时聊天工具，

* 全异步非阻塞Server，可以同时支持数百万TCP连接在线
* 基于websocket+flash_websocket支持所有浏览器/客户端/移动端
* 支持单聊/群聊/组聊等功能
* 支持永久保存聊天记录，使用MySQL存储
* 基于Server PUSH的即时内容更新，登录/登出/状态变更/消息等会内容即时更新
* 用户列表和在线信息使用Redis存储
* 支持发送连接/图片/语音/视频/文件
* 支持Web端直接管理所有在线用户和群组


 ----
|安装| 
 ----
swoole扩展
```shell
pecl install swoole
```

swoole框架
```shell
composer install
```

运行
----
将`webroot`目录配置到Nginx/Apache的虚拟主机目录中，使`webroot/`可访问。

详细部署说明
----

__1. 安装composer(php依赖包工具)__

```shell
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
```

注意：如果未将php解释器程序设置为环境变量PATH中，需要设置。因为composer文件第一行为#!/usr/bin/env php，并不能修改。
更加详细的对composer说明：http://blog.csdn.net/zzulp/article/details/18981029

__2. composer install__

切换到PHPWebIM项目目录，执行指令composer install，如很慢则

```shell
composer install --prefer-dist
```

__3. Ningx配置__

* 这里未使用swoole_framework提供的Web AppServer  
* Apache请参照Nginx配置，自行修改实现
* 这里使用了`im.swoole.com`作为域名，需要配置host或者改成你的域名

```shell
server {
    listen       80;
    server_name  im.swoole.com;
    index index.html index.php;
    
    location / {
        root   /path/to/webim/webroot;

        proxy_set_header X-Real-IP $remote_addr;
        if (!-e $request_filename) {
            rewrite ^/(.*)$ /index.php;
        }
    }
    
    location ~ .*\.(php|php5)?$ {
	    fastcgi_pass  127.0.0.1:9000;
	    fastcgi_index index.php;
	    include fastcgi.conf;
    }
}
```
`**注意：https下必须采取wss  So-有两种方案 1.采用nginx 反向代理4431端口 swoole 的端口和4431进行通讯。2.swoole 确认是否启用了openssl，是否在编译时加入了--enable-openssl的支持,然后在set 证书路径即可。两种方案选择其一就好，不过第一种方案有个潜在神坑就是你通过反向代理拿不到真实的IP地址了,这点值得注意，Nginx有办法拿到真实的ip，不懂可以私聊我，光wss的坑太多了就不一一说了。**`  
__4. 修改配置__

* 配置`configs/db.php`中数据库信息，将聊天记录存储到MySQL中
* 配置`configs/redis.php`中的Redis服务器信息，将用户列表和信息存到Redis中

表结构
```sql
CREATE TABLE `webim_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `name` varchar(64) COLLATE utf8mb4_bin NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `type` varchar(12) COLLATE utf8mb4_bin NOT NULL,
  `msg` text COLLATE utf8mb4_bin NOT NULL,
  `send_ip` varchar(20) COLLATE utf8mb4_bin,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin
```

* 修改`configs/webim.php`中的选项，设置服务器的URL和端口
```php
$config['server'] = array(
    //监听的HOST
    'host' => '0.0.0.0',
    //监听的端口
    'port' => '9503',
    //WebSocket的URL地址，供浏览器使用的
    'url' => 'ws://im.xxx.com:9503',
    //用于Comet跨域，必须设置为web页面的URL
    //比如你的网站静态页面放在 http://im.xxx.com:8888/main.html
    //这里就是 http://im.xxx.com:8888
    'origin' => 'http://im.xxx.com:8888',
);
```

* server.host server.port 项为WebIM服务器即WebSocket服务器的IP与端口，其他选择项根据具体情况修改
* server.url对应的就是服务器IP或域名以及websocket服务的端口，这个就是提供给浏览器的WebSocket地址
* server.origin为Comet跨域设置，必须修改origin才可以支持IE等不支持WebSocket的浏览器

__5. 启动WebSocket服务器__

```shell
php server.php start 
```

IE浏览器不支持WebSocket，需要使用FlashWebSocket模拟，请修改flash_policy.php中对应的端口，然后启动flash_policy.php。
```shell
php webim/flash_policy.php
```

__6. 绑定host与访问聊天窗口（可选）__

如果URL直接使用IP:PORT，这里不需要设置。

```shell
vi /etc/hosts
```

快速了解项目架构
----

1.目录结构

```
+ webim
  |- server.php //WebSocket协议服务器
  |+ swoole.ini // WebSocket协议实现配置
  |+ configs //配置文件目录
  |+ webroot
    |+ static
    |- config.js // WebSocket配置
  |+ log // swoole日志及WebIM日志
  |+ src // WebIM 类文件储存目录
    |+ Store
      |- File.php // 默认用内存tmpfs文件系统(linux /dev/shm)存放天着数据，如果不是linux请手动修改$shm_dir
      |- Redis.php // 将聊天数据存放到Redis
    |- Server.php // 继承实现WebSocket的类，完成某些业务功能
  |+ vendor // 依赖包目录
```

2.Socket Server与Socket Client通信数据格式

如：登录

Client发送数据

```js
{"cmd":"login","name":"xdy","avatar":"http://tp3.sinaimg.cn/1586005914/50/5649388281/1"}
```

Server响应登录

```js
{"cmd":"login", "fd": "31", "name":"xdy","avatar":"http://tp3.sinaimg.cn/1586005914/50/5649388281/1"}
```

可以看到cmd属性，client与server发送时数据都有指定，主要是用于client或者server的回调处理函数。

3.需要理清的几种协议或者服务的关系

http协议：超文本传输协议。单工通信，等着客户端请求之后响应。

WebSocket协议：是HTML5一种新的协议，它是实现了浏览器与服务器全双工通信。服务器端口与客户端都可以推拉数据。

Web服务器：此项目中可以用基于Swoole的App Server充当Web服务器，也可以用传统的nginx/apache作为web服务器

Socket服务器：此项目中浏览器的WebSocket客户端连接的服务器，swoole_framework中有实现WebSocket协议PHP版本的服务器。

WebSocket Client：实现html5的浏览器都支持WebSocket对象，如不支持此项目中有提供flash版本的实现。


------------

### 后台一键安装 直接访问入口即可 初始admin admin 
采用Bootstrap3精确定制的lyui除了拥有100%bootstrap体验外，融合了更多适合国人使用的前端组建。并且一套代码适应多种屏幕大小。

## 目录结构
```
├─index.php 入口文件
│
├─Addons 插件目录
├─Application 应用模块目录
│  ├─Admin 后台模块
│  │  ├─Conf 后台配置文件目录
│  │  ├─Common 后台函数目录
│  │  ├─Controller 后台控制器目录
│  │  ├─Model 后台模型目录
│  │  └─View 后台视图文件目录
│  │
│  ├─Common 公共模块目录（不能直接访问）
│  │  ├─Behavior 行为扩展目录
│  │  ├─Builder Builder目录
│  │  ├─Common 公共函数文件目录
│  │  ├─Conf 公共配置文件目录
│  │  ├─Controller 公共控制器目录
│  │  ├─Model 公共模型目录
│  │  └─Util 第三方类库目录
│  │
│  ├─Home 前台模块
│  │  ├─Conf 前台配置文件目录
│  │  ├─Common 前台函数目录
│  │  ├─Controller 前台控制器目录
│  │  ├─Model 前台模型目录
│  │  ├─TagLib 前台标签库目录
│  │  └─View 模块视图文件目录
│  │
│  ├─Install 安装模块
│  │  ├─Conf 配置文件目录
│  │  ├─Common 函数目录
│  │  ├─Controller 控制器目录
│  │  ├─Model 模型目录
│  │  └─View 模块视图文件目录
│  │
│  └─... 扩展的可装卸功能模块
│
├─Public 应用资源文件目录
│  ├─libs 第三方插件类库目录
│  ├─css gulp编译样式结果存放目录
│  └─js gulp编译脚本结果存放目录
│
├─Runtime 应用运行时目录
├─Framework 框架目录
└─Uploads 上传根目录
```

##问题反馈

在使用中有任何问题，欢迎反馈给我们，可以用以下联系方式跟我们交流

* QQ群:274904994
* QQ:779763764

##另有整套直播教学视频提供
![另有整套直播教学视频提供](https://github.com/DOUBLE-Baller/momo/blob/master/Uploads/zb120191120163102.png?raw=true)
##打赏一下
![](https://github.com/DOUBLE-Baller/momo/blob/master/Uploads/%E5%BE%AE%E4%BF%A1%E5%9B%BE%E7%89%87_20191205162328.jpg?raw=true)


