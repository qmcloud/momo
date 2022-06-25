### 直播源码,短视频,直播带货,游戏陪玩,仿比心,猎游,tt语音聊天,美女约玩,陪玩系统源码开黑,约玩源码

----------------
[English](./README-en.md) | 简体中文

<div align=center>
<img src="https://img.shields.io/badge/php-7.3-blue"/>
<img src="https://img.shields.io/badge/golang-1.13-blue"/>
<img src="https://img.shields.io/badge/gin-1.4.0-lightBlue"/>
<img src="https://img.shields.io/badge/vue-2.6.10-brightgreen"/>
<img src="https://img.shields.io/badge/element--ui-2.12.0-green"/>
<img src="https://img.shields.io/badge/gorm-1.9.12-red"/>
</div>


### 前端: VUE 移动端: Android + ios

### 微服务（K8s,Docker容器）组成：

- **goim** ：不多说 B站 IM架构
- **SRS** ：SRS是一个高效的实时视频服务器，支持RTMP/WebRTC/HLS/HTTP-FLV/SRT/GB28181。
- **webrtc** ：Janus Gateway：Meetecho优秀的通用WebRTC服务器（SFU）；
- **MongoDB** ：云时代构建的基于文档的分布式数据库；
- **Redis**：内存中的数据结构存储，用作数据库，缓存和消息代理；
- **kafka** ：队列 群聊，私聊，消息通知等。
- **Coturn** ：TURN和STUN Server的开源项目；
- **Nginx** ：高性能负载平衡器，Web服务器和有HTTPS / Quiche和Brtoli支持的反向代理；
- **K8s+docker**：用于构建、部署和管理容器化应用程序的平台。
- **后台管理界面**: php版 | golang版 + vue + Element-UI 
----------------

**技术群：**
![](https://img-blog.csdnimg.cn/20200623093238797.png)

----------------
微信：BCFind5 【请备注好信息】

文档地址：https://www.52webrtc.top

博客地址：https://blog.csdn.net/u012115197/article/details/106916635

Gitee：https://gitee.com/baoyalive/baoyalive.git

----------------

### 商业合作 （UI设计，定制开发，系统重构，代理推广等）

----------------

[上架APP请搜：]() 【欢心交友】

SRS+Goim演示地址：http://live.52webrtc.top
 
APP下载地址：http://voice.52webrtc.top

点击此处下载app（用手机浏览器打开下载，不要用微信直接下载）
  
IOS 视频演示：https://pan.baidu.com/s/18KaHu-39TMQLetb0m7XD0Q 提取码：v929


----------------

**前端展示**
![前端展示](https://img-blog.csdnimg.cn/20210605203510511.jpg?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3UwMTIxMTUxOTc=,size_16,color_FFFFFF,t_70#pic_center)

**后台界面**
![vue界面](https://img-blog.csdnimg.cn/6a993757bb6e43698358ea12f838e8ad.png?x-oss-process=image/watermark,type_d3F5LXplbmhlaQ,shadow_50,text_Q1NETiBA5Y2I5aSc56CB54uC,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)

![k8s界面](https://img-blog.csdnimg.cn/ae959e8fb8994ef2a3b92356c3276890.png?x-oss-process=image/watermark,type_d3F5LXplbmhlaQ,shadow_50,text_Q1NETiBA5Y2I5aSc56CB54uC,size_20,color_FFFFFF,t_70,g_se,x_16#pic_center)

----------------

### 入门推荐书籍

* [FFmpeg从入门到精通](https://book.douban.com/subject/30178432/) - 强烈推荐
* [直播系统开发：基于Nginx与Nginx-rtmp-module](https://book.douban.com/subject/30423374/)
* [WebRTC权威指南](https://book.douban.com/subject/26915289/)
* [CDN技术详解](https://book.douban.com/subject/10759173/)

### 技术结构
## 前端 
- **集小视频/IM聊天/直播等功能于一体的直播项目。界面仿制抖音|火山小视频|陌陌直播|比心陪玩等。**

#### *注意：*前端的具体细技术细节请移至前端篇赘述，本贴暂不描述前端内容。


## PHP框架开发

-  **PHP版本视频互动系统由 WEB 系统、REDIS 服务、MYSQL 服务、视频服务、聊天服务、后台管理系统和定时监控组成，后台管理及API采用PHP语言开发**

1. WEB 系统提供页面、接口逻辑。
2. REDIS 服务提供数据的缓存、存储动态数据。
3. MYSQL 服务提供静态数据的存储。
4. 视频服务提供视频直播，傍路直播，转码、存储、点播等 支持腾讯云 阿里云 七牛等 自建流媒体服务器等（包括两套成熟方案 nginx_rtmp SRS + WebRtc）。
5. kafka 队列 聊天服务提供直播群聊，私聊，消息通知等。
6. prometheus系统监控：监听主播异常掉线情况、直播消息推送等。
 
------------
## golang微服务架构

**微服务介绍**

1. 轻松获得支撑百万日活服务的稳定性
2. 内建级联超时控制、限流、自适应熔断、自适应降载等微服务治理能力，无需配置和额外代码
3. 微服务治理中间件可无缝集成到其它现有框架使用
4. 极简的 API 描述，一键生成各端代码
5. 自动校验客户端请求参数合法性
6. 大量微服务治理和并发工具包

**架构图**

![](https://github.com/DOUBLE-Baller/momo/blob/master/doc/doc.jpg?raw=true)

**代码目录说明**

```
├── ergo
│   ├── app  // app代码
│   ├── backend // 后台接口，rpc
│   ├── backendweb // 后台vue页面代码
│   ├── script // 数据库脚本，简化的kubernetes部署脚本
│   ├── .gitignore // git控制忽略文件
│   ├── LICENSE // LICENSE文件，使用的是MIT LICENSE
```
**网关**
```
nginx做网关，使用nginx的auth模块，调用后端的backend服务统一鉴权，业务内部不鉴权，如果涉及到业务资金比较多也可以在业务中进行二次鉴权。
另外，很多同学觉得nginx做网关不太好，这块原理基本一样，可以自行替换成apisix、kong等
```
**开发模式**
```
本项目使用的是微服务开发，api （http） + rpc（grpc） ， api充当聚合服务，复杂、涉及到其他业务调用的统一写在rpc中，如果一些不会被其他服务依赖使用的简单业务，可以直接写在api的logic中
```
**日志**
```
关于日志，统一使用filebeat收集，上报到kafka中，logstash把kafka数据源同步到elasticsearch中，再通过kibana进行分析处理展示等。
```
**监控**
```
监控采用prometheus，只需要配置就可以了，这里可以看项目中的配置
```
**链路追踪**
```
默认jaeger、zipkin支持，只需要配置就可以了，可以看配置
```
**消息队列**
```
这里使用可kq，kq是基于kafka做的高性能消息队列
```
**延迟队列、定时任务**
```
延迟队列、定时任务本项目使用的是asynq ， google团队给予redis开发的简单中间件，
当然了asynq也支持消息队列，你也可也把kq消息队列替换成这个，毕竟只需要redis不需要在去维护一个kafka也是不错的
链接：https://github.com/hibiken/asynq
```
**分布式事务**
```
分布式事务准备使用的是dtm，本项目目前还未使用到，后续准备直接集成就好了，如果读者使用直接去看那个源码就行了
```
**部署**
```
部署的话，目前这个直接使用docker可以部署整套技术栈，如果上k8s的话 ，通过goctl生成k8s的yaml文件也非常简单。

我说下思路，搭建一个gitlab、jenkins、harbor

1、将代码放在gitlab

2、在gitlab创建流水线，基本是一个服务一个流水线了

3、流水线步骤 ：

拉取代码--->ci检测（这里可以省略哈，自己看着办）--->构建镜像（Dockerfile可以通过goctl自动生成）-->推送到harbor镜像服务--->使用kubectl去k8s拉取镜像（ack、ask都行，ask无法使用daemonset 不能用filebeat）---->ok
```

## 视频服务
------------

README ： https://github.com/DOUBLE-Baller/momo/tree/master/livego


## goim聊天服务
------------

README ：https://github.com/DOUBLE-Baller/momo/tree/master/IM

==问题反馈==

**在使用中有任何问题，欢迎反馈给我们**

https://github.com/DOUBLE-Baller/momo/issues

