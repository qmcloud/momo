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
- **livego** ：基于golang开发的高性能rtmp服务器 实测机型：阿里云32核64G独享服务器 30000路并发拉流，cpu占用率不到50%！
- **webrtc** ：Janus Gateway：Meetecho优秀的通用WebRTC服务器（SFU）；
- **MongoDB** ：云时代构建的基于文档的分布式数据库；
- **Redis**：内存中的数据结构存储，用作数据库，缓存和消息代理；
- **kafka** ：队列 群聊，私聊，消息通知等。
- **Coturn** ：TURN和STUN Server的开源项目；
- **Nginx** ：高性能负载平衡器，Web服务器和有HTTP3 / Quiche和Brtoli支持的反向代理；
- **Docker**：用于构建、部署和管理容器化应用程序的平台。
- **后台管理界面**: php版 | golang版 + vue + Element-UI 
----------------


**技术群：**
![](https://img-blog.csdnimg.cn/20200623093238797.png)


----------------
微信：BCFind5 【请备注好信息】

博客地址：https://blog.csdn.net/u012115197/article/details/106916635

Gitee：https://gitee.com/baoyalive/baoyalive.git


----------------

### 商业合作 （UI设计，定制开发，系统重构，代理推广等）

----------------

[上线APP搜：]() 【欢心交友】 

**演示：**
![](https://qr.api.cli.im/newqr/create?data=https%3A%2F%2Fwwa.lanzoui.com%2FisHcIvmbeng&level=H&transparent=false&bgcolor=%23FFFFFF&forecolor=%23000000&blockpixel=12&marginblock=1&logourl=&logoshape=no&size=96&bgimg=&text=&fontsize=30&fontcolor=%23000000&fontfamily=simsun.ttc&incolor=&outcolor=&qrcode_eyes=null&background=&wper=&hper=&tper=&lper=&eye_use_fore=1&qrpad=10&kid=cliim&key=1accf12ddac20710ce6c829b73fb8aeb)

[直播短视频带货app下载](https://wwa.lanzoui.com/isHcIvmbeng) 点击此处下载app（用手机浏览器打开下载，不要用微信直接下载）
  
----------------
IOS 视频演示：https://pan.baidu.com/s/18KaHu-39TMQLetb0m7XD0Q 提取码：v929

----------------

文档地址：http://live1219.dasddf.club/appapi/listAllApis.php?type=expand

----------------

**前端展示**
![前端展示](https://img-blog.csdnimg.cn/20210605203510511.jpg?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3UwMTIxMTUxOTc=,size_16,color_FFFFFF,t_70#pic_center)

**后台界面**
![后台界面](https://img-blog.csdnimg.cn/20200907180807339.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3UwMTIxMTUxOTc=,size_16,color_FFFFFF,t_70#pic_center)


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
![陪玩app](https://img-blog.csdnimg.cn/20210406163759968.jpg?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3UwMTIxMTUxOTc=,size_16,color_FFFFFF,t_70#pic_center)

## 后端语言

**系统开发语言**
-  **PHP|golang 视频互动系统由 WEB 系统、REDIS 服务、MYSQL 服务、视频服务、聊天服务、后台管理系统和定时监控组成，后台管理采用PHP|golang 语言开发，所有服务提供横向扩展。**

1. WEB 系统提供页面、接口逻辑。
2. REDIS 服务提供数据的缓存、存储动态数据。
3. MYSQL 服务提供静态数据的存储。
4. 视频服务提供视频直播，傍路直播，转码、存储、点播等 支持腾讯云 阿里云 七牛等 自建流媒体服务器等（包括两套成熟方案 nginx_rtmp SRS Livego 和 golang的）。
5. golang +kafka 队列 聊天服务提供直播群聊，私聊，消息通知等。
6. etcd + grpc 系统监控：监听主播异常掉线情况、直播消息推送等。
 
------------
## golang微服务架构

**微服务介绍**

1. 轻松获得支撑千万日活服务的稳定性
2. 内建级联超时控制、限流、自适应熔断、自适应降载等微服务治理能力，无需配置和额外代码
3. 微服务治理中间件可无缝集成到其它现有框架使用
4. 极简的 API 描述，一键生成各端代码
5. 自动校验客户端请求参数合法性
6. 大量微服务治理和并发工具包

**架构图**

![ ](https://gitee.com/baoyalive/baoyalive/blob/master/doc/doc.jpg)

**代码目录说明**
├── code-dir
│   ├── app  // app代码
│   ├── backend // 后台接口，rpc
│   ├── backendweb // 后台vue页面代码
│   ├── script // 数据库脚本，简化的kubernetes部署脚本
│   ├── .gitignore // git控制忽略文件
│   ├── LICENSE // LICENSE文件，使用的是MIT LICENSE


## 视频服务
------------

README ： https://github.com/DOUBLE-Baller/momo/tree/master/livego


## goim聊天服务
------------

README ：https://github.com/DOUBLE-Baller/momo/tree/master/IM

==问题反馈==

**在使用中有任何问题，欢迎反馈给我们**

https://github.com/DOUBLE-Baller/momo/issues

