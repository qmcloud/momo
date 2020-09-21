### 直播源码,短视频,直播带货,游戏陪玩,仿比心,猎游,tt语音聊天,美女约玩,陪玩系统源码开黑,约玩源码

----------------
### 后台: laravel-admin 前端: VUE 移动: Android + ios ### 分布式架构 IM,API等: Consul + Go Micro + Micro API + kafka + Docker 等

----------------

**技术群：**
![技术群](https://img-blog.csdnimg.cn/20200623093238797.png)


为了避免发广告及虚假用户，请先Github star此项目，然后附上Github账号(非邮箱)申请入群
纯技术群，入群有一定门槛，谢谢理解。
----------------
微信：BCFind5 【请备注好信息】

博客地址：https://blog.csdn.net/u012115197/article/details/106916635

----------------

### 团队接活

| 类目        | 价格   |  商业授权  |
| --------   | -----:  | :----:  |
| 基础开源版本      | $0        |   无（不可商用）     |
| 授权版本          |   $15000  |   有（可以商用）   |
| 定制，系统优化，重构，商业合作等|    $30000起|  有（可以商用）  |

----------------


----------------
[gihub地址：](https://github.com/DOUBLE-Baller/momo) https://github.com/DOUBLE-Baller/momo

[web演示地址：](http://www.jinqianlive.com) http://www.jinqianlive.com 

[直播后台演示地址：](http://www.jinqianlive.com/admin) http://www.jinqianlive.com/admin

账号 ：test
密码： test

----------------
[直播短视频app下载](https://baoya.lanzous.com/imcL9e57tej) https://baoya.lanzous.com/imcL9e57tej（用手机浏览器打开下载，不要用微信直接下载）

----------------
[陪玩app下载](https://baoya.lanzous.com/izTEKgg0yhi ) https://baoya.lanzous.com/izTEKgg0yhi （用手机浏览器打开下载，不要用微信直接下载）

----------------
IOS 视频演示：https://pan.baidu.com/s/18KaHu-39TMQLetb0m7XD0Q 提取码：v929

----------------

文档地址：http://www.jinqianlive.com/appapi/listAllApis.php?type=expand

----------------

**前端展示**
![前端展示](https://img-blog.csdnimg.cn/20200908194734911.jpg?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3UwMTIxMTUxOTc=,size_16,color_FFFFFF,t_70#pic_center)

**后台界面**
![后台界面](https://img-blog.csdnimg.cn/20200907180807339.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3UwMTIxMTUxOTc=,size_16,color_FFFFFF,t_70#pic_center)


----------------

### 入门推荐书籍

* [FFmpeg从入门到精通](https://book.douban.com/subject/30178432/) - 强烈推荐
* [直播系统开发：基于Nginx与Nginx-rtmp-module](https://book.douban.com/subject/30423374/)
* [WebRTC权威指南](https://book.douban.com/subject/26915289/)
* [CDN技术详解](https://book.douban.com/subject/10759173/)

### 技术结构
# 前端 
- **集小视频/IM聊天/直播等功能于一体的直播项目。界面仿制抖音|火山小视频|陌陌直播|比心陪玩等。**

#### *注意：*前端的具体细技术细节请移至前端篇赘述，本贴暂不描述前端内容。
![在这里插入图片描述](https://img-blog.csdnimg.cn/20200909110034788.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3UwMTIxMTUxOTc=,size_16,color_FFFFFF,t_70#pic_center)

# 后端

**系统开发语言**
-  **PHP 视频互动系统由 WEB 系统、REDIS 服务、MYSQL 服务、视频服务、聊天服务、后台管理系统和定时监控组成，后台管理采用PHP 语言开发，所有服务提供横向扩展。**

1. WEB 系统提供页面、接口逻辑。
2. REDIS 服务提供数据的缓存、存储动态数据。
3. MYSQL 服务提供静态数据的存储。
4. 视频服务提供视频直播，傍路直播，转码、存储、点播等 支持腾讯云 阿里云 七牛等 自建流媒体服务器等（包括两套成熟方案 nginx_rtmp 和 golang的）。
5. golang +kafka 队列 聊天服务提供直播群聊，私聊，消息通知等。
6. consul + grpc + docker 系统监控：监听主播异常掉线情况、直播消息推送等。
 
------------
**分布式架构**

--待续--

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


**点播配置**

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

### 四、部署im
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

### 五、启动im
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
![在这里插入图片描述](https://img-blog.csdnimg.cn/20200908200113599.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3UwMTIxMTUxOTc=,size_16,color_FFFFFF,t_70#pic_center)


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

------------
## PHP 为什么 “慢” ？？？
### Laravel + Swoole 提速 30倍

 - 复用容器
 - 启用协程
 - 数据库连接池

 ----    

>  - 复用容器
>  熟悉 Laravel 的朋友都知道 IoC 容器是整个框架的核心，几乎所有 Laravel 提供的服务都被注册在 IoC 容器中。每当容器启动时，Laravel 就会将大部分服务注册到容器中来，有些服务还会去加载文件，比如配置、路由等，可以说启动容器是比较 “耗时” 的。我们再次观察上面的脚本，可以看到 request 回调的第一行就是创建 IoC 容器（$app），这也意味着每次在处理请求时都会创建一次容器，这样不仅重复执行了许多代码，还造成不小的 IO 开销，所以上述脚本显然不是最优的做法。那我们试试只创建一个容器，再让所有的请求都复用这个容器。我们可以在 worker 进程启动时（也就是 workerStart 回调中）创建并启动容器，这样在 request 回调中就能复用了。现在将 swoole.php 如下：

```php
<?php

require __DIR__.'/vendor/autoload.php';

use HuangYi\Shadowfax\Http\Request;
use HuangYi\Shadowfax\Http\Response;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request as IlluminateRequest;
use Swoole\Http\Server;

$server = new Server('127.0.0.1', 9501);

$server->set([
    'worker_num' => 1,
    'enable_coroutine' => false,
]);

$app = null;

$server->on('workerStart', function () use (&$app) {
    $app = require __DIR__.'/bootstrap/app.php';

    $app->instance('request', IlluminateRequest::create('http://localhost'));

    $app->make(Kernel::class)->bootstrap();
});

$server->on('request', function ($request, $response) use (&$app) {
    $kernel = $app->make(Kernel::class);

    $illuminateResponse = $kernel->handle(
        $illuminateRequest = Request::make($request)->toIlluminate()
    );

    Response::make($illuminateResponse)->send($response);

    $kernel->terminate($illuminateRequest, $illuminateResponse);
});

$server->start();
```
>  - 启用协程
>  协程是 Swoole 的最强武器，也是实现高并发的精髓所在。那么在 Laravel 中使用协程会有问题吗？首先启动 Swoole 的协程特性，将 enable_coroutine 设置为 true 即可，然后在 routes/web.php 里面添加两个路由：

```php
<?php

use Swoole\Coroutine;

app()->singleton('counter', function () {
    $counter = new stdClass;
    $counter->number = 0;

    return $counter;
});

Route::get('one', function () {
    app('counter')->number = 1;

    Coroutine::sleep(5);

    echo sprintf("one: %d\n", app('counter')->number);
});

Route::get('two', function () {
    app('counter')->number = 2;

    Coroutine::sleep(5);

    echo sprintf("two: %d\n", app('counter')->number);
});
```

> 上述代码首先在容器里面注册了一个 counter 单例，路由 one 将 counter 单例的 number 属性设置为 1，然后模拟协程被挂起 5 秒，恢复后打印出 number 属性的值。路由 two 也类似，只是将 number 属性设置为了 2。启动服务器后，我们先访问 one，然后立马访问 two（间隔不要超过 5 秒）。我们可以观察到 Console 输出的信息为：
```shell
one: 2
two: 2
```
>  - 数据库连接池
> 注意： 在协程环境下使用数据库如果不配合连接池，就会造成连接异常。当然，使用 Swoole 的 Channel 来创建连接池非常简单，但是如果直接在业务代码中使用连接池，程序员需要自行控制何时取何时回收，而且还不能使用 Laravel 的 Model 了，这点我是绝对不能接受的。还有一点，由于在业务代码中使用了 Swoole 的接口，这意味着你的程序必须运行在 Swoole 之上，再也无法切回 PHP-FPM 了。Shadowfax 做到了无感知的使用连接池，开发者依然像平时那样用 Model 来查询或者更新数据，唯一需要做的就是将程序中使用到的数据库连接名配置到 db_pools 当中即可。Shadowfax 是如何做到的呢？我们只需要搞清楚一点就能明白原理了，Laravel 中的数据库连接都是通过 Illuminate\Database\DatabaseManager::connection() 方法来获取的，我们可以继承这个类并改造 connection() 方法，如果取的是 db_pools 中配置的连接，那么就从对应的连接池中获取。最后使用这个改造后的类注覆盖原来的 db 服务即可。具体的实现就请阅读源码 。

```php
<?php

namespace HuangYi\Shadowfax\Laravel;

use Illuminate\Database\Connection;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Database\DatabaseManager as LaravelDatabaseManager;

class DatabaseManager extends LaravelDatabaseManager
{
    use HasConnectionPools;

    /**
     * The callback to be executed to reconnect to a database in pool.
     *
     * @var callable
     */
    protected $poolReconnector;

    /**
     * Create a new DatabaseManager instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  \Illuminate\Database\Connectors\ConnectionFactory  $factory
     * @param  array  $poolsConfig
     * @return void
     */
    public function __construct($app, ConnectionFactory $factory, array $poolsConfig = [])
    {
        parent::__construct($app, $factory);

        $this->poolsConfig = $poolsConfig;

        $this->poolReconnector = function ($connection) {
            $this->poolReconnect($connection);
        };
    }

    /**
     * Get a database connection instance.
     *
     * @param  string|null  $name
     * @return \Illuminate\Database\ConnectionInterface
     */
    public function connection($name = null)
    {
        $name = $name ?: $this->getDefaultConnection();

        if (! $this->isPoolConnection($name)) {
            return parent::connection($name);
        }

        if ($connection = $this->getConnectionFromContext($name)) {
            return $connection;
        }

        return $this->getConnectionFromPool($name);
    }

    /**
     * Resolve the connection.
     *
     * @param  string  $name
     * @return \Illuminate\Database\Connection
     */
    protected function resolveConnection($name)
    {
        [$database, $type] = $this->parseConnectionName($name);

        $connection = $this->configure($this->makeConnection($database), $type);

        $connection->setReconnector($this->poolReconnector);

        return $connection;
    }

    /**
     * Get the connection key in coroutine context.
     *
     * @param  string  $name
     * @return string
     */
    protected function getConnectionKeyInContext($name)
    {
        return 'db.connections.'.$name;
    }

    /**
     * Reconnect to the given database.
     *
     * @param  string|null  $name
     * @return \Illuminate\Database\Connection
     */
    public function reconnect($name = null)
    {
        $name = $name ?: $this->getDefaultConnection();

        if (! $this->isPoolConnection($name)) {
            return parent::reconnect($name);
        }
    }

    /**
     * Reconnect the connection in pool.
     *
     * @param  \Illuminate\Database\Connection  $connection
     * @return \Illuminate\Database\Connection
     */
    public function poolReconnect(Connection $connection)
    {
        $connection->disconnect();

        $fresh = $this->makeConnection($connection->getName());

        return $connection
            ->setPdo($fresh->getRawPdo())
            ->setReadPdo($fresh->getRawReadPdo());
    }
}
```



swoole扩展安装
```shell
pecl install swoole
```

swoole框架
```shell
composer install
```
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

切换到PHPWeb项目目录，执行指令composer install，如很慢则

```shell
composer install --prefer-dist
```

__3. Ningx配置HTTPS__

* Apache请参照Nginx配置，自行修改实现
* 这里使用了`www.xxx.com`作为域名，需要配置host或者改成你的域名

```shell
server {
    listen       80;
    server_name  www.xxx.com;
    index index.html index.php;
    
    location / {
        root   /path/to/web/webroot;

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

------------

## 何为 laravel-admin
是一个可以快速帮你构建后台管理的工具，它提供的页面组件和表单元素等功能，能帮助你使用很少的代码就实现功能完善的后台管理功能

**特性**

 - 内置用户和权限系统
 - model-grid支持快速构建数据表格
 - model-form支持快速构建数据表单
 - model-tree支持快速构建树状数据
 - 内置40+种form元素组件、以及支持扩展组件
 - 支持Laravel的多种模型关系
 - mysql、mongodb、pgsql等多数据库支持
 - 支持引入第三方前端库
 - 数据库和artisan命令行工具的web实现
 - 支持自定义图表
 - 多种常用web组件
 - 支持本地和oss文件上传

## 目录结构
```
app/Admin
├── Controllers
│   ├── ExampleController.php
│   └── HomeController.php
├── bootstrap.php
└── routes.php
```
#### 克隆项目

```
git clone https://github.com/DOUBLE-Baller/momo.git
```

#### 进入项目
```shell
cd momo
```

#### 安装功能包
```shell
composer install 
```

#### 复制.env文件
```shell
cp .env.example .env
```

#### 生成密钥
```shell
php artisan key:generate
```

#### 运行迁移
```shell
# 注意修改.env文件的数据库信息
php artisan migrate
```

#### 建立文件链接(如果报错请百度解决，都是小问题)
```shell
php artisan storage:link
```

#### 初始化数据库
```shell
php artisan db:seed-sql 
# 输入yes
```

> 后台地址：http://xxxx/admin 账户：admin，密码：admin

#### 创建后台控制器

```shell
# 以创建用户管理为例
php artisan admin:controller UserController
```

> 注意：控制器里面修改相应模型，变量，视图即可，视图请参考`auth`里面的代码

#### 使用说明

> 搜索和导出功能需自己完善

- 路由文件：app/Admin/routes.php
- 配置文件：config/admin.php
- 语言文件：resources/lang/zh-CN/admin.php
- 后台控制器路径：app/Admin/Controllers
- 后台视图路径：app/Admin/Views
- 视图请参考`auth`里面的代码
- 视图页面代码请参考adminlte2.4文件
- 资源文件：public/plugins
- 获取登录用户`Admin::user()`
- 获取登录用户ID`Admin::id()`
- 判断登录用户是否是某个角色(判断角色标识)`Admin::isAdmin('slug'['slug','slug'])`
- 视图获取登录用户`admin('user')`
- 视图获取登录用户ID`admin('id')`
- 视图判断登录用户是否是某个角色(判断角色标识)`admin('isAdmin','slug'['slug','slug'])`

==问题反馈==

**在使用中有任何问题，欢迎反馈给我们，可以用以下联系方式跟我们交流**


~~整套直播教学视频 进群后@群主索取~~
![在这里插入图片描述](https://img-blog.csdnimg.cn/20200623094714713.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3UwMTIxMTUxOTc=,size_16,color_FFFFFF,t_70#pic_center)

