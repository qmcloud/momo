**[HOME](Home) > [CN](#)**

![](http://ossrs.net/gif/v1/sls.gif?site=github.com&path=/wiki/v5_CN_Home)
[![](https://ossrs.net/wiki/images/wechat-badge4.svg)](Contact#wechat)
[![](https://ossrs.net/wiki/images/srs-faq.svg)](https://github.com/ossrs/srs/issues/2716)

> Note: 如果觉得Github的Wiki访问太慢，可以访问 [Gitee](https://gitee.com/ossrs/srs/wikis/v5_CN_Home) 镜像。

> 注意：SRS5属于开发版，不稳定。

## SRS Overview

SRS是一个简单高效的实时视频服务器，支持RTMP/WebRTC/HLS/HTTP-FLV/SRT/GB28181。

[![SRS Overview](https://ossrs.net/wiki/images/SRS-SingleNode-4.0-sd.png?v=114)](https://ossrs.net/wiki/images/SRS-SingleNode-4.0-hd.png)

> Note: 简单的单节点架构，适用于大多数场景，大图请看[figma](https://www.figma.com/file/333POxVznQ8Wz1Rxlppn36/SRS-4.0-Server-Arch)。

[![SRS Overview](https://ossrs.net/wiki/images/SRS-Overview-4.0.png)](https://ossrs.net/wiki/images/SRS-Overview-4.0.png)

> Note: 这是典型的源站和边缘集群的架构，适用于需要高并发的场景，高清大图请参考[这里](https://www.processon.com/view/link/5e3f5581e4b0a3daae80ecef)

对于新手来说，音视频的门槛真的非常高，SRS的目标是**降低**（不能**消除**）音视频的门槛，所以请一定要读完Wiki。
不读Wiki一定扑街，不读文档请不要提Issue，不读文档请不要提问题，任何文档中明确说过的疑问都不会解答。

<a name='getting-started'></a>
## Getting Started

SRS支持下面多种方式启动，请使用你最熟悉的方式。

<a name='build-from-source'></a>
## Build From Source

下载源码，推荐用CentOS7系统：

```
git clone -b 4.0release https://gitee.com/ossrs/srs.git
```

编译，注意需要切换到`srs/trunk`目录：

```
cd srs/trunk
./configure
make
```

启动服务器：

```
./objs/srs -c conf/srs.conf
```

检查SRS是否成功启动，可以打开 [http://localhost:8080/](http://localhost:8080/) ，或者执行命令：

```
# 查看SRS的状态
./etc/init.d/srs status

# 或者看SRS的日志
tail -n 30 -f ./objs/srs.log
```

例如，下面的命令显示SRS正在运行：

```
MB0:trunk $ ./etc/init.d/srs status
SRS(pid 90408) is running.                                 [  OK  ]

MB0:trunk $ tail -n 30 -f ./objs/srs.log
[2021-08-13 10:30:36.634][Trace][90408][12c97232] Hybrid cpu=0.00%,0MB, cid=1,1, timer=61,0,0, clock=0,22,25,0,0,0,0,1,0
```

使用 [FFmpeg(点击下载)](https://ffmpeg.org/download.html) 或 [OBS(点击下载)](https://obsproject.com/download) 推流：

```bash
ffmpeg -re -i ./doc/source.flv -c copy -f flv rtmp://localhost/live/livestream
```

或者使用FFmpeg的Docker推流，请将`192.168.1.10`换成你的内网IP：

```bash
docker run --rm registry.cn-hangzhou.aliyuncs.com/ossrs/srs:encoder \
  ffmpeg -re -i ./doc/source.200kbps.768x320.flv -c copy -f flv rtmp://192.168.1.10/live/livestream
```

打开下面的页面播放流（若SRS不在本机，请将localhost更换成服务器IP）:

* RTMP (by [VLC](https://www.videolan.org/)): rtmp://localhost/live/livestream
* H5(HTTP-FLV): [http://localhost:8080/live/livestream.flv](http://localhost:8080/players/srs_player.html?autostart=true&stream=livestream.flv&port=8080&schema=http)
* H5(HLS): [http://localhost:8080/live/livestream.m3u8](http://localhost:8080/players/srs_player.html?autostart=true&stream=livestream.m3u8&port=8080&schema=http)

注意如果RTMP转WebRTC流播放，必须使用配置文件[`rtmp2rtc.conf`](https://github.com/ossrs/srs/issues/2728#rtmp2rtc-cn-guide):

* H5(WebRTC): [webrtc://localhost/live/livestream](http://localhost:8080/players/rtc_player.html?autostart=true)

> Note: 推荐直接运行SRS，可以使用 **[docker](v5_CN_Home#docker)**, 或者 **[K8s](v5_CN_Home#k8s)**

> Note: 若需要开启WebRTC能力，请将CANDIDATE设置为服务器的外网地址，详细请阅读[WebRTC: CANDIDATE](v5_CN_WebRTC#config-candidate)。

> Note: 若需要HTTPS，比如WebRTC和浏览器都要求是HTTPS，那么请参考
> **[HTTPS API](https://github.com/ossrs/srs/wiki/v5_CN_HTTPApi#https-api)**
> 以及 **[HTTPS Callback](https://github.com/ossrs/srs/wiki/v5_CN_HTTPCallback#https-callback)**
> 以及 **[HTTPS Live Streaming](https://github.com/ossrs/srs/wiki/v5_CN_DeliveryHttpStream#https-flv-live-stream)**，
> 当然了HTTPS的反向代理也能和SRS工作很好，比如Nginx代理到SRS。

请继续阅读下面的内容，了解更多SRS的信息。

## Docker

推荐使用Docker直接启动SRS，可用镜像在[这里](https://cr.console.aliyun.com/repository/cn-hangzhou/ossrs/srs/images)和每个[Release](https://github.com/ossrs/srs/releases?q=v4&expanded=true)都会给出来链接:

```bash
docker run --rm -it -p 1935:1935 -p 1985:1985 -p 8080:8080 \
    registry.cn-hangzhou.aliyuncs.com/ossrs/srs:4 ./objs/srs -c conf/docker.conf
```

若需要支持WebRTC，需要设置CANDIATE，并开启UDP/8000端口：

```bash
CANDIDATE="192.168.1.10"
docker run --rm -it -p 1935:1935 -p 1985:1985 -p 8080:8080 \
    --env CANDIDATE=$CANDIDATE -p 8000:8000/udp \
    registry.cn-hangzhou.aliyuncs.com/ossrs/srs:4 ./objs/srs -c conf/docker.conf
```

若需要HTTPS，需要开启端口映射，并使用配置文件`conf/https.*`，比如`conf/https.docker.conf`：

```bash
CANDIDATE="192.168.1.10"
docker run --rm -it -p 1935:1935 -p 1985:1985 -p 8080:8080 -p 1990:1990 -p 8088:8088 \
    --env CANDIDATE=$CANDIDATE -p 8000:8000/udp \
    registry.cn-hangzhou.aliyuncs.com/ossrs/srs:4 ./objs/srs -c conf/https.docker.conf
```

> Note: 请将CANDIDATE设置为服务器的外网地址，详细请阅读[WebRTC: CANDIDATE](v5_CN_WebRTC#config-candidate)。

> Note: 注意如果RTMP转WebRTC流播放，必须使用配置文件[`rtmp2rtc.conf`](https://github.com/ossrs/srs/issues/2728#rtmp2rtc-cn-guide)

> Remark: 请使用你的证书文件，代替上面配置中的key和cert，请参考
> **[HTTPS API](https://github.com/ossrs/srs/wiki/v5_CN_HTTPApi#https-api)**
> 以及 **[HTTPS Callback](https://github.com/ossrs/srs/wiki/v5_CN_HTTPCallback#https-callback)**
> 以及 **[HTTPS Live Streaming](https://github.com/ossrs/srs/wiki/v5_CN_DeliveryHttpStream#https-flv-live-stream)**，
> 当然了HTTPS的反向代理也能和SRS工作很好，比如Nginx代理到SRS。

使用 [FFmpeg(点击下载)](https://ffmpeg.org/download.html) 或 [OBS(点击下载)](https://obsproject.com/download) 推流：

```bash
ffmpeg -re -i ./doc/source.flv -c copy -f flv rtmp://localhost/live/livestream
```

或者使用FFmpeg的Docker推流，请将`192.168.1.10`换成你的内网IP：

```bash
docker run --rm registry.cn-hangzhou.aliyuncs.com/ossrs/srs:encoder \
  ffmpeg -re -i ./doc/source.200kbps.768x320.flv -c copy -f flv rtmp://192.168.1.10/live/livestream
```

打开下面的页面播放流（若SRS不在本机，请将localhost更换成服务器IP）:

* RTMP (by [VLC](https://www.videolan.org/)): rtmp://localhost/live/livestream
* H5(HTTP-FLV): [http://localhost:8080/live/livestream.flv](http://localhost:8080/players/srs_player.html?autostart=true&stream=livestream.flv&port=8080&schema=http)
* H5(HLS): [http://localhost:8080/live/livestream.m3u8](http://localhost:8080/players/srs_player.html?autostart=true&stream=livestream.m3u8&port=8080&schema=http)

请继续阅读下面的内容，了解更多SRS的信息。

## Cloud Virtual Machine

SRS可以在云虚拟机上工作得很好，下面是一些可用的云厂商，以及使用方式：

* [TencentCloud LightHouse](https://www.bilibili.com/video/BV1844y1L7dL/)：不仅仅是SRS，这是个微缩视频云，参考[#2856](https://github.com/ossrs/srs/issues/2856#lighthouse)。
* [TencentCloud CVM](https://mp.weixin.qq.com/s/x-PjoKjJj6HRF-eCKX0KzQ)：不仅仅是SRS，这是个微缩视频云，参考[#2856](https://github.com/ossrs/srs/issues/2856#lighthouse)。
* [DigitalOcean Droplet](https://mp.weixin.qq.com/s/_GcJm15BGv1qbmHixPQAGQ)：海外用户，直接创建SRS Droplet。
* [CentOS 7安装包](https://github.com/ossrs/srs/releases)：在所有云厂商的虚拟机上，手动安装SRS，使用[systemctl](v5_CN_LinuxService#systemctl)管理服务。

## K8s

推荐使用K8s部署SRS，参考[Deploy to Cloud Platforms](#v4_CN_K8s#deploy-to-cloud-platforms)，视频教程[Bilibili: SRS-027-用K8s零命令行部署SRS](https://www.bilibili.com/video/BV1g44y1j7Vz/)

SRS提供了一系列的模版项目，可以快速部署到云平台K8s：

* [TKE(腾讯云K8s)](https://github.com/ossrs/srs-tke-template)
* [通用K8s](https://github.com/ossrs/srs-k8s-template)
* [ACK(阿里云K8s)](https://github.com/ossrs/srs-ack-template)
* [EKS(亚马逊AWS K8s)](https://github.com/ossrs/srs-eks-template)
* [AKS(微软Azure K8s)](https://github.com/ossrs/srs-aks-template)

请继续阅读下面的内容，了解更多SRS的信息。

## Effective SRS

SRS是一个服务器，也可以扩展成集群，还涉及多种协议和场景。下图是SRS的概览大地图，先有个大概印象：

[![](https://ossrs.net/wiki/images/srs-arch4-1.png)](https://ossrs.net/wiki/images/srs-arch4-1.png)

> Note: 高清图请看 https://www.processon.com/view/link/619f25c37d9c083e98adb37e

> Note: 别被这张图吓到，一般也用不到所有的能力，关键是要花时间看文档，了解这些部分怎么工作的。

我们从几个典型的应用场景来说下上面的大图，更多场景请看[Applications](v5_CN_Sample)：

* 全平台直播，小荷才露尖尖角。只需要上图的Encoders(FFmpeg/OBS)[推送RTMP到SRS](https://gitee.com/ossrs/srs/wikis/v5_CN_SampleRTMP)；一台SRS Origin(不需要Cluster)，[转封装成HTTP-FLV流](https://gitee.com/ossrs/srs/wikis/v5_CN_SampleHttpFlv)、[转封装成HLS](https://gitee.com/ossrs/srs/wikis/v5_CN_SampleHLS)；Players根据平台的播放器可以选HTTP-FLV或HLS流播放。
* WebRTC通话业务，[一对一通话](https://mp.weixin.qq.com/s/xWe6f9WRhtwnpJQ8SO0Eeg)，[多人通话](https://mp.weixin.qq.com/s/CM2h99A1e_masL5sjkp4Zw)，会议室等。[WebRTC](https://gitee.com/ossrs/srs/wikis/v5_CN_WebRTC)是SRS4引入的关键和核心的能力，从1到3秒延迟，到100到300毫秒延迟，绝对不是数字的变化，而是本质的变化。
* 监控和广电上云，各行业风起云涌。除了使用FFmpeg主动[拉取流到SRS](https://gitee.com/ossrs/srs/wikis/v5_CN_Ingest)，还可以广电行业[SRT协议](https://gitee.com/ossrs/srs/wikis/v5_CN_SRTWiki)推流，或监控行业[GB28181协议](https://github.com/ossrs/srs/issues/1500#issue-528623588)推流，SRS转换成互联网的协议观看。
* 直播低延迟和互动，聚变近在咫尺。[RTMP转WebRTC播放](https://github.com/ossrs/srs/issues/307#issue-76908382)降低播放延迟，还能做[直播连麦](https://mp.weixin.qq.com/s/7xexl07rrWBdh8xennXK3w)，或者使用WebRTC推流，未来还会支持WebTransport直播等等。
* 大规模业务，带你装逼带你飞。如果业务快速上涨，可以通过[Edge Cluster](https://gitee.com/ossrs/srs/wikis/v5_CN_SampleRTMPCluster)支持海量Players，或者[Origin Cluster](https://gitee.com/ossrs/srs/wikis/v5_CN_OriginCluster)支持海量Encoders，当然可以直接平滑迁移到视频云。未来还会支持RTC的级联和集群。

> Note: 这些场景的K8S部署，请参考[Edge Cluster](https://github.com/ossrs/srs/wiki/v4_CN_K8s#srs-edge-cluster-for-high-concurrency-streaming)和[Origin Cluster](https://github.com/ossrs/srs/wiki/v4_CN_K8s#srs-origin-cluster-for-a-large-number-of-streams)。

每个场景可能会用到一些通用的能力，比如：

* 一般都需要[录制成FLV/MP4](v5_CN_DVR)，[将RTMP流转码](v5_CN_SampleFFMPEG)，[流截图](v5_CN_Snapshot)。
* 也需要和现有业务系统集成，比如[HTTP回调](v5_CN_HTTPCallback)，或者通过[HTTP API接口](v5_CN_HTTPApi)查询流和客户端的信息。
* 使用FFmpeg主动[拉取流到SRS](v5_CN_Ingest)，或者[Forward](v5_CN_SampleForward)处理流后转给其他服务，或者[推送RTSP/UDP/FLV到SRS](v5_CN_Streamer)。
* 安全方面，使用[安全策略Security](v5_CN_Security)设置访问，或者用[HTTP API接口](v5_CN_HTTPApi)踢流。
* 使用[VHOST虚拟服务器 ](v5_CN_RtmpUrlVhost)隔离不同的业务，用域名作为调度单元，应用不同的配置。

如果你更喜欢看视频，可以移步看下面的视频专栏介绍，最后还是要墙裂劝说看一遍Wiki：

* [SRS答疑FAQ，精彩剪辑，大家有的疑问，你也可能有，推荐观看](https://space.bilibili.com/430256302/channel/collectiondetail?sid=239740)
* [SRS云服务器，无门槛入门，推荐大家先使用SRS云服务器，先熟悉和跑通场景](https://space.bilibili.com/430256302/channel/collectiondetail?sid=180263&ctype=0)
* [如何使用OBS做直播，OBS的使用分享，一些有用的插件，推荐用OBS推流](https://space.bilibili.com/430256302/channel/collectiondetail?sid=44145&ctype=0)
* [SRS使用和定制开发，核心能力分析，如何定制，推荐有一定基础的朋友观看](https://space.bilibili.com/430256302/channel/collectiondetail?sid=44177&ctype=0)
* [零声学院(视频)：SRS4.0入门系列](https://ke.qq.com/course/3202131)

> 再啰嗦一遍：不读Wiki一定扑街，不读文档请不要提Issue，不读文档请不要提问题，任何文档中明确说过的疑问都不会解答。

看完上面的文档，对SRS能做的事情有了大概的了解，可以阅读下面的文档，深入了解SRS。

<a name="deployment-guides"></a>

### Deployment Guides

* [Delivery RTMP](v5_CN_SampleRTMP): 如何部署SRS提供RTMP服务。
* [Delivery HLS](v5_CN_SampleHLS): 如何部署SRS提供RTMP和HLS服务。
* [Delivery HTTP FLV](v5_CN_SampleHttpFlv): 如何部署SRS分发FLV流。
* [Delivery HDS](v5_CN_DeliveryHDS): 如何部署SRS分发HDS流。
* [Delivery DASH](v5_CN_SampleDASH): 如何部署SRS分发DASH流。
* [Transmux SRT](v5_CN_SampleSRT): 如何部署SRS支持SRT流。
* [Transmux GB28181](https://github.com/ossrs/srs/issues/1500#issue-528623588)：如何部署SRS支持GB28181流。
* [Transcode](v5_CN_SampleFFMPEG): 如何部署SRS对直播流转码。
* [Snapshot](v5_CN_Snapshot): 如何对直播流截图。
* [Forward](v5_CN_SampleForward): 如何部署SRS转发RTMP流到其他服务器。
* [Low latency](v5_CN_SampleRealtime): 如何部署SRS为低延迟模式。
* [Ingest](v5_CN_SampleIngest): 如何将其他流拉到SRS作为RTMP流。
* [HTTP Server](v5_CN_SampleHTTP): 如何部署SRS为HTTP服务器。
* [SRS DEMO](v5_CN_SampleDemo): 如何启动SRS的DEMO。
* [Projects](v5_CN_Sample): 都有谁在使用SRS。
* [Setup](v5_CN_Setup): SRS安装和部署摘要。
* [WebRTC Play](https://github.com/ossrs/srs/issues/307#issue-76908382): SRS支持WebRTC播放流。
* [GB28181 Publish](https://github.com/ossrs/srs/issues/1500#issue-528623588): SRS支持GB28181推流。
* [SRT Publish](https://github.com/ossrs/srs/issues/1147#issuecomment-577469119): SRS支持SRT推流。
* [HEVC/H.265](https://github.com/ossrs/srs/pull/1721#issuecomment-619460847): SRS支持H.265编码格式。

<a name="cluster-guides"></a>

### Cluster Guides

* [Origin Cluster](v5_CN_OriginCluster): 如何支持源站集群，扩展源站能力。
* [Edge Cluster: RTMP](v5_CN_SampleRTMPCluster): 如何部署RTMP分发集群，譬如CDN支持RTMP分发。
* [Edge Cluster: FLV](v5_CN_SampleHttpFlvCluster): 如何部署HTTP-FLV分发集群，譬如CDN支持HTTP-FLV分发。
* [Edge Cluster: HLS](v5_CN_SampleHlsCluster): 如何部署HLS分发集群，比如CDN支持HLS分发。
* [VHOST](v5_CN_RtmpUrlVhost): 如何一个集群支持多个用户，即Vhost。
* [Reload](v5_CN_Reload): 如何不中断服务的前提下应用新的配置，即Reload。
* [Tracable Log](v5_CN_SrsLog): 如何在集群中追溯错误和日志，基于连接的日志，排错日志。
* [Log Rotate](v5_CN_LogRotate): 如何切割服务器的日志，然后压缩或者清理。
* [K8s](v4_CN_K8s): 如何使用[ACK(阿里云容器服务Kubernetes版)](https://www.aliyun.com/product/kubernetes)部署SRS集群。

<a name="integration-guides"></a>

### Integration Guides

* [Linux Service](v5_CN_LinuxService): 启动或停止服务。
* [HTTP Callback](v5_CN_HTTPCallback): 使用HTTP回调侦听SRS的事件。
* [HTTP API](v5_CN_HTTPApi): 使用SRS的HTTP API获取数据。
* [Special Control](v5_CN_SpecialControl): 一些特殊的控制配置。

<a name="video-guides"></a>
<a name="solution-guides"></a>

### Solution Guides

* [陈海博：SRS在安防中的应用](https://www.bilibili.com/video/BV11S4y197Zx)
* 最佳实践：[一对一通话](https://mp.weixin.qq.com/s/xWe6f9WRhtwnpJQ8SO0Eeg)，[多人通话](https://mp.weixin.qq.com/s/CM2h99A1e_masL5sjkp4Zw)和[直播连麦](https://mp.weixin.qq.com/s/7xexl07rrWBdh8xennXK3w)
* [最佳实践：如何扩展你的SRS并发能力？](https://mp.weixin.qq.com/s/pd9YQS0WR3hSuHybkm1F7Q)
* SRS是单进程模型，不支持多进程；可以使用[集群](https://mp.weixin.qq.com/s/pd9YQS0WR3hSuHybkm1F7Q)
  或者[ReusePort](v5_CN_REUSEPORT)扩展多进程(多核)能力。
* [基于HLS-TS&RTMP-FLV的微信小程序点直播方案](https://mp.weixin.qq.com/s/xhScUrkoroM7Q7ziODHyMA)
* [借力SRS落地实际业务的几个关键事项](https://mp.weixin.qq.com/s/b19kBer_phZl4n4oUBOvxQ)
* [干货 | 基于SRS直播平台的监控系统之实现思路与过程](https://mp.weixin.qq.com/s/QDTtW85giKmryhvCBkyyCg)
* [Android直播实现](https://blog.csdn.net/dxpqxb/article/details/83012950)
* [SRS直播服务器与APP用户服务器的交互](https://www.jianshu.com/p/f3dfa727475a)
* [使用flvjs实现摄像头flv流低延时实时直播](https://www.jianshu.com/p/2647393f956a)
* [IOS 直播方面探索（服务器搭建，推流，拉流）](https://www.jianshu.com/p/1aa677d99d17)
* [国产开源流媒体SRS4.0对视频监控GB28181的支持](https://mp.weixin.qq.com/s/VIPSPaBB5suUk7_I2oOkMw)

<a name="client-sdk-guide"></a>
<a name="develop-guide"></a>

### Develop Guide

* [高性能网络服务器设计](https://blog.csdn.net/win_lin/article/details/8242653)，分析高性能网络服务器的设计要点。
* [SRS高精度、低误差定时器](https://mp.weixin.qq.com/s/DDSzRKHyJ-uYQ9QQC9VOZg)，论高并发服务器的定时器问题。
* [协程原理：函数调用过程、参数和寄存器](https://mp.weixin.qq.com/s/2TsYSiV8ysyLrELHdlHtjg)，剖析SRS协程实现的最底层原理。
* [性能优化：SRS为何能做到同类的三倍](https://mp.weixin.qq.com/s/r2jn1GAcHe08IeTW32OyuQ)，论性能优化的七七八八、前前后后。
* [SRS代码分析](https://github.com/xialixin/srs_code_note/blob/master/doc/srs_note.md)，分析SRS结构和代码逻辑，类结构图，线程模型，模块架构。
* [Third-party Client SDK](v5_CN_ClientSDK): 第三方厂商提供的客户端推流和播放的SDK，一般是移动端包括Andoird和iOS。
* [轻量线程分析](https://github.com/ossrs/state-threads#analysis)，分析SRS依赖的库ST的关键技术。
* [SRS代码分析](https://github.com/xialixin/srs_code_note/blob/master/doc/srs_note.md)，分析SRS结构和代码逻辑，类结构图，线程模型，模块架构。
* [深度: 掀起你的汇编来：如何移植ST协程到其他系统或CPU？](https://mp.weixin.qq.com/s/dARz99INVlGuoFW6K7SXaw)
* [肖志宏：SRS支持WebRTC级联和QUIC协议](https://www.bilibili.com/video/BV1Db4y1b77J)
* [StateThreads源码分析](https://www.xianwaizhiyin.net/?cat=24)
* [SRS 4.0源码分析](https://www.xianwaizhiyin.net/?cat=21)

<a name="migrate-from-nginx-rtmp"></a>

### Migrate From NGINX-RTMP

* [NG EXEC](v5_CN_NgExec): 为特殊的事件执行外部程序，譬如exec_publish，当发布流时exec外部程序。

<a name="user-guides"></a>

### Product & Milestones

* [Milestones](v5_CN_Product): SRS的路线图和产品计划。
* [Why SRS](v5_CN_Product): 为何选择SRS？SRS的路线图？
* [GIT Mirrors][mirrors]: SRS在各个主要GIT站点的镜像，代码都是保持同步的。
* [Main Features][features]: SRS的功能列表。请注意有些功能只有特定的版本才有。请注意有些功能是实验性的。
* [Releases][releases]: SRS目前已经发布的版本。
* [Docs](v5_CN_Docs): SRS的详细文档。
* [Compare][compare]: SRS和其他服务器的对比。
* [Performance][performance]: SRS的性能测试报告。

## Tech Docs

* [历经5代跨越25年的RTC架构演化史](https://mp.weixin.qq.com/s/fO-FcKU_9Exdqh4xb_U5Xw)
* [技术解码 | SRT和RIST协议综述](https://mp.weixin.qq.com/s/jjtD4ik-9noMyWbecogXHg)
* [公众号专栏：SRS，知识库，重要功能和阶段性结果，解决方案和DEMO](https://mp.weixin.qq.com/mp/appmsgalbum?action=getalbum&__biz=MzA4NTQ3MzQ5OA==&scene=1&album_id=1703565147509669891&count=10#wechat_redirect)
* [公众号专栏：深度，底层技术分析，服务器模型，协议处理，性能优化等](https://mp.weixin.qq.com/mp/appmsgalbum?__biz=MzA4NTQ3MzQ5OA==&action=getalbum&album_id=2156820160114900994#wechat_redirect)
* [公众号专栏：动态，关于最新的会议和动态，新闻，社区等](https://mp.weixin.qq.com/mp/appmsgalbum?__biz=MzA4NTQ3MzQ5OA==&action=getalbum&album_id=1683217451712299009&count=10#wechat_redirect)
* [WebRTC 的现状和未来：专访 W3C WebRTC Chair Bernard Aboba](https://mp.weixin.qq.com/s/0HzzWSb5irvpNKNnSJL6Bg)
* [B站专栏(视频)：SRS开源服务器](https://space.bilibili.com/430256302/channel/detail?cid=136049)
* [零声学院(视频)：SRS流媒体服务器实战](https://www.bilibili.com/video/BV1XZ4y1P7um)
* [音视频开发为什么要学SRS流媒体服务器](https://zhuanlan.zhihu.com/p/190182314)

<a name="join-us"></a>

### Join Us

* [如何向SRS提交ISSUE？](v5_CN_HowToAskQuestion)
* [File Issue][issue]: 提交需求、Bug和反馈。
* [Contact](v5_CN_Contact): 用钉钉、微信、邮箱联系我们。

### Questions or need help?

其他联系方式，参考[联系我们](v5_CN_Contact)

Winlin 2020.01

[st]: https://github.com/ossrs/state-threads
[website]: http://ossrs.net

[qstart]: https://github.com/ossrs/srs/tree/4.0release#usage
[mirrors]: https://github.com/ossrs/srs/tree/4.0release#mirrors
[features]: https://github.com/ossrs/srs/tree/4.0release#features
[releases]: https://github.com/ossrs/srs/tree/4.0release#releases
[issue]: https://github.com/ossrs/srs/issues/new

[compare]: https://github.com/ossrs/srs/tree/4.0release#compare
[performance]: https://github.com/ossrs/srs/tree/4.0release#performance
