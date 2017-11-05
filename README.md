# PHP 仿陌陌直播 

## 项目介绍

此项目利用 TP+Redis+Nginx+nginx-rtmp-module+ffmpeg+HLS +Swoole 的架构方案

## 演示地址：



## 后台截图

![截图](http://www.thinkphp.cn/Uploads/editor/2017-09-05/59aebf32e2bd5.png)

## 优点介绍

### 后台nginx-rtmp 安装讲解

现在主要有两种rtmp server，商业的和开源的。商业的比开源的支持的功能多，个人根据需要选择吧

商业的有FMS Wowza

开源RTMP server

1. red5 java   java用的较多，性能还是不错的！

2. crtmpserver c++ 支持多种rtmp协议，移动设备以及IPTV相关网络协议 http://www.rtmpd.com/ Erlyvideo erlong 有开源和商业版本 https//github.com/erlyvideo/erlyvideo h

3. aXeVideo haXe 一个实验性的，轻量级的服务器 http://code.google.com/p/haxevideo/ 

4. FluorineFx .Net To be defined http://www/fluorinefx.com 

5. nginx-rtmp c nginx模块 支持rtmp和HLS https://github.com/arut/nginx-rtmp-module

本人采用的则为第5个 Nginx-rtmp ，接下来讲解 安装过程。

### 安装 Nginx-rtmp

1、下载nginx-rtmp-module：
nginx-rtmp-module的官方github地址：https://github.com/arut/nginx-rtmp-module

使用命令：
git clone https://github.com/arut/nginx-rtmp-module.git  

将nginx-rtmp-module下载到linux中。

2、安装nginx：
nginx的官方网站为：http://nginx.org/en/download.html

wget http://nginx.org/download/nginx-1.8.1.tar.gz  
tar -zxvf nginx-1.8.1.tar.gz  
cd nginx-1.8.1  
./configure --prefix=/usr/local/nginx  --add-module=../nginx-rtmp-module  --with-http_ssl_module    
make && make install  
本次默认安装目录为：/root， add-module为下载的nginx-rtmp-module文件路径。
安装时候可能会报错没有安装openssl，需要执行命令：

yum -y install openssl openssl-devel    

3、修改nginx配置文件：


vi /usr/local/nginx/conf/nginx.conf  

加入以下内容：

rtmp {    
    
    server {    
    
        listen 1935;  #监听的端口  
    
        chunk_size 4000;    
          
           
        application hls {  #rtmp推流请求路径  
            live on;    
            hls on;    
            hls_path /usr/share/nginx/html/hls;    
            hls_fragment 5s;    
        }    
    }    
}  
hls_path需要可读可写的权限。
修改http中的server模块：

server {  
    listen       81;  
    server_name  localhost;  
  
    #charset koi8-r;  
  
    #access_log  logs/host.access.log  main;  
  
    location / {  
        root   /usr/share/nginx/html;  
        index  index.html index.htm;  
    }  
  
    #error_page  404              /404.html;  
  
    # redirect server error pages to the static page /50x.html  
    #  
    error_page   500 502 503 504  /50x.html;  
    location = /50x.html {  
        root   html;  
    }  
当然了，root可以跟据自己的需求来改的。
然后启动nginx:

/usr/local/nginx/sbin/nginx -c /usr/local/nginx/conf/nginx.conf

4、开始推流
做好以上的配置后，就可以开始推流了，我们可以使用obs来推流。

![截图](http://img.blog.csdn.net/20161015104358248?watermark/2/text/aHR0cDovL2Jsb2cuY3Nkbi5uZXQv/font/5a6L5L2T/fontsize/400/fill/I0JBQkFCMA==/dissolve/70/gravity/Center)

在设置->串流 中填写信息：URL为 rtmp://xxx:1935/hls，xxx为你的服务器的IP地址，hls是用来存放流媒体的。
秘钥可以随便填写一个，用来播放的时候识别播放哪个流媒体的，例如填写test等。
填写完毕后，点击开始串流，就说明我们的流媒体服务器搭建成功了。

5、观看直播（拉流）
观看直播就比较简单了，可以简单的使用h5的vedio标签就可以观看了。
可以访问http://xxx:81/hls/mystream.m3u8来观看直播，其中xxx为你的服务器IP地址，
或者使用

<video>    
    <source src="http://xxx:81/hls/test.m3u8"/>    
    <p class="warning">Your browser does not support HTML5 video.</p>    
</video>  

同上， xxx写的是你服务器IP地址。
然后使用手机访问这个网站就能够观看直播了。延迟大概在20S左右。
（在iOS的safari浏览器中可以正常观看）
写在最后
为什么延迟 那么高呢？这是因为服务器将视频流切断成一个个小的以.ts结尾的文件。
!["截图"](http://img.blog.csdn.net/20161015110430369?watermark/2/text/aHR0cDovL2Jsb2cuY3Nkbi5uZXQv/font/5a6L5L2T/fontsize/400/fill/I0JBQkFCMA==/dissolve/70/gravity/Center)

而我们访问的是.m3u8文件，这个文件内容是将一个个ts文件串联起来的，这就达到了一个播放的效果，所以看起来会有很大的延迟。

!["截图"](http://img.blog.csdn.net/20161015110511823?watermark/2/text/aHR0cDovL2Jsb2cuY3Nkbi5uZXQv/font/5a6L5L2T/fontsize/400/fill/I0JBQkFCMA==/dissolve/70/gravity/Center)

如果降低延迟也不是没有方法，可以设置切片生成的大小以及访问的速度，但是这样大大增加了服务器的压力。
当然，我们也可以用rtmp拉流工具（VLC等）来看该直播，延迟大概在2-5S左右，拉流地址与推流地址一致。

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

* QQ群: 274904994

##如果帮到你请打赏小弟一下！十分感激

!["打赏一下"](http://group.store.qq.com/qun/M5CXvbshIHEJdIzwzpGpEg!!/V3tordiEAAItFnC*GMt/800?w5=612&h5=624&rf=viewer_421) 

##文档会持续更新

