



# 背景
:::info
本文内容涉及到了WebRTC涉及的协议讲解、相关服务器的搭建、WebRTC核心API学习，最后包含一个WenRTC音视频通话的小实例开发教程实践（含完整代码）。测试过成都市内、成都↔武汉、成都↔北京、成都↔沈阳，基本都成功了。[了解更多...](https://github.com/DOUBLE-Baller/WebRTC_IM)
:::


# ​一、协议
## 1.1 P2P通信原理与实现
### 1.1.1 基本术语
**防火墙（Firewall）**： 防火墙主要限制内网和公网的通讯，通常丢弃未经许可的数据包。防火墙会检测(但是不修改)试图进入内网数据包的IP地址和TCP/UDP端口信息。
**网络地址转换协议**[（NAT）](http://en.wikipedia.org/wiki/NAT)： 用来给你的（私网）设备映射一个公网的IP地址的协议。一般情况下，路由器的WAN口有一个公网IP，所有连接这个路由器LAN口的设备会分配一个私有网段的IP地址（例如192.168.1.3）。私网设备的IP被映射成路由器的公网IP和唯一的端口，通过这种方式不需要为每一个私网设备分配不同的公网IP，但是依然能被外网设备发现。NAT不止检查进入数据包的头部，而且对其进行修改，从而实现同一内网中不同主机共用更少的公网IP（通常是一个）。
**基本NAT（Basic NAT）**： 基本NAT会将内网主机的IP地址映射为一个公网IP，不改变其TCP/UDP端口号。基本NAT通常只有在当NAT有公网IP池的时候才有用。
**网络地址-端口转换器（NAPT）**： 到目前为止最常见的即为NAPT，其检测并修改出入数据包的IP地址和端口号，从而允许多个内网主机同时共享一个公网IP地址。
**锥形NAT（Cone NAT）**： 在建立了一对（公网IP，公网端口）和（内网IP，内网端口）二元组的绑定之后，Cone NAT会重用这组绑定用于接下来该应用程序的所有会话（同一内网IP和端口），只要还有一个会话还是激活的。 例如，假设客户端A建立了两个连续的对外会话，从相同的内部端点（10.0.0.1:1234）到两个不同的外部服务端S1和S2。Cone NAT只为两个会话映射了一个公网端点（155.99.25.11:62000）， 确保客户端端口的“身份”在地址转换的时候保持不变。由于基本NAT和防火墙都不改变数据包的端口号，因此这些类型的中间件也可以看作是退化的Cone NAT。
```shell
 Server S1                                     Server S2
18.181.0.31:1235                              138.76.29.7:1235
       |                                             |
       |                                             |
       +----------------------+----------------------+
                              |
  ^  Session 1 (A-S1)  ^      |      ^  Session 2 (A-S2)  ^
  |  18.181.0.31:1235  |      |      |  138.76.29.7:1235  |
  v 155.99.25.11:62000 v      |      v 155.99.25.11:62000 v
                              |
                           Cone NAT
                         155.99.25.11
                              |
  ^  Session 1 (A-S1)  ^      |      ^  Session 2 (A-S2)  ^
  |  18.181.0.31:1235  |      |      |  138.76.29.7:1235  |
  v   10.0.0.1:1234    v      |      v   10.0.0.1:1234    v
                              |
                           Client A
                        10.0.0.1:1234
```
### 1.1.2 UDP打洞(UDP hole punching)
P2P通信技术中被广泛采用的技术“UDP打洞”。UDP打洞技术依赖于通常防火墙和cone NAT允许正当的P2P应用程序在中间件中打洞且与对方建立直接链接的特性。
在学习UDP打洞之前，我们先了解一下另外两种P2P通信技术。
（1）中继（Relaying）
中继是最可靠但效率最低的一种P2P通信技术，它的原理是通过一台服务器来中继转发不同客户端的数据。
```shell
 Server S
                          |
                          |
   +----------------------+----------------------+
   |                                             |
 NAT A                                         NAT B
   |                                             |
   |                                             |
Client A                                      Client B
```


什么意思呢？就是我和你开视频，我和你的视频数据会直接被我们共同连接上的一台服务器接收，这台服务器会将你我的视频数据分别转发响应给我和你的客户端。这样服务器压力就很大，带宽需求也非常大，当仅仅只有两个客户端连接服务器开视频的话，服务器的带宽就至少是客户端带宽的两倍，CPU消耗同样也是。那么当同时视频通话的人很多了，那么服务器的压力难以想象。
所以中继是一种效率很低的P2P通信技术。
（2）逆向连接（Connection reversal）
这种连接只有在两个通信端点中有一个不存在中间件的时候有效。
例如，客户端A在NAT之后而客户端B拥有全局IP地址，如下图：
```shell
Server S
                        18.181.0.31:1235
                               |
                               |
        +----------------------+----------------------+
        |                                             |
      NAT A                                           |
155.99.25.11:62000                                    |
        |                                             |
        |                                             |
     Client A                                      Client B
  10.0.0.1:1234                               138.76.29.7:1234　
```
```shell
客户端A内网地址为10.0.0.1，且应用程序正在使用TCP端口1234。A和服务器S建立了一个连接，服务器的IP地址为18.181.0.31，监听1235端口。NAT A给客户端A分配了TCP端口62000，地址为NAT的公网IP地址155.99.25.11， 作为客户端A对外当前会话的临时IP和端口。因此S认为客户端A就是155.99.25.11:62000。而B由于有公网地址，所以对S来说B就是138.76.29.7:1234。
```


当客户端B想要发起一个对客户端A的P2P链接时，要么链接A的外网地址155.99.25.11:62000，要么链接A的内网地址10.0.0.1:1234，然而两种方式链接都会失败。 链接10.0.0.1:1234失败自不用说，为什么链接155.99.25.11:62000也会失败呢？来自B的TCP SYN握手请求到达NAT A的时候会被拒绝，因为对NAT A来说只有外出的链接才是允许的。
在直接链接A失败之后，B可以通过S向A中继一个链接请求，从而从A方向“逆向“地建立起A-B之间的点对点链接。
现在很多P2P系统都实现了这种技术，但是这种技术有局限性，只有当其中一放客户端有公网IP的时候才能建立起连接。为什么现在很多P2P系统都实现了逆向连接技术，因为我们接下来要讲的UDP打洞技术，主要是依赖这种技术。
## **UDP打洞正文开始**：
现在最多的网路连接情况是双方都是在内网下，都需要通过NAT进行地址转换，所以上面的逆向连接不适用，但是可以利用逆向连接技术进行改造。
假设客户端A和客户端B的地址都是内网地址，且在不同的NAT后面。A、B上运行的P2P应用程序和服务器S都使用了UDP端口1234，A和B分别初始化了 与Server的UDP通信，地址映射如图所示:
```shell
  Server S
                        18.181.0.31:1234
                               |
                               |
        +----------------------+----------------------+
        |                                             |
      NAT A                                         NAT B
155.99.25.11:62000                            138.76.29.7:31000
        |                                             |
        |                                             |
     Client A                                      Client B
  10.0.0.1:1234                                 10.1.1.3:1234
```


现在假设客户端A打算与客户端B直接建立一个UDP通信会话。如果A直接给B的公网地址138.76.29.7:31000发送UDP数据，NAT B将很可能会无视进入的 数据（除非是Full Cone NAT），因为源地址和端口与S不匹配，而最初只与S建立过会话。B往A直接发信息也类似。
假设A开始给B的公网地址发送UDP数据的同时，给服务器S发送一个中继请求，要求B开始给A的公网地址发送UDP信息。
A往B的输出信息会导致NAT A打开 一个A的内网地址与与B的外网地址之间的新通讯会话，B往A亦然。一旦新的UDP会话在两个方向都打开之后，客户端A和客户端B就能直接通讯， 而无须再通过引导服务器S了。
UDP打洞技术有许多有用的性质。一旦一个的P2P链接建立，链接的双方都能反过来作为“引导服务器”来帮助其他中间件后的客户端进行打洞， 极大减少了服务器的负载。应用程序不需要知道中间件具体是什么（如果有的话），因为以上的过程在没有中间件或者有多个中间件的情况下 也一样能建立通信链路。
**还有一些特殊情况**：当通信双方都在同一局域网，也就是两个客户端都在一个内网下呢？是不是可以降低NAT转换，直接在内网上连接呢？此外还有，当一些大型企业，内网中有多级NAT转换呢？这里已不再本文的讨论中了，详细可以看以下参考文章详细了解：
> 参考文章：[https://zhuanlan.zhihu.com/p/26796476](https://zhuanlan.zhihu.com/p/26796476)

学到这里，根据上面的原理是可以实现自己的一套程序和通信规则，但很多时候是需要对接第三方的协议，往往这个适配是比较麻烦的。因此就产生了标准化的通用规则（STUN、TURN、ICE），下面的几个章节将逐个介绍这些协议。
## 1.2 STUN协议
STUN（[STUN/RFC3489(废弃)](http://www.rfc-editor.org/info/rfc3489)，[STUN/RFC5389](http://www.rfc-editor.org/info/rfc5389)）是P2P标准化通信规则（协议）之一。
### 1.2.1 简介
NAT的会话穿越功能[Session Traversal Utilities for NAT (STUN)](http://en.wikipedia.org/wiki/STUN) (缩略语的最后一个字母是NAT的首字母)是一个允许位于


NAT后的客户端找出自己的公网地址，判断出路由器阻止直连的限制方法的协议。
STUN是一个C/S架构的协议，支持两种传输类型。一种是请求/响应（request/respond）类型，由客户端给服务器发送请求，并等待服务器返回响应；另一种是指示类型（indication transaction），由服务器或者客户端 发送指示，另一方不产生响应。对于请求/响应类型，允许客户端将响应和产生响应的请求连接起来； 对于指示类型，通常在debug时使用。我们主要了解请求/响应类型。
### 1.2.2 通信过程
客户端通过给公网的STUN服务器发送请求获得自己的公网地址信息，以及是否能够被（穿过路由器）访问。

1. 客户端A向服务器产生一个Request（STUN叔叔，你能告诉我我的ip是多少吗）
1. 服务器接收Request，检查报文是否合法，并生成Success响应或Error响应（A小朋友，你的ip是208.141.55.130:3255）

![](https://cdn.nlark.com/yuque/0/2022/png/22838525/1645065022544-58092cd7-5f20-4898-bbdc-c89f628549b3.png#crop=0&crop=0&crop=1&crop=1&id=Fh2Xo&originHeight=378&originWidth=259&originalType=binary&ratio=1&rotation=0&showTitle=false&status=done&style=none&title=)
## 1.3 TURN协议
TURN（[TURN/RFC5766](http://www.rfc-editor.org/info/rfc5766)）是P2P标准化通信规则（协议）之一，是对STUN的补充。
### 1.3.1 简介
TURN的全称为[Traversal Using Relays around NAT (TURN)](http://en.wikipedia.org/wiki/TURN) ，是STUN/RFC5389的一个拓展，主要添加了Relay功能。前面介绍的STUN协议处理的是市面上大多数的Cone NAT，但还有少量的设备使用的Symmetric NAT。因此传统的打洞方法不适用，为了保证这一部分设备能够建立通信，我们不得不通过中继（Relaying）的方法进行连接，这时就需要公网的服务器作为一个中继， 对来往的数据进行转发。这个转发的协议就被定义为TURN。这种情况会增加服务器负担，所以这是最坏的情况的通信解决方案。
TURN服务器与客户端之间的连接都是基于UDP的，但是服务器和客户端之间可以通过其他各种连接来传输STUN报文, 比如TCP/UDP/TLS-over-TCP。客户端之间通过中继传输数据时候，如果用了TCP，也会在服务端转换为UDP，因此建议客户端使用 UDP来进行传输。至于为什么要支持TCP，那是因为一部分防火墙会完全阻挡UDP数据，而对于三次握手的TCP数据则不做隔离。
### 1.3.2 通信过程
客户端A向STUN服务器发送请求获取自己的公网地址，STUN服务器可以获取到客户端A的地址，但发现客户端A的使用的Symmetric NAT，因此STUN服务器告诉客户端A，我不能帮助你和客户端B建立连接，你们之间可以通过TURN进行连接。因此客户端A和客户端B同时去连接TURN服务器，通过TURN服务器进行中继连接。

1. 客户端A向STUN服务器产生一个Request（STUN叔叔，你能告诉我我的ip是多少吗）
1. STUN服务器响应（A小朋友，你的ip是208.141.55.130:3255，可是你的ip别人不能和你连接哦，你需要去找你TURN大伯，他是专门负责帮你连接）
1. 客户端A向TURN服务器发起请求（TURN大伯，STUN叔叔叫我来找你）
1. TURN服务器响应（A小侄儿，我知道了，但是现在还没有其他小朋友找你哦，你可以在这附近逛一逛，每10分钟要给我报告一下你还在这附近哦，一有其他小朋友来找你我就通知你。）

![](https://cdn.nlark.com/yuque/0/2022/png/22838525/1645065089371-c383fdf2-2821-4973-af12-894d55c14f95.png#crop=0&crop=0&crop=1&crop=1&id=I4r5n&originHeight=297&originWidth=295&originalType=binary&ratio=1&rotation=0&showTitle=false&status=done&style=none&title=)
## 1.4 ICE协议
TURN（[ICE/RFC5245](http://www.rfc-editor.org/info/rfc5245)）是P2P标准化通信规则（协议）之一，提供了完整的NAT传输解决方案。
STUN、TURN都是工具类协议，只提供穿透NAT的功能。且TURN本身就是被设计为ICE/RFC5245的一部分
### 1.4.1 简介
ICE的全称为[Interactive Connectivity Establishment (ICE)](http://en.wikipedia.org/wiki/Interactive_Connectivity_Establishment)，即交互式连接建立。在实际的网络当中，有很多原因能导致简单的从A端到B端直连不能如愿完成。这需要绕过阻止建立连接的防火墙，给你的设备分配一个唯一可见的地址（通常情况下我们的大部分设备没有一个固定的公网地址），如果路由器不允许主机直连，还得通过一台服务器转发数据。ICE通过使用STUN、TURN、NAT、SDP技术完成上述工作。(引用自：[https://developer.mozilla.org/en-US/docs/Web/API/WebRTC_API/Protocols](https://developer.mozilla.org/en-US/docs/Web/API/WebRTC_API/Protocols))
ICE是一个用于在[Offer/Answer](http://www.rfc-editor.org/info/rfc3264)模式下的NAT传输协议，主要用于UDP下多媒体会话的建立，其使用了STUN协议以及TURN 协议，同时也能被其他实现了Offer/Answer模型的的其他程序所使用，比如[SIP](http://www.rfc-editor.org/info/rfc3261)(Session Initiation Protocol)。
网络编程的ICE（Internate Communications Engine）：是一种用于分布式程序设计的网络通信中间件，本文指并非此ICE
交互式连接ICE（Interactive Connectivity Establishment）：是一个允许你的浏览器和对端浏览器建立连接的协议框架。
### 1.4.2 SDP会话描述
ICE信息的描述格式通常采用标准的[SDP](http://www.rfc-editor.org/info/rfc4566)，其全称为[Session Description Protocol (SDP)](http://en.wikipedia.org/wiki/Session_Description_Protocol) ，即会话描述协议。SDP不是一个真正的协议，而是一种数据格式，用于描述在设备之间共享媒体的连接。可以被其他传输协议用来交换必要的信息，如SIP和RTSP等。
**SDP格式**：
SDP由一行或多行UTF-8文本组成，每行以一个字符的类型开头，后跟等号（“ =”），然后是包含值或描述的结构化文本，其格式取决于类型。
SDP会话描述包含了多行如下类型的文本:
```shell
<type>=<value>
```
以给定字母开头的文本行通常称为“字母行”。例如，提供媒体描述的行的类型为“ m”，因此这些行称为“ m行”。
```shell
m=audio 49170 RTP/AVP 0
```
<type>是大小写敏感的，其中一些行是必须要有的，有些是可选的，所有元素都必须以固定顺序给出。如下所示，其中可选的元素标记为* ：
`会话描述`
```shell
     v=  (protocol version)
     o=  (originator and session identifier)
     s=  (session name)
     i=* (session information)
     u=* (URI of description)
     e=* (email address)
     p=* (phone number)
     c=* (connection information -- not required if included in
          all media)
     b=* (zero or more bandwidth information lines)
     One or more time descriptions ("t=" and "r=" lines; see below)
     z=* (time zone adjustments)
     k=* (encryption key)
     a=* (zero or more session attribute lines)
     Zero or more media descriptions
```
`时间信息描述:`
```shell
     t=  (time the session is active)
     r=* (zero or more repeat times)
```
`多媒体信息描述(如果有的话):`
```shell
  m=  (media name and transport address)
     i=* (media title)
     c=* (connection information -- optional if included at
          session level)
     b=* (zero or more bandwidth information lines)
     k=* (encryption key)
     a=* (zero or more media attribute lines)
```
所有元素的type都为小写，并且不提供拓展.但是我们可以用a(attribute)字段来提供额外的信息。一个SDP描述的例子如下：
```shell
v=0
o=jdoe 2890844526 2890842807 IN IP4 10.47.16.5
s=SDP Seminar
i=A Seminar on the session description protocol
u=http://www.example.com/seminars/sdp.pdf
e=j.doe@example.com (Jane Doe)
c=IN IP4 224.2.17.12/127
t=2873397496 2873404696
a=recvonly
m=audio 49170 RTP/AVP 0
m=video 51372 RTP/AVP 99
a=rtpmap:99 h263-1998/90000
```


具体字段的type/value描述和格式可以参考[RFC4566](http://www.rfc-editor.org/info/rfc4566)。
### 1.4.3 Offer/Answer模型
SDP用来描述多播主干网络的会话信息，但是并没有具体的交互操作细节是如何实现的，因此[RFC3264](https://link.zhihu.com/?target=http%3A//www.rfc-editor.org/info/rfc3264) 定义了一种基于SDP的Offer/Answer模型。
在该模型中，会话参与者的其中一方生成一个SDP报文构成offer， 其中包含了一组offer希望使用的多媒体流和编解码方法，以及offer用来接收改数据的IP地址和端口信息。
offer传输到会话的另一端(称为answer)，由这一端生成一个answer，即用来响应对应offer的SDP报文。
answer中包含不同offer对应的多媒体流，并指明该流是否可以接受。
![](https://cdn.nlark.com/yuque/0/2022/png/22838525/1645065272967-f0b7952b-46e9-42b8-82e5-76e9f3babb86.png#crop=0&crop=0&crop=1&crop=1&id=Jxvx2&originHeight=540&originWidth=720&originalType=binary&ratio=1&rotation=0&showTitle=false&status=done&style=none&title=)


### 1.4.4 ICE工作流程
一个典型的ICE工作环境如下，有两个端点A和B，都运行在各自的NAT之后(他们自己也许并不知道)，NAT的类型和性质也是未知的。L和R通过交换SDP信息在彼此之间建立多媒体会话，通常交换通过一个SIP服务器完成：
```shell
                 +-----------+
                 |    SIP    |
+-------+        |    Srvr   |         +-------+
| STUN  |        |           |         | STUN  |
| Srvr  |        +-----------+         | Srvr  |
|       |        /           \         |       |
+-------+       /             \        +-------+
               /<- Signaling ->\
              /                 \
         +--------+          +--------+
         |  NAT   |          |  NAT   |
         +--------+          +--------+
           /                       \
          /                         \
         /                           \
     +-------+                    +-------+
     | Agent |                    | Agent |
     |   A   |                    |   B   |
     |       |                    |       |
     +-------+                    +-------+
```


ICE的基本思路是，每个终端都有一系列传输地址(包括传输协议，IP地址和端口)的候选，可以用来和其他端点进行通信。其中可能包括：

- 直接和网络接口联系的传输地址(host address)
- 经过NAT转换的传输地址,即反射地址(server reflective address)
- TURN服务器分配的中继地址(relay address)
> 通过之前的学习，我们可以了解到每个终端的情况是比较复杂的（有的终端可能同时连着wifi和网线，有多个内网地址），所有每个终端有多种可以连接的方案。

获取到这一系列传输地址后，会以一定优先级将地址排序。按照优先级和其他终端的传输地址进行组合检测连接可用性（连接性检查：Connectivity Checks）。
两端连接性检查，是一个4次握手过程:
```shell
A                        B
-                        -
STUN request ->                  \  A's
          <- STUN response       /  check

           <- STUN request       \  B's
STUN response ->                 /  check
```
**连接性检查详细过程**：

1. 为中继候选地址生成许可(Permissions)；
1. 从本地候选往远端候选发送Binding Request：在Binding请求中通常需要包含一些特殊的属性，以在ICE进行连接性检查的时候提供必要信息：
   - PRIORITY 和 USE-CANDIDATE：优先级和候选
   - ICE-CONTROLLED和ICE-CONTROLLING：标识本端是受控方还是主控方（offer生成方）。
   - 生成Credential：STUN短期身份验证
3. 处理Response：当收到Binding Response时，终端会将其与Binding Request相联系，通常生成事务ID。随后将会将此事务ID与候选地址对进行绑定。
   - 成功响应：要同时满足三个条件（STUN传输产生一个Success Response；response的源IP和端口等于Binding Request的目的IP和端口；response的目的IP和端口等于Binding Request的源IP和端口）
   - 失败响应：487错误，并将检测地址状态设置为Waiting

以上仅对协议作了简单的介绍，具体服务器程序实现可参考：[https://github.com/evilpan/TurnServer](https://github.com/evilpan/TurnServer)
## 1.5 经典WebRTC连接建立流程
通过前面的协议了解学习，相信大家已经对WebRTC的底层连接流程有了一个模糊的意思，这里有张图展现了具体的连接流程。
![](https://cdn.nlark.com/yuque/0/2022/png/22838525/1645065440169-112e9b07-0921-4b1f-b3f8-30126cb3091c.png#crop=0&crop=0&crop=1&crop=1&id=DFbWX&originHeight=661&originWidth=766&originalType=binary&ratio=1&rotation=0&showTitle=false&status=done&style=none&title=)
> 引用自：[https://aggresss.blog.csdn.net/article/details/106832965](https://aggresss.blog.csdn.net/article/details/106832965)

# 二、服务器搭建
## 2.1 STUN/TURN服务器【可跳过】
网上有公用的stun服务器，本节可直接跳过。
STUN服务器已有现成项目：[https://github.com/coturn/coturn](https://github.com/coturn/coturn)
以下是在ubuntu上的安装和配置：
### 2.1.1 安装coturn
可以克隆github上的源码编译安装，在ubuntu里有直接的安装包
```bash
apt-get -y update
apt-get -y install coturn
```


安装完毕后，先关闭coturn服务：
```shell
systemctl stop coturn
```
​

### 2.1.2 配置coturn
**(1) 允许turnserver**
首先需要允许turnserver，打开/etc/default/coturn文件，将注释去掉：
```shell
vim /etc/default/coturn
```
取消注释后如下：
```shell
TURNSERVER_ENABLED=1
```
**(2) 获取ip和SSL**
首选需要获取一下自己的内网ip以及网卡:
```shell
ifconfig
```
生成SSL证书:
```shell
apt install openssl
openssl req -x509 -newkey rsa:2048 -keyout /etc/turn_server_pkey.pem -out /etc/turn_server_cert.pem -days 99999 -nodes 
```
**(3) 配置**
接下来正式改配置文件/etc/turnserver.conf，改之前先将原文件备份一个：
```shell
mv /etc/turnserver.conf /etc/turnserver.conf.bat
```
然后新建配置文件：
```shell
vim /etc/turnserver.conf
```
然后复制以下配置：
```shell
server-name=turn.webrtc.zzboy.cn
realm=turn.webrtc.zzboy.cn

fingerprint

relay-device=eth0   #与前ifconfig查到的网卡名称一致

listening-ip=192.168.0.186    #内网IP
listening-port=3478
tls-listening-port=5349
relay-ip=192.168.0.186
external-ip=121.36.105.109    #公网IP

relay-threads=50
lt-cred-mech
no-cli
verbose

cert=/etc/turn_server_cert.pem
pkey=/etc/turn_server_pkey.pem
#pidfile=/var/run/turnserver.pid
min-port=49152
max-port=65535
user=jun:123456    #用户名密码，创建IceServer时用
```
### 2.1.3 测试
工具：[Trickle ICE](https://webrtc.github.io/samples/src/content/peerconnection/trickle-ice/)
点击打开上面的工具
## 2.2 Nodejs构建信令服务器(Signal Server)
信令服务器我直接使用的一个开源项目：[https://github.com/qdgx/WebRtcRoomServer](https://github.com/qdgx/WebRtcRoomServer)
其实信令服务器已经涉及到实战了，这里就不讲具体实现，这里只先部署。
单纯地看，信令服务器其实可以算作是一个后端项目，我们这里部署也只是对该项目进行服务器部署。这里我使用的这个开源项目是使用node.js开发的，因此部署步骤和node.js部署步骤相差无异。
以下是我在ubuntu上的安装和配置：
### 2.2.1 安装node环境
**(1) 更新环境，安装curl、git**
```shell
apt-get update
apt-get install -y curl git
```
**(2) 安装node.js**
先去官网https://nodejs.org/，查看最新稳定长期支持版，发现最新稳定版是14.15.3 LTS，node.js的每个大版本号都有相对应的源，比如这里的14.15.3版本的源是 [https://deb.nodesource.com/setup_14.x](https://deb.nodesource.com/setup_14.x)
所以在终端执行：
```shell
curl -sL https://deb.nodesource.com/setup_10.x | sudo -E bash -
```
然后安装node.js
```shell
apt-get install nodejs
```
node -v 和 npm -v 查看node和npm是否安装成功
### 2.2.2 克隆项目，安装依赖
进入用户目录，克隆项目：
```shell
cd ~/ && git clone https://github.com/qdgx/WebRtcRoomServer.git
```
安装依赖：
```shell
cd ~/WebRtcRoomServer
npm i
```
启动服务：
```shell
node app.js
```
在浏览器打开以下地址，测试一下是否访问：
[https://你服务器外网地址:8443](https://xn--6qq22f55d4wakcs4l640b8l8b:8443/)
只要浏览器提示该页面存在风险，即表示项目已生效，点击高级，选择接受风险继续访问即可。（为什么提示风险：因为这个项目的证书是自签名证书）
![](https://cdn.nlark.com/yuque/0/2022/png/22838525/1645066216163-d0ea1e47-1827-4ec3-a6ab-5a727fd259e2.png#crop=0&crop=0&crop=1&crop=1&id=vInJW&originHeight=1396&originWidth=2062&originalType=binary&ratio=1&rotation=0&showTitle=false&status=done&style=none&title=)
如果无法访问，请检查服务器安全组是否打开了TCP和UDP协议的8443端口，有些服务器开端口需要在服务器上那配置安全组，比如阿里云ECS和华为云。
### 2.2.3 pm2管理node服务
直接用node app.js运行项目，在关闭终端后，node项目也会随之被关闭，因此需要使用额外的工具来保持node服务一直开启。
安装pm2：
```shell
npm install pm2@latest -g
```
启动服务：
```shell
pm2 start app.js --name signal-server --watch
```

- name：给应用命名，可以不管
- watch：相当于热更新，应用文件更新后会重启应用
> 有关pm2的使用，可以百度查询一下，也可以参考本人之前写的一篇文章：[https://www.zzboy.cn/Learning/f360ef90efef](https://www.zzboy.cn/Learning/f360ef90efef)

# 三、API学习
以下主要介绍下一章节实战开中需要到的常用接口，完整的接口学习可查看对应官方文档。
## 3.1 [socket.io](http://socket.io/)
官方文档：[https://socket.io/docs/v3/](https://socket.io/docs/v3/)
中文w3chool：[https://www.w3cschool.cn/socket/](https://www.w3cschool.cn/socket/)
Socket是一种**全双工通信**,当客户端和服务端建立起连接后，如果不主动断开，双方可以一直互相发送消息，适合于双方频繁通信的场景，也是支持服务端主动推送的一种通信方式。WebSocket是Html5推出的前端可以直接使用的API，不过目前项目中用的还是socket.io比较多。socket.io在浏览器环境下封装了WebSocket, 可以给开发者带来更好的体验，在功能上也更完善。
socket.io主要使用两个方法：

- emit(description: string, data: any：监听事件；description是标识；data是需要发送的数据。
- on(description: string, callback: function：监听事件；description表示监听的标识；callback是监到事件后处理方法，参数是emit发送的数据。

通俗说，一个就是发送，一个是接收。发送方法需要指定谁(description)来接收；接收方法找到对应description接收。
### 3.1.1 服务器端
**(1) 安装**
```shell
npm install socket.io
```
**(2) 初始化**
```shell
const httpServer = require("http").createServer(); // 创建http服务

// 使用socket.io监听http服务
const socketIO = require("socket.io");
const io = socketIO.listen(httpServer);

// 也可以使用如下方式
const io = require("socket.io")(httpServer, {
  // options配置项
});
```
配置项：是初始配置socket.io的一些参数，我们使用默认的接口，如需要配置，可以看文档了解具体配置项：[https://socket.io/docs/v3/server-api/#new-Server-httpServer-options](https://socket.io/docs/v3/server-api/#new-Server-httpServer-options)
根据WebRTC安全策略，我们需要使用https，因此，比较**完整的初始化代码**为：
```shell
const fs = require('fs');
const server = require('https').createServer({
  key: fs.readFileSync('/tmp/key.pem'),
  cert: fs.readFileSync('/tmp/cert.pem')
});
const options = { /* ... */ };
const io = require('socket.io')(server, options);

io.on('connection', socket => { /* ... */ });

server.listen(3000);

```
**(3) 方法**
**io.on(‘connection’, fn)** ：监听客户端连接
从上面初始化代码不难看出，socket.io第一个方法应该io.on('connection', fn)。
connection是保留description，当有客户端连接上当前服务器时，就会触发。
我们需要在其回调中处理相关业务：
```shell
io.on('connection', socket => {
  // 监听断开连接
  socket.on('disconnect', reason => console.log(reason)) // socket断开监听，disconnect也是保留字段
  
	// 其他业务监听
  socket.on('join', data => console.log(`欢迎${data.name}进入直播间`));
});
```
**socket.on(‘disconnect’, fn)** ：监听客户端断开连接
```shell
socket.on('disconnect', reason => {
  console.log(reason); // 断开原因有很多，可能是用户主动断开，也可能是浏览器直接关闭等
})
```
socket.emit() : 发送信息
### 3.1.2 客户端
## 3.2 音视频相关API
### 3.2.1 navigator.mediaDevices
浏览器API，可以通过该浏览器API获取用户媒体设备，通常只会用到一个方法：getUserMedia(options)，调用该方法时，浏览器会弹出请求音频或视频的权限，用户同意授权过后，即可获取到音视频流。
```shell
navigator.mediaDevices.getUserMedia(options)
.then(function(stream) {
  /* use the stream */
})
.catch(function(err) {
  /* handle the error */
});
```
需要注意：navigator的mediaDevices属性需要在https环境下才会有，这是浏览器的限制。
**options: 配置项**
一般可直接设置为：{ audio: true, video: true }，表示为获取音频和视频。
```shell
navigator.mediaDevices.getUserMedia({
  audio: true,
  video: true
})
.then(function(stream) {
  /* use the stream */
})
.catch(function(err) {
  /* handle the error */
});
```
视频方面，也可以准确定义视频画面的宽高：
```shell
navigator.mediaDevices.getUserMedia({
  audio: true,
  video: { width: 1280, height: 720 } // 当定义宽高是，视频算是true，请求视频权限
})
.then(function(stream) {
  /* use the stream */
})
.catch(function(err) {
  /* handle the error */
});
```
> 其他更多配置可参考：[https://developer.mozilla.org/en-US/docs/Web/API/MediaDevices/getUserMedia](https://developer.mozilla.org/en-US/docs/Web/API/MediaDevices/getUserMedia)

### 3.2.2 video
**(1) video标签**
```shell
<video src="path/to/movie.mp4" controls="controls">
您的浏览器不支持 video 标签。
</video>
```
属性：

- autoplay: 如果出现该属性，则视频在就绪后马上播放
- controls：如果出现该属性，则向用户显示控件，比如播放按钮
- loop：如果出现该属性，则当媒介文件完成播放后再次开始播放
- muted：规定视频的音频输出应该被静音
- poster：规定视频下载时显示的图像，或者在用户点击播放按钮前显示的图像
- preload：如果出现该属性，则视频在页面加载时进行加载，并预备播放。如果使用 “autoplay”，则忽略该属性
- src：要播放的视频的 URL
- width：设置视频播放器的宽度，单位px
- height：设置视频播放器的高度，单位px

我们在进行音视频通话时，通常
**本地视频（我方视频）**应如下：
```shell
<video id="local" muted autoplay>
您的浏览器不支持 video 标签。
</video>
```
本地视频静音播放，因为我们无需我们自己发出的声音，因为我们到时候视频资源是从设备直接实时获取视频流，因此无需设置src，并且设置autoplay，可以让我们获取到视频流直接播放。
**远程视频（对方视频）**应如下：
```shell
<video id="remote" poster="xxx" autoplay>
您的浏览器不支持 video 标签。
</video>
```
远程视频同样设置autoplay属性，让接收到的视频流直接播放。另外可设置一个poster属性，可以在呼叫过程中或者被呼叫时，让页面显示呼叫中或者是显示对方头像肖像等，不然页面全黑会显得很尴尬。
**(2) video对象**
使用音视频通话，我们控制音视频的播放基本通过js实现的，就连前面介绍的video标签一般都是通过js创建。video对象有很多属性，我这里只简单介绍部分属性，能基本满足WebRTC音视频通话。
我们要实现音视频实时通讯，传递的数据是音视频流，音视频流怎么让video播放出来呢？看看下面代码：
```shell
/**
 * 视频流绑定到video节点展示
 * @param {dom} video video节点
 * @param {obj} stream 视频流
 */
const pushStreamToVideo = (video, stream) => {
  video.srcObject = stream;
}

// 获取video节点
const domLocalVideo = $('#local');

// 调用摄像头
navigator.mediaDevices.getUserMedia({
  audio: true,
  video: true
})
.then(stream => {
  pushStreamToVideo(domLocalVideo[0], stream); // 实时显示
})
.catch(err => {
  alert(`getUserMedia() error: ${err.name}`)
});
```
不难看出，video对象有个srcObject的属性，初始时该属性值是null，将我们获取到音视频流直接赋值给该属性，我们的video标签就可以实时播放了。上面这个例子是调用本地摄像头并展示到一个id=local的video标签上，需要在https上就可以正常运行了。
**我们如何关闭视频呢？**
方法一：简单粗暴，关闭页面或者关闭浏览器。（你会让用户这么干么？）
方法二：使用MediaStream.getTracks()，获取到所有媒体流轨道，每条轨道调用一个方法stop()，就可以关闭当前流，摄像头也会停止录制。
```shell
/**
 * 关闭摄像头
 * @param {dom} video video节点
 */
const closeCamera = video => {
  video.srcObject.getTracks()[0].stop(); // audio
  video.srcObject.getTracks()[1].stop(); // video
}
```
音频是第一条轨道，视频是第二条轨道，两个同时关闭即可。
## 3.3 WebRTC
官方文档（不推荐）：[https://www.w3.org/TR/webrtc/#peer-to-peer-connections](https://www.w3.org/TR/webrtc/#peer-to-peer-connections)
官方文档中文翻译（不推荐）：[https://github.com/RTC-Developer/WebRTC-Documentation-in-Chinese/tree/master/resource](https://github.com/RTC-Developer/WebRTC-Documentation-in-Chinese/tree/master/resource)
MDN Web Docs（推荐）：[https://developer.mozilla.org/en-US/docs/Web/API/WebRTC_API](https://developer.mozilla.org/en-US/docs/Web/API/WebRTC_API)
### 3.3.1 RTCPeerConnection
[https://developer.mozilla.org/en-US/docs/Web/API/RTCPeerConnection/](https://developer.mozilla.org/en-US/docs/Web/API/RTCPeerConnection/)
RTCPeerConnection是浏览器之间点对点连接的核心API，用于处理对等体之间流数据的稳定和有效通信，
```shell
const pc = new RTCPeerConnection(serverConfig);
```
serverConfig包含iceServers参数，它包含有关STUN和TURN服务器相关信息数组，在查找ICE的时候候选使用。可以在网上找一些公共的STUN服务器，也可以使用前面章节我们自己通过coturn搭建的STUN服务器。
```shell
const serverConfig = {
  iceServers: [
    {
      urls: 'stun:stun.xten.com'
    },
    {
      urls: 'stun:你的服务器ip:3478', // 见2.1服务器搭建
      username: '用户名',
      credential: '密码'
    }
  ]
}

```


**(1) onicecandidate = eventHandler**
作用：监听RTCPeerConnection实例上发生icecandidate事件，该函数会返回ICE协商结果，我们需要将结果发送给信令服务器，交由信令服务器转发给对方。
```shell
pc.onicecandidate = event => {
  if (event.candidate) {
    sendCandidateToRemotePeer(event.candidate);
  } else {
    /* there are no more candidates coming during this negotiation */
  }
};
```
**(2) ontrack = eventHandler **
作用：监听RTCPeerConnection实例上接收到远程的数据流，该函数可获取到对端的媒体流。
```shell
pc.ontrack = event => {
  document.getElementById("received_video").srcObject = event.streams[0];
};
```
**(3) addTrack(track, stream…)**
作用：设置轨道，该轨道将会在连同后传输到对端。
```shell
async openCall(pc) {
  const gumStream = await navigator.mediaDevices.getUserMedia({video: true, audio: true});
  for (const track of gumStream.getTracks()) {
    pc.addTrack(track);
  }
}

```
MDN不建议使用addStream()
**(3) removeTrack(sender)**
作用：删除轨道，删除已添加的轨道，用于挂断的时候
```shell
var pc, sender;
navigator.getUserMedia({video: true}, function(stream) {
  pc = new RTCPeerConnection();
  var track = stream.getVideoTracks()[0];
  sender = pc.addTrack(track, stream);
});

document.getElementById("closeButton").addEventListener("click", function(event) {
  pc.removeTrack(sender);
  pc.close();
}, false);
```
不建议的：[onremovestream](https://developer.mozilla.org/en-US/docs/Web/API/RTCPeerConnection/onremovestream)
**(5) setLocalDescription()/setRemoteDescription()**
[setLocalDescription(sessionDescription)](https://developer.mozilla.org/en-US/docs/Web/API/RTCPeerConnection/setLocalDescription)：
设置本地offer，将自己的描述信息加入到PeerConnection中，参数类型：RTCSessionDescription（见下一小节 3.2.2 RTCSessionDescription）
[setRemoteDescription(sessionDescription)](https://developer.mozilla.org/en-US/docs/Web/API/RTCPeerConnection/setRemoteDescription)：
设置远端的answer，将对方的描述信息加入到PeerConnection中，参数类型：RTCSessionDescription（见下一小节 3.2.2 RTCSessionDescription）
![](https://cdn.nlark.com/yuque/0/2022/png/22838525/1645066636445-7b0906de-a8c3-4d15-af4f-c67616efd508.png#crop=0&crop=0&crop=1&crop=1&id=itLxU&originHeight=540&originWidth=720&originalType=binary&ratio=1&rotation=0&showTitle=false&status=done&style=none&title=)
通俗说：Alice为了和Bob建立合作关系(连接)，Alice我把拟好了一份合同，并签字了，我这里先保留扫描版，纸质合同通过快递(SDP)给你了，你通过快递(SDP)拿到合同后，先签字确认，这时候纸质合同上都有我们双方的签名了，但我这边还没有你的签名。你保存一下扫描版，然后通过快递把纸质再给我发回来，我拿到快递后，我也保存一下扫描版。这样，你我双放都有双方签名的扫描版合同。合同开始生效！
**(6) createOffer()/createAnswer()**
[createOffer([options])](https://developer.mozilla.org/en-US/docs/Web/API/RTCPeerConnection/createOffer)：
创建一个offer，表示我方的请求。通常在WebRTC通信中，我们会请求对方接收我们的音频和视频数据。
```shell
const offerOptions = {
  offerToReceiveAudio: true, // 请求接收音频
  offerToReceiveVideo: true, // 请求接收视频
},
pc.createOffer(offerOptions)
        .then(offer => onCreateOfferSuccess(offer.sdp))
        .catch(error => onCreateOfferError());

```
[createAnswer([options])](https://developer.mozilla.org/en-US/docs/Web/API/RTCPeerConnection/createAnswer)：
创建一个answer，回应对方offer。answer也是有offer作用的，在回应的时候，表示答应你，并向你请求。
打个比方：A向B表白，请求B做A的女朋友。如果B接受了，表示B成了A女朋友。同时，这也有另外一层含义，表示B有请求：请A做我的男朋友。
```shell
const answerOptions = {
  offerToReceiveAudio: true, // 请求接收音频
  offerToReceiveVideo: true, // 请求接收视频
},
pc.createAnswer(answerOptions)
        .then(answer => onCreateAnswerSuccess(answer.sdp))
        .catch(error => onCreateAnswerError());
```
### 3.3.2 RTCSessionDescription
用于生成Offer/Answer协商过程中SDP协议的相关描述。
```shell
new RTCSessionDescription(rtcDescription)
```
rtcDescription只有两个属性：type，sdp

- type只能设置：‘answer’，‘offer’，‘pranswer’，‘rollback’；
- sdp是标准的SDP会话描述（可由createOffer/createAnswer生成）
### 3.3.3 RTCIceCandidate
[https://developer.mozilla.org/en-US/docs/Web/API/RTCIceCandidate](https://developer.mozilla.org/en-US/docs/Web/API/RTCIceCandidate)
[https://blog.51cto.com/zhangjunhd/25481](https://blog.51cto.com/zhangjunhd/25481)
用于建立ICE连接。通常我们不会手动去实例化一个RTCIceCandidate对象，在前面3.3.1 RTCPeerConnection中的onicecandidate事件回调就是一个RTCIceCandidate对象，我们只需要了解其中几个属性即可。

- **candidate**: 用于连接性检测的对象
- **sdpMid**: candidate的媒体流的识别标签
- **sdpMLineIndex**: candidate的媒体流的相关联的SDP描述索引号
- address: 本机IP地址
- relatedAddress: 中继IP
- port: 本机端口
- relatedPort: 中继端口
- component: 候选协议，只有两种情况：RTP(Real-Time Transport Protocol)， RTCP(Real-Time Transport Control Protocol)
- foundation: 来自于STUN服务器的唯一标识符
- priority: 优先级
- tcpType: 如果使用的TCP协议，这个属性及表示TCP的状态
- type: [RTCIceCandidateType类型](https://developer.mozilla.org/en-US/docs/Web/API/RTCIceCandidateType)
- usernameFragment: ice-ufrag片段，用于生成ice-pwd，同一ICE进程的连接都将使用的是同一个片段。
# 四、实战开发
前面基本上已经列举了大部分基础知识，现在开始运用起来。
本章实战开发，是开发一个 **web实时音视频聊天室** ：输入相同房间号，即可加入聊天室，进行视频聊天。
主要有两个项目，前端界面([页面+WebRTC+socket.io](http://xn--+webrtc+socket-9768by1h.io/))，后端信令服务器控制转发([Express+socket.io](http://express+socket.io/))。
整个项目完整代码：[WebRTC-demo](https://github.com/DOUBLE-Baller/momo/)
## 4.1 环境准备

- anywhere: npm i -g anywhere
## 4.2 信令服务器
因为信令服务器代码结构比较简单，咱们先开发信令服务器。观察1.5 经典WebRTC连接建立流程，不难发现，信令服务器主要需要实现：转发offer、转发answer、转发candidate的三大核心功能。此外，我们开发聊天室，还需要：创建聊天室、退出聊天室的功能。
​

### 4.2.1 搭建项目
（1）创建一个文件夹signal-server，在目录下创建两个文件：
package.json
```shell
{
  "name": "signal-server",
  "version": "1.0.0",
  "author": "Patrick Jun",
  "description": "A webRTC signal server",
  "scripts": {
    "start": "node app.js"
  },
  "dependencies": {
    "express": "^4.17.1",
    "express-session": "^1.17.1",
    "socket.io": "^2.3.0"
  }
}
```


app.js
```shell
const https = require('https');  // https服务
const fs = require('fs');        // fs
const socketIO = require('socket.io');

//读取密钥和签名证书
const options = {
  key: fs.readFileSync('keys/server_key.pem'),
  cert: fs.readFileSync('keys/server_crt.pem'),
}

// 构建https服务器
const apps = https.createServer(options);

const SSL_PORT = 8443;

apps.listen(SSL_PORT);


// 构建signal server
const io = socketIO.listen(apps);

// socket监听连接
io.sockets.on('connection', (socket) => {
  console.log('连接建立');
  // 之后所有业务处理，写在这里面
});

```


（2）创建证书
在项目文件夹下，创建一个文件夹keys，然后开始生成自签名证书：
linux环境下：
```shell
openssl req -x509 -newkey rsa:2048 -keyout ./keys/server_key.pem -out ./keys/server_crt.pem -days 99999 -nodes 
```


windows下：参考 [https://letsencrypt.org/zh-cn/docs/certificates-for-localhost/](https://letsencrypt.org/zh-cn/docs/certificates-for-localhost/)
修改app.js，将秘钥和签名证书的路径改为你电脑中的绝对路径，例如：
```shell
//读取密钥和签名证书
const options = {
  key: fs.readFileSync('D://signal-server/keys/server_key.pem'),
  cert: fs.readFileSync('D://signal-server/keys/server_crt.pem'),
}

```


（3）运行
在项目根目录下，安装依赖：
```shell
npm i
```
然后，启动：
```shell
node app.js
```
打开浏览器，访问：https://localhost:8443
访问时，浏览器会提示不安全的访问，这个时候，直接敲键盘：thisisunsafe 即可继续访问。当看到浏览器地址栏继续一直在请求中，那么就表示项目成功运行。
### 4.2.2 房间功能
房间功能主要包括：创建/加入房间、退出房间。
业务处理，都放在连接成功后的回调函数里。
（1）创建房间
```shell
// socket监听连接
io.sockets.on('connection', (socket) => {
  console.log('连接建立');
  
  // 创建/加入房间
  socket.on('createAndJoinRoom', (message) => {
    const { room } = message;
    console.log('Received createAndJoinRoom：' + room);
    // 判断room是否存在
    const clientsInRoom = io.sockets.adapter.rooms[room];
    const numClients = clientsInRoom ? Object.keys(clientsInRoom.sockets).length : 0;
    console.log('Room ' + room + ' now has ' + numClients + ' client(s)');
    if (numClients === 0) {
      // room 不存在 不存在则创建（socket.join）
      // 加入并创建房间
      socket.join(room);
      console.log('Client ID ' + socket.id + ' created room ' + room);

      // 发送消息至客户端 [id,room,peers]
      const data = {
        id: socket.id, //socket id
        room: room, // 房间号
        peers: [], // 其他连接
      };
      socket.emit('created', data);
    } else {
      // room 存在
      // 加入房间中
      socket.join(room);
      console.log('Client ID ' + socket.id + ' joined room ' + room);
      
      // joined告知房间里的其他客户端 [id,room]
      io.sockets.in(room).emit('joined', {
        id: socket.id, //socket id
        room: room, // 房间号
      });


      // 发送消息至客户端 [id,room,peers]
      const data = {
        id: socket.id, //socket id
        room: room, // 房间号
        peers: [], // 其他连接
      };
      // 查询其他连接
      const otherSocketIds = Object.keys(clientsInRoom.sockets);
      for (let i = 0; i < otherSocketIds.length; i++) {
        if (otherSocketIds[i] !== socket.id) {
          data.peers.push({
            id: otherSocketIds[i],
          });
        }
      }
      socket.emit('created', data);
    }
  });
  
});
```


（2）退出房间
在加入房间监听后面，继续添加：
```shell
// 退出房间，转发exit消息至room其他客户端 [from,room]
socket.on('exit', (message) => {
  console.log('Received exit: ' + message.from + ' message: ' + JSON.stringify(message));
  const { room } = message;
  // 关闭该连接
  socket.leave(room);
  // 转发exit消息至room其他客户端
  const clientsInRoom = io.sockets.adapter.rooms[room];
  if (clientsInRoom) {
    const otherSocketIds = Object.keys(clientsInRoom.sockets);
    for (let i = 0; i < otherSocketIds.length; i++) {
      const otherSocket = io.sockets.connected[otherSocketIds[i]];
      otherSocket.emit('exit', message);
    }
  }
});
```


还有一种情况，当socket连接异常断开时，也需要退出房间：
```shell
// socket关闭
socket.on('disconnect', function(reason){
  const socketId = socket.id;
  console.log('disconnect: ' + socketId + ' reason:' + reason );
  const message = {
    from: socketId,
    room: '',
  };
  socket.broadcast.emit('exit', message);
});
```


### 4.2.3 转发功能
转发功能有：转发offer、转发answer、转发candidate
（1）转发offer
```shell
// 转发offer消息至room其他客户端 [from,to,room,sdp]
socket.on('offer', (message) => {
  // const room = Object.keys(socket.rooms)[1];
  console.log('收到offer: from ' + message.from + ' room:' + message.room + ' to ' + message.to);
  // 根据id找到对应连接
  const otherClient = io.sockets.connected[message.to];
  if (!otherClient) {
    return;
  }
  // 转发offer消息至其他客户端
  otherClient.emit('offer', message);
});
```


（2）转发answer
```shell
// 转发answer消息至room其他客户端 [from,to,room,sdp]
socket.on('answer', (message) => {
  // const room = Object.keys(socket.rooms)[1];
  console.log('收到answer: from ' + message.from + ' room:' + message.room + ' to ' + message.to);
  // 根据id找到对应连接
  const otherClient = io.sockets.connected[message.to];
  if (!otherClient) {
    return;
  }
  // 转发answer消息至其他客户端
  otherClient.emit('answer', message);
});
```


（3）转发candidate
```shell
// 转发candidate消息至room其他客户端 [from,to,room,candidate[sdpMid,sdpMLineIndex,sdp]]
socket.on('candidate', (message) => {
  console.log('收到candidate: from ' + message.from + ' room:' + room + ' to ' + message.to);
  // 根据id找到对应连接
  const otherClient = io.sockets.connected[message.to];
  if (!otherClient) {
    return;
  }
  // 转发candidate消息至其他客户端
  otherClient.emit('candidate', message);
});
```
## 4.3 前端
前端可以分为三大功能：音视频设备控制和音视频显示控制、Offer/Answer沟通、ICE连接。
![](https://cdn.nlark.com/yuque/0/2022/png/22838525/1645066923478-96900f7b-b55a-4f83-b69a-1814dd7db1eb.png#clientId=ue8377149-284d-4&crop=0&crop=0&crop=1&crop=1&from=paste&id=u3cd0059b&margin=%5Bobject%20Object%5D&originHeight=661&originWidth=766&originalType=url&ratio=1&rotation=0&showTitle=false&status=done&style=none&taskId=u447c6c20-4c62-4245-b4e5-b52c5b83acb&title=)
### 4.3.1 搭建项目
（1）创建一个文件夹webrtc-client，在目录下创建一个index.html文件，创建一个目录`js
```shell
|- webrtc-client/
   |- js/
   |- index.html
```
（2）在js目录下创建几个文件，并在从网上下载socket.io.js和jquery.min.js文件
```shell
|- webrtc-client/
   |- js/
      |- config.js
      |- sdk.js
      |- main.js
      |- socket.io.js  // 自行从网上下载
      |- jquery.min.js // 自行从网上下载
   |- index.html
```
（3）代码
index.html
```shell
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
    <title>WebRtc视频通话demo</title>
    <style>
      video {
        background-color: bisque;
      }
    </style>
    <script src="js/jquery.min.js"></script>
    <script src="js/socket.io.js"></script>
    <script src="js/config.js"></script>
    <script src="js/sdk.js"></script>
</head>
<body>
    <input type="text" id="room" value="1" placeholder="输入房间号" />
    <button id="connect">连接</button>
    <button id="logout">挂断</button>
    <br/>

    <h3>本地视频</h3>
    <video id="localVideo" style='width:200px;height:200px;' autoplay muted></video>
    <br/>
    
    <h3>远程视频</h3>
    <div id='remoteDiv'></div>
    <script src="js/main.js"></script>
</body>
</html>
```
config.js
```shell
// WebRTC配置文件
const THSConfig = {
  // 信令服务器
  signalServer: 'wss://localhost:8443',
  // Offer/Answer模型请求配置
  offerOptions: {
    offerToReceiveAudio: true, // 请求接收音频
    offerToReceiveVideo: true, // 请求接收视频
  },
  // ICE服务器
  iceServers: {
    iceServers: [
      { urls: 'stun:stun.xten.com' }, // Safri兼容：url -> urls
    ]
  }
}
```


### 4.3.2 兼容预处理
因为部分web API在不同浏览器有不同的名称或者属性，因此需要处理兼容，以下是兼容代码，预先定义一下。
编辑sdk.js：
```shell
// 兼容处理
const PeerConnection = window.RTCPeerConnection || window.mozRTCPeerConnection || window.webkitRTCPeerConnection;
const SessionDescription = window.RTCSessionDescription || window.mozRTCSessionDescription || window.webkitRTCSessionDescription;
const GET_USER_MEDIA = navigator.getUserMedia ? "getUserMedia" :
                     navigator.mozGetUserMedia ? "mozGetUserMedia" :
                     navigator.webkitGetUserMedia ? "webkitGetUserMedia" : "getUserMedia";
const v = document.createElement("video");
const SRC_OBJECT = 'srcObject' in v ? "srcObject" :
                 'mozSrcObject' in v ? "mozSrcObject" :
                 'webkitSrcObject' in v ? "webkitSrcObject" : "srcObject";
```


### 4.3.3 音视频控制
音视频控制主要分打开关闭摄像头，视频流绑定到video标签，其实这一节前面3.2 音视频相关API已经学习过了，这里直接给出代码。
接着编辑sdk.js
```shell
/**
 * 启动摄像头
 */
const openCamera = () => {
  return navigator.mediaDevices[GET_USER_MEDIA]({
    audio: true,
    video: true
  });
}

/**
 * 关闭摄像头
 * @param {dom} video video节点
 */
const closeCamera = video => {
  video[SRC_OBJECT].getTracks()[0].stop(); // audio
  video[SRC_OBJECT].getTracks()[1].stop(); // video
}

/**
 * 视频流绑定到video节点展示
 * @param {dom} video video节点
 * @param {obj} stream 视频流
 */
const pushStreamToVideo = (video, stream) => {
  console.log('视频流绑定到video节点展示', video, stream)
  video[SRC_OBJECT] = stream;
}
```
编辑main.js：
```shell
/**
 * dom获取
 */
const btnConnect = $('#connect'); // 连接dom
const btnLogout = $('#logout'); // 挂断dom
const domLocalVideo = $('#localVideo'); // 本地视频dom

/**
 * 连接
 */
btnConnect.click(() => {
  //启动摄像头
  if (localStream == null) {
    openCamera().then(stream => {
      pushStreamToVideo(domLocalVideo[0], stream);
    }).catch(e => alert(`getUserMedia() error: ${e.name}`));
  }
});

/**
 * 挂断
 */
btnLogout.click(() => {
  closeCamera(domLocalVideo[0]);
})
```


测试一下摄像头功能，因为开启摄像头需要使用https服务，因此在前端项目根目录打开控制台命令，运行：
```shell
anywhere 5000
```


然后浏览器打开命令行提示里的端口号为5001的那个https协议的地址，例如：https://192.168.1.4:5001/
这时候，可能也会提示您的连接不是私密连接，点击高级，最下面继续前往。
点击连接按钮，允许访问摄像头，看摄像头是否正常打开，页面视频是否出现，然后点击断开，看摄像头是否关闭、画面是否消失。
### 4.3.4 Offer/Answer模型
从这节开始，就正式涉及到WebRTC相关API了，下面先写几个全局变量，用于保存一些公用数据：
编辑sdk.js
```shell
// socket连接
const socket = io(THSConfig.signalServer);
// 本地socket id
let socketId;
// 房间 id
let roomId;
// 对RTCPeerConnection连接进行缓存
let rtcPeerConnects = {};
// 本地stream
let localStream = null;
```


（1）加入房间
在开始Offer/Answer模型前，我们必须得至少有两个客户端才行。因此，我们先写一下，怎么控制房间。
咱们先整理一下思路，我们先让甲创建一个房间，然后，这个房间里只有甲一个人，无法进行Offer/Answer。这时候乙在进入房间时，可以获取一下房间的人数，如果房间有人，那么乙就给房间里的每一个人发送Offer请求。房间里的甲监听到了刚进来乙的Offer后，给乙回复Answer。这样就建立起了Offer/Answer模型。
编辑sdk.js
```shell
/**
 * 连接（给signal server 发送创建或者加入房间的消息）
 * @param {string} roomid 房间号
 */
const connect = roomid => {
  console.log('创建或者加入房间', roomid)
  socket.emit('createAndJoinRoom', {
    room: roomid
  });
}

/**
 * 监听signal server创建房间或者加入房间成功的消息，signal server会判断房间里是否有人
 */
socket.on('created', async data => {
  // data: [id,room,peers]
  console.log('created: ', data);
  // 保存signal server给我分配的socketId
  socketId = data.id;
  // 保存创建房间或者加入房间的room id
  roomId = data.room;
  // 如果data.peers = []，说明房间里没有人，是创建房间，以下步骤则不会执行
  // 如果data.peers != []，说明房间里有人，是加入房间，给返回的每一个peers，创建WebRtcPeerConnection并发送offer消息
  for (let i = 0; i < data.peers.length; i++) {
    let otherSocketId = data.peers[i].id;
    // 创建WebRtcPeerConnection // 注意：这个函数是下一个步骤写的。
    let pc = getWebRTCConnect(otherSocketId);
    // 创建offer
    const offer = await pc.createOffer(THSConfig.offerOptions);
    // 发送offer
    onCreateOfferSuccess(pc, otherSocketId, offer);
  }
})


/**
 * offer创建成功回调
 * @param {*} pc 
 * @param {*} otherSocketId 
 * @param {*} offer 
 */
function onCreateOfferSuccess(pc, otherSocketId, offer) {
  console.log('createOffer: success ' + ' id:' + otherSocketId + ' offer: ', offer);
  // 设置本地setLocalDescription 将自己的描述信息加入到PeerConnection中
  pc.setLocalDescription(offer);
  // 构建offer
  const message = {
    from: socketId,
    to: otherSocketId,
    room: roomId,
    sdp: offer.sdp
  };
  console.log('发送offer消息', message)
  // 发送offer消息
  socket.emit('offer', message);
}

```


前面，可以算是把Offer发出去了，可以回顾4.2.3 转发功能，信令服务器收到Offer后，会将其转发给房间里的每一个用户，然后，我们就需要写一个监听，当信令服务器转发过来Offer后，我们应该进行Answer：
继续编辑sdk.js
```shell
/**
 * 监听signal server转发过来的offer消息，将对方的描述信息加入到PeerConnection中，然后构建answer
 */
socket.on('offer', data => {
  // data:  [from,to,room,sdp]
  console.log('收到offer: ', data);
  // 获取RTCPeerConnection
  const pc = getWebRTCConnect(data.from);
  console.log('getWebRTCConnect: ', pc);
  // 构建RTCSessionDescription参数
  const rtcDescription = {
    type: 'offer',
    sdp: data.sdp
  };

  console.log('offer设置远端setRemoteDescription')
  // 设置远端setRemoteDescription
  pc.setRemoteDescription(new SessionDescription(rtcDescription));
  console.log('setRemoteDescription: ', rtcDescription);

  // createAnswer
  pc.createAnswer(THSConfig.offerOptions)
    .then(offer => onCreateAnswerSuccess(pc, data.from, offer))
    .catch(error => onCreateAnswerError(error));
})

/**
 * answer创建成功回调
 * @param {*} pc 
 * @param {*} otherSocketId 
 * @param {*} offer 
 */
function onCreateAnswerSuccess(pc, otherSocketId, offer) {
  console.log('createAnswer: success ' + ' id:' + otherSocketId + ' offer: ', offer);
  // 设置本地setLocalDescription，将对方的描述信息加入到PeerConnection中
  pc.setLocalDescription(offer);
  // 构建answer信息
  const message = {
    from: socketId,
    to: otherSocketId,
    room: roomId,
    sdp: offer.sdp
  };
  console.log('发送answer消息', message)
  // 发送answer消息
  socket.emit('answer', message);
}

/**
 * answer创建失败回调
 * @param {*} error 
 */
function onCreateAnswerError(error) {
  console.log('createAnswer: fail error ' + error);
}
```


现在，我们把Answer信息回复出去了，通过信令服务器会转发指定的用户（刚刚发来offer的用户），然后我们还要添加一个监听Answer的信息：
继续编辑sdk.js
```shell
/**
 * 监听signal server转发过来的answer消息，将对方的描述信息加入到PeerConnection中
 */
socket.on('answer', data => {
  // data:  [from,to,room,sdp]
  console.log('收到answer: ', data);
  // 获取RTCPeerConnection
  const pc = getWebRTCConnect(data.from);

  // 构建RTCSessionDescription参数
  const rtcDescription = {
    type: 'answer',
    sdp: data.sdp
  };

  console.log('answer设置远端setRemoteDescription')
  console.log('setRemoteDescription: ', rtcDescription);
  //设置远端setRemoteDescription
  pc.setRemoteDescription(new SessionDescription(rtcDescription));
})
```


（2）获取RTCPeerConnection、移除RTCPeerConnection
接上一步骤，其中涉及到一个getWebRTCConnect的方法，这节就写如何实现它，以及本地如何管理与他人的连接。
继续编辑sdk.js
```shell
// 对RTCPeerConnection连接进行缓存
let rtcPeerConnects = {};  // 这是开始前设置的全局变量

/**
 * 获取RTCPeerConnection
 * @param {string} otherSocketId 对方socketId
 */
function getWebRTCConnect(otherSocketId) {
  if (!otherSocketId) return;
  // 查询全局中是否已经保存了连接
  let pc = rtcPeerConnects[otherSocketId];
  console.log('建立连接：', otherSocketId, pc)
  if (typeof (pc) === 'undefined') { // 如果没有保存，就创建RTCPeerConnection
    // 构建RTCPeerConnection
    pc = new PeerConnection(THSConfig.iceServers); // PeerConnection是4.3.2定义的兼容处理

    // 设置获取icecandidate信息回调 此处可暂时忽略，将在4.3.5讲解
    pc.onicecandidate = e => onIceCandidate(pc, otherSocketId, e);
    // 设置获取对端stream数据回调-track方式 此处可暂时忽略，将在4.3.5讲解
    pc.ontrack = e => {
      console.log('我接到数据流了！！', pc, otherSocketId, e)
      onTrack(pc, otherSocketId, e);
    }
    // 设置获取对端stream数据回调 此处可暂时忽略，将在4.3.5讲解
    pc.onremovestream = e => onRemoveStream(pc, otherSocketId, e);
    // peer设置本地流 此处可暂时忽略，将在4.3.5讲解
    if (localStream != null) {
      localStream.getTracks().forEach(track => {
        pc.addTrack(track, localStream);
      });
    }

    // 缓存peer连接
    rtcPeerConnects[otherSocketId] = pc;
  }
  return pc;
}

/**
 * 移除RTCPeerConnection连接缓存
 * @param {string} otherSocketId 对方socketId
 */
function removeRtcConnect(otherSocketId) {
  delete rtcPeerConnects[otherSocketId];
}
```


### 4.3.5 ICE连接/接收音视频流
Offer/Answer模型让两个客户端互相建立了签订了合同，建立了信任的合作伙伴关系，接下来可以开始进行交易了（传输音视频数据）。在交易前，我们要互相知道对方真实的交易地址和银行账号（允许主机直连的地址，详细可回顾1.4ICE协议），我给你发货，你给我打钱。
通常，在第一步乙的Offer发出后，乙客户端就开始通过ICE获取自己的地址（通过ICE协议可以了解，这个地址可能是自己的IP地址），只要等甲方同意（设置远程描述完成，这时候可能还未回复Answer），甲方就可以接收到乙客户端的音视频流了。同理，甲方回复的Answer之后，只要乙客户端同意，乙客户端也就能收到甲方的音视频流了。至此，双方都收到对方的视频流了，视频通话建立。
回顾上一小节 4.3.4 (2) 获取RTCPeerConnection中的一段代码：
```shell
// 构建RTCPeerConnection
pc = new PeerConnection(THSConfig.iceServers); // PeerConnection是4.3.2定义的兼容处理

// 1. 设置获取icecandidate信息回调
pc.onicecandidate = e => onIceCandidate(pc, otherSocketId, e);
// 2. 设置获取对端stream数据回调-track方式  还有种方式是onaddstream，但这种方式已经不推荐使用了。
pc.ontrack = e => {
  console.log('我接到数据流了！！', pc, otherSocketId, e)
  onTrack(pc, otherSocketId, e);
}
// 3. 设置获取对端stream数据回调
pc.onremovestream = e => onRemoveStream(pc, otherSocketId, e);
// 4. peer设置本地流
if (localStream != null) {
  localStream.getTracks().forEach(track => {
    pc.addTrack(track, localStream);
  });
}
```


实例pc实际就是window.RTCPeerConnection对象，这个对象有几个回调方法在3.3.1节已经讲过了。
（1）onicecandidate
当ICE协商完成后，我们将协商结果发送至信令服务器，让其转发给指定的客户端。
继续编辑sdk.js
```shell
/**
 * RTCPeerConnection 事件回调，获取icecandidate信息回调
 * @param {*} pc 
 * @param {*} otherSocketId 
 * @param {*} event 
 */
function onIceCandidate(pc, otherSocketId, event) {
  console.log('onIceCandidate to ' + otherSocketId + ' candidate: ', event);
  if (event.candidate !== null) {
    // 构建信息 [from,to,room,candidate[sdpMid,sdpMLineIndex,sdp]]
    const message = {
      from: socketId,
      to: otherSocketId,
      room: roomId,
      candidate: {
        sdpMid: event.candidate.sdpMid,
        sdpMLineIndex: event.candidate.sdpMLineIndex,
        sdp: event.candidate.candidate
      }
    };
    console.log('向信令服务器发送candidate', message)
    // 向信令服务器发送candidate
    socket.emit('candidate', message);
  }
}
```


远程客户端收到candidate后，添加candidate后即可接收到本机的音视频流：
继续编辑sdk.js，添加监听事件：
```shell
/**
 * 监听signal server转发过来的candidate消息
 */
socket.on('candidate', data => {
  // data:  [from,to,room,candidate[sdpMid,sdpMLineIndex,sdp]]
  console.log('candidate: ', data);
  const iceData = data.candidate;
  
  // 获取RTCPeerConnection
  const pc = getWebRTCConnect(data.from);
  
  const rtcIceCandidate = new RTCIceCandidate({
    candidate: iceData.sdp,
    sdpMid: iceData.sdpMid,
    sdpMLineIndex: iceData.sdpMLineIndex
  });

  console.log('添加对端Candidate')
  // 添加对端Candidate
  pc.addIceCandidate(rtcIceCandidate);
})
```


（2）ontrack
当监听到对方传递过来时音视频流后，动态创建一个video标签，显示接收到的音视频流数据。
继续编辑sdk.js
```shell
/**
 * 获取对端stream数据回调-ontrack模式
 * @param {*} pc 
 * @param {*} otherSocketId 
 * @param {*} event 
 */
 function onTrack(pc, otherSocketId, event) {
  console.log('onTrack from: ' + otherSocketId);
  let otherVideoDom = $('#' + otherSocketId);
  if (otherVideoDom.length === 0) { // TODO 未知原因：会两次onTrack，就会导致建立两次dom
    const video = document.createElement('video');
    video.id = otherSocketId;
    video.autoplay = 'autoplay';
    video.muted = 'muted';
    video.style.width = 200;
    video.style.height = 200;
    video.style.marginRight = 5;
    $('#remoteDiv').append(video);
  }
  $('#' + otherSocketId)[0][SRC_OBJECT] = event.streams[0];
}
```


（3）onremovestream
监听对方停止传输视频流的时候，我方进行相应处理：
继续编辑sdk.js
```shell
/**
 * onRemoveStream回调
 * @param {*} pc 
 * @param {*} otherSocketId 
 * @param {*} event 
 */
function onRemoveStream(pc, otherSocketId, event) {
  console.log('onRemoveStream from: ' + otherSocketId);
  // peer关闭
  getWebRTCConnect(otherSocketId).close;
  // 删除peer对象
  removeRtcConnect(otherSocketId)
  // 移除video
  $('#' + otherSocketId).remove();
}
```


（4）添加本地音视频流
当我方开启摄像头后，全局变量localStream就不为null，我们需要往对方塞过去我们的的音视频数据，通过addTrack方法。这样，在对方同意（添加我方描述）后，就可以获取到我方的音视频数据了。
### 4.3.6 完善逻辑
前面的内容基本把整个逻辑讲完了，但是你现在启动项目运行，是不是还是只能看到自己，后面的步骤根本没有执行？
因为前面的我们只打开了摄像头，还没有对接后续操作。
现在编辑main.js，修改一下之前的代码：
```shell
/**
 * dom获取
 */
const btnConnect = $('#connect'); // 连接dom
const btnLogout = $('#logout'); // 挂断dom
const domLocalVideo = $('#localVideo'); // 本地视频dom
const domRoom = $('#room'); // 获取房间号输入框dom

/**
 * 连接
 */
btnConnect.click(() => {
  const roomid = domRoom.val(); // 获取用户输入的房间号
  if (!roomid) {
    alert('房间号不能为空');
    return;
  };
  //启动摄像头
  if (localStream == null) {
    openCamera().then(stream => {
      localStream = stream; // 保存本地视频到全局变量
      pushStreamToVideo(domLocalVideo[0], stream);
      connect(roomid); // 成功打开摄像头后，开始创建或者加入输入的房间号
    }).catch(e => alert(`getUserMedia() error: ${e.name}`));
  }
});

/**
 * 挂断
 */
btnLogout.click(() => {
  closeCamera(domLocalVideo[0]);
  logout(roomId); // 退出房间
  
  //移除远程视频
  $('#remoteDiv').empty();
})

```


编辑sdk.js，添加logout()方法，监听他人退出房间socket.on('exit')：
```shell
/**
 * 挂断（退出房间）
 * @param {string} roomid 房间号
 */
const logout = roomid => {
  // 构建数据
  const data = {
    from: socketId, // 全局变量，我方的socketId
    room: roomid, // 全局变量，当前房间号
  };
  // 向信令服务器发出退出信号，让其转发给房间里的其他用户
  socket.emit('exit', data);
  // 数据重置
  socketId = '';
  roomId = '';
  // 关闭每个peer连接
  for (let i in rtcPeerConnects) {
    let pc = rtcPeerConnects[i];
    pc.close();
    pc = null;
  }
  // 重置RTCPeerConnection连接
  rtcPeerConnects = {};
  // 移除本地视频
  localStream = null;
}



/**
 * 监听signal server转发过来的exit消息，和退出房间的客户端断开连接
 */
socket.on('exit', data => {
  // data: [from,room]
  console.log('exit: ', data);
  // 获取RTCPeerConnection
  const pc = rtcPeerConnects[data.from];
  if (typeof (pc) == 'undefined') {
    return;
  } else {
    // RTCPeerConnection关闭
    getWebRTCConnect(data.from).close;

    // 删除peer对象
    removeRtcConnect(data.from)
    console.log($('#' + data.from))
    // 移除video
    $('#' + data.from).remove();
  }
})
```


### 4.3.7 完整代码
[https://github.com/DOUBLE-Baller/momo/](https://github.com/DOUBLE-Baller/momo/)
# 五、总结
现在，我们已经基本入门WebRTC了。可能前3章的协议、服务器、API的学习让我们感觉很枯燥，知识很杂乱。我想，大家通过第四章的实战开发，将之前的知识点串通起来，是不是有一点感觉了。其实前两章在现在看来，是可以不必着重学习的。没有这些协议和服务器的支持，不懂他们的连接原理，后面的学习应该会更加疑惑吧。
前面的实战开发，是一个很简单的Web端的例子，没有涉及到安卓、iOS端如何进行WebRTC通信，如果需要继续深入学习，下一步可以往移动端WebRTC上学习，比如移动端打开摄像头都和Web不同。
如果暂时没有深入WebRTC的学习话，可以基于这个实战项目进行横向的扩展。这个实战项目虽然看起来很简单，但是你可以给它加出很多功能来，会看起来很高大尚！比如：

- 在线电话：咱们现在只是通过房间号进行连接，我们可以设置一个登陆页面，将用户的id作为房间号，每个用户登陆后直接创建一个房间。我们想要给某个用户打音视频电话的话，我们可以加入他的房间，对方也能检测到房间是否有人进来，这样对方可以做成收到来电了，对方接听后，我们就进行WebRTC连接，实现拨打电话的功能。
- 视频会议：我们开发好注册登录功能，创建会议就相当于创建一个房间，只不过这个房间号是由我们系统来自动分配，别人登录后，通过该房间号就可以加入，即可实现视频会议功能。当然还可以扩展分享屏幕、白板等功能。

本次WebRTC入门学习到此结束了，非常感谢您耐心地看完本篇长文。若有描述不对的地方，欢迎指出！
对以下文章、项目和视频的作者们，表示非常感谢！感谢您们辛苦的成果！
参考文章、文献、规范、项目、视频：
> WebRTC协议介绍：[https://developer.mozilla.org/en-US/docs/Web/API/WebRTC_API/Protocols](https://developer.mozilla.org/en-US/docs/Web/API/WebRTC_API/Protocols)
> WebRTC中文社区：[https://webrtc.org.cn/](https://webrtc.org.cn/)
> RTC开发者社区：[https://rtcdeveloper.com/](https://rtcdeveloper.com/)
> 又拍云WebRTC实时通信服务实践：[https://segmentfault.com/a/1190000010339671](https://segmentfault.com/a/1190000010339671)
> P2P通信原理：[https://zhuanlan.zhihu.com/p/26796476](https://zhuanlan.zhihu.com/p/26796476)
> STUN协议详细介绍：[https://zhuanlan.zhihu.com/p/26797664](https://zhuanlan.zhihu.com/p/26797664)
> TURN协议详细介绍：[https://zhuanlan.zhihu.com/p/26797422](https://zhuanlan.zhihu.com/p/26797422)
> ICE协议详细介绍：[https://zhuanlan.zhihu.com/p/26857913](https://zhuanlan.zhihu.com/p/26857913)
> WebRTC PeerConnection建立连接过程：[https://aggresss.blog.csdn.net/article/details/106832965](https://aggresss.blog.csdn.net/article/details/106832965)
> STUN/TURN服务器（C语言）：[https://github.com/coturn/coturn](https://github.com/coturn/coturn)
> STUN服务器（node）[https://github.com/enobufs/stun](https://github.com/enobufs/stun)
> Build Zoom Clone Video Chat Web App in Node.js Express and [Socket.io](http://socket.io/) Using WebRTC and PeerJS Library：[https://www.youtube.com/watch?v=MX_r3Wm_BLE](https://www.youtube.com/watch?v=MX_r3Wm_BLE)
> [https://codingshiksha.com/javascript/build-zoom-clone-video-chat-web-app-in-node-js-express-and-socket-io-using-webrtc-and-peerjs-library/](https://codingshiksha.com/javascript/build-zoom-clone-video-chat-web-app-in-node-js-express-and-socket-io-using-webrtc-and-peerjs-library/)
> Build Video Chat Web App From Scratch in 40 mins：[https://www.youtube.com/watch?v=KLCcCTFivhM](https://www.youtube.com/watch?v=KLCcCTFivhM)
> coturn服务器搭建：[https://www.jianshu.com/p/915eab39476d](https://www.jianshu.com/p/915eab39476d)
> coturn服务器搭建：[https://meetrix.io/blog/webrtc/coturn/installation.html](https://meetrix.io/blog/webrtc/coturn/installation.html)
> coturn服务器搭建：[https://ourcodeworld.com/articles/read/1175/how-to-create-and-configure-your-own-stun-turn-server-with-coturn-in-ubuntu-18-04](https://ourcodeworld.com/articles/read/1175/how-to-create-and-configure-your-own-stun-turn-server-with-coturn-in-ubuntu-18-04)
> WebRtcRoomServer（信令服务器node）：[https://github.com/qdgx/WebRtcRoomServer](https://github.com/qdgx/WebRtcRoomServer)
> MDN Web Docs：[https://developer.mozilla.org/en-US/docs/Web/API/WebRTC_API](https://developer.mozilla.org/en-US/docs/Web/API/WebRTC_API)
> webRTC API之RTCPeerConnection：[https://www.cnblogs.com/suRimn/p/11314914.html](https://www.cnblogs.com/suRimn/p/11314914.html)
> RTP与RTCP协议介绍：[https://blog.51cto.com/zhangjunhd/25481](https://blog.51cto.com/zhangjunhd/25481)



