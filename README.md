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

### 微服务（Docker容器）组成：

- **goim** ：不多说 B站 IM架构
- **livego** ：基于golang开发的高性能rtmp服务器 实测机型：阿里云32核64G独享服务器 30000路并发拉流，cpu占用率不到50%！
- **webrtc** ：Janus Gateway：Meetecho优秀的通用WebRTC服务器（SFU）；
- **MongoDB** ：云时代构建的基于文档的分布式数据库；
- **Redis**：内存中的数据结构存储，用作数据库，缓存和消息代理；
- **kafka** ：队列 群聊，私聊，消息通知等。
- **Coturn** ：TURN和STUN Server的开源项目；
- **Nginx** ：高性能负载平衡器，Web服务器和有HTTP3 / Quiche和Brtoli支持的反向代理；
- **Docker**：用于构建、部署和管理容器化应用程序的平台。
- **后台管理界面**: php（老业务php后台）+ gin（API接口重构）+ vue + Element-UI 
----------------


**技术群：**
![](https://img-blog.csdnimg.cn/20200623093238797.png)


----------------
微信：BCFind5 【请备注好信息】

博客地址：https://blog.csdn.net/u012115197/article/details/106916635

Gitee：https://gitee.com/baoyalive/baoyalive.git


----------------

### 商业合作 （UI设计，定制开发，系统重构，代理推广等）

| 类目        | 价格   |  商业授权  |
| --------   | -----:  | :----:  |
| 基础开源版本（php+go的基础版本仅供学习使用）      | ￥0        |   无（不可商用）     |
| php版本       |   ￥15000  |   有（可以商用）  |
| golang海外商用版本		|    ￥30000起|  有（可以商用）|

----------------

[上线APP搜：]() 【欢心交友】 

**演示：**
![](https://qr.api.cli.im/newqr/create?data=https%3A%2F%2Fwwa.lanzoui.com%2FilOakvjblda&level=H&transparent=false&bgcolor=%23FFFFFF&forecolor=%23000000&blockpixel=12&marginblock=1&logourl=&logoshape=no&size=84&bgimg=&text=&fontsize=30&fontcolor=%23000000&fontfamily=simsun.ttc&incolor=&outcolor=&qrcode_eyes=null&background=&wper=&hper=&tper=&lper=&eye_use_fore=1&qrpad=10&kid=cliim&key=165a2a9dd311adb79839a6d0139f85d8)

[直播短视频带货app下载](https://wwa.lanzoui.com/ilOakvjblda) 点击此处下载app（用手机浏览器打开下载，不要用微信直接下载）
  
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

## 后端

**系统开发语言**
-  **PHP|golang 视频互动系统由 WEB 系统、REDIS 服务、MYSQL 服务、视频服务、聊天服务、后台管理系统和定时监控组成，后台管理采用PHP + golang 语言开发，所有服务提供横向扩展。**

1. WEB 系统提供页面、接口逻辑。
2. REDIS 服务提供数据的缓存、存储动态数据。
3. MYSQL 服务提供静态数据的存储。
4. 视频服务提供视频直播，傍路直播，转码、存储、点播等 支持腾讯云 阿里云 七牛等 自建流媒体服务器等（包括两套成熟方案 nginx_rtmp 和 golang的）。
5. golang +kafka 队列 聊天服务提供直播群聊，私聊，消息通知等。
6. consul + grpc 系统监控：监听主播异常掉线情况、直播消息推送等。
 
------------
## 分布式架构

	注：本教程适用于 Go 1.13 版本，因为 Go 1.11 才正式引入了 Go Module 作为包管理器。

**针对市面上现有的直播系统多为单机裸奔版本，系统臃肿，业务耦合 IM API等不可横向扩展，所以采用分布式尤为必要，采用到技术如下：**

后台管理：**laravel** 

分布式：**go mirco + micro api + etcd + kafka**等 

前端：**vue**

移动端： **ios+android+小程序**等

数据库：**mysql+redis**

通讯框架：**gprc**

长连接通讯协议：**protocol buffers**


### 项目代码
>项目目录：
![在这里插入图片描述](https://img-blog.csdnimg.cn/20200923112617559.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3UwMTIxMTUxOTc=,size_16,color_FFFFFF,t_70#pic_center)

> server:       服务启动入口 
>
> config:       服务配置
>
> app:     每个服务私有代码 
>
> comm:   服务共有代码 
>
> sql:          项目sql文件
>
> test:         长连接测试脚本


**app服务介绍**

![在这里插入图片描述](https://img-blog.csdnimg.cn/20200923113249111.png#pic_center)

```bash
1.tcp_conn
维持与客户端的TCP长连接，心跳，以及TCP拆包粘包，消息编解码
2.ws_conn
维持与客户端的WebSocket长连接，心跳，消息编解码
3.logic
设备信息，好友信息，群组信息管理，消息转发逻辑
4.user
可以根据自己的业务需求，进行扩展,也可以替换成自己的业务服务器
```
**客户端接入流程**
```bash
1.调用LogicExt.RegisterDevice接口，完成设备注册，获取设备ID（device_id）,注意，一个设备只需完成一次注册即可，后续如果本地有device_id,就不需要注册了，举个例子，如果是APP第一次安装，就需要调用这个接口，后面即便是换账号登录，也不需要重新注册。
2.调用UserExt.SignIn接口，完成账户登录，获取账户登录的token。
3.建立长连接，使用步骤2拿到的token，完成长连接登录。
如果是web端,需要调用建立WebSocket时,将user_id,device_id,token，以URL参数的形式传递到服务器，完成长连接登录，例如：ws://localhost:8081/ws?user_id={user_id}&device_id={device_id}&token={token}
如果是APP端，就需要建立TCP长连接，在完成建立TCP长连接时，第一个包应该是长连接登录包（SignInInput），如果信息无误，客户端就会成功建立长连接。
4.使用长连接发送消息同步包（SyncInput），完成离线消息同步，注意：seq字段是客户端接收到消息的最大同步序列号，如果用户是换设备登录或者第一次登录，seq应该传0。
接下来，用户可以使用LogicExt.SendMessage接口来发送消息，消息接收方可以使用长连接接收到对应的消息。

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

