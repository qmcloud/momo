### Live source code, short video, live with goods, games to play, imitation than the heart, hunting, TT voice chat, beauty about to play, play with the system source code open black, about to play source code

----------------
<div align=center>
<img src="https://img.shields.io/badge/php-7.3-blue"/>
<img src="https://img.shields.io/badge/golang-1.13-blue"/>
<img src="https://img.shields.io/badge/gin-1.4.0-lightBlue"/>
<img src="https://img.shields.io/badge/vue-2.6.10-brightgreen"/>
<img src="https://img.shields.io/badge/element--ui-2.12.0-green"/>
<img src="https://img.shields.io/badge/gorm-1.9.12-red"/>
</div>

English | [简体中文](./README.md)

### Front-end: Vue Mobile Terminal: Android + iOS

### The micro service (Docker container) consists of:

- **goim** ：Bilibili station IM architecture:
- **livego** ：High-performance RTMP server based on Golang test model: Aliyun 32 core 64G exclusive server 30000 concurrent pull stream, CPU occupation rate is less than 50%!
- **webrtc** ：Janus Gateway: MeetEcho's excellent universal WebRTC server (SFU);
- **MongoDB** ：Distributed database based on documents built in cloud era;
- **Redis**：In-memory data structure storage, used as a database, cache, and message broker;
- **kafka** ：Queue group chat, private chat, message notification, etc.
- **Coturn** ：Open source projects for Turn and Stun Server;
- **Nginx** ：High performance load balancer, Web server and reverse proxies supported by HTTP3 / Quiche and Brtoli;
- **Docker**：A platform for building, deploying, and managing containerized applications.
- **Admin**: PHP (old business PHP backend) + GIN (API interface refactoring) + VUE + ELEMent-UI
----------------


**Contact us：**
![](https://img-blog.csdnimg.cn/20200623093238797.png)

----------------
WeChat：BCFind5 【Please note the good information】

Telegram:@BCFind5

----------------


[Background presentation address：](http://www.jinqianlive.com/admin) http://www.jinqianlive.com/admin

user ：test
pass： test

----------------
[Live short video](https://baoya.lanzous.com/imcL9e57tej) https://baoya.lanzous.com/imcL9e57tej

----------------
[Voice chat room](http://app.6sjs.com/wej8) http://app.6sjs.com/wej8 
  
----------------
IOS ：https://pan.baidu.com/s/18KaHu-39TMQLetb0m7XD0Q 提取码：v929

----------------

doc：http://www.jinqianlive.com/appapi/listAllApis.php?type=expand

----------------

**The front-end display**
![The front-end display](https://img-blog.csdnimg.cn/20200908194734911.jpg?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3UwMTIxMTUxOTc=,size_16,color_FFFFFF,t_70#pic_center)

**Backend interface**
![Backend interface](https://img-blog.csdnimg.cn/20200907180807339.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3UwMTIxMTUxOTc=,size_16,color_FFFFFF,t_70#pic_center)


----------------

### Technical structure


**System development language**
-  **PHP | golang video interactive system by the WEB system, REDIS service, MYSQL services, video services, chat, background management system, and regularly monitor, background management using PHP + golang language development, all services provide lateral extension.**
 
------------

### Environment set up


**Install golang** 
```bash
wget http://www.golangtc.com/static/go/go1.3.linux-amd64.tar.gz
```
```bash
tar -C /usr/local -zxvf  go1.3.linux-amd64.tar.gz 
```

```bash
vim /etc/profile	
export GOROOT=/usr/local/go
export PATH=$PATH:$GOROOT/bin
export GOPATH="$HOME/go
```
**success**

![](https://img-blog.csdnimg.cn/20200922210610471.png#pic_center)

**install etcd**
```bash
curl -L https://github.com/coreos/etcd/releases/download/v3.3.2/etcd-v3.3.2-linux-amd64.tar.gz -o etcd-v3.3.2-linux-amd64.tar.gz
```
```bash
tar -zxf etcd-v3.3.2-linux-amd64.tar.gz
```
```bash
mv etcd-v3.3.2-linux-amd64/etcd* /$GOPATH/bin
```
```bash
./etcd 
```
**success**

![](https://img-blog.csdnimg.cn/20200922213137699.png#pic_center)

**install Protobuf tools**

```bash
mkdir /www/go/live
```
```bash
cd /www/go/live
```
```bash
go mod init live
#go.mod file
```
**Go Micro RPC**
```bash
go get github.com/micro/go-micro
```
**install protoc**
```bash
from https://github.com/protocolbuffers/protobuf/releases down`new`version protoc ：
./configure
make && make install
```
```bash
protoc --version
```
![success](https://img-blog.csdnimg.cn/20200922221355356.png#pic_center)

**install protoc-gen-micro**
```bash
go get -u github.com/micro/protoc-gen-micro
```
**install protoc-gen-go**
```bash
go get -u github.com/golang/protobuf/protoc-gen-go

cp protoc-gen-* /usr/local/bin/

$GOPATH/bin copy /usr/local/bin 
```

![](https://img-blog.csdnimg.cn/20200922222247686.png#pic_center)

```golang
mkdir /www/go/live/proto
```
```proto3
syntax = "proto3";

service Live {
rpc Call(LiveRequest) returns (LiveResponse) {}
}

message LiveRequest {
string name = 1; 
}

message liveResponse {
string result = 1;
}
```
```bash
protoc auto code
protoc --proto_path=. --micro_out=. --go_out=. proto/live.proto
```
>：
![](https://img-blog.csdnimg.cn/20200922224029282.png#pic_center)

**Write the Go service implementation code live  make mian.go**
```golang
package main

import (
	"context"
	"fmt"
	"github.com/micro/go-micro"
	proto "live/proto"
)

type LiveServiceHandler struct{}

func (g *LiveServiceHandler)Call(ctx context.Context, req *proto.LiveRequest, rsp *proto.LiveResponse) error {
	rsp.Result = "github：https://github.com/DOUBLE-Baller/" + req.Name
	return nil
}

func main()  {
	service := micro.NewService(
		micro.Name("go.micro.api.Live"), //go.micro.api namespace
	)

	// init
	service.Init()

	// register
	proto.RegisterLiveHandler(service.Server(), new(LiveServiceHandler))

	// run
	if err := service.Run(); err != nil {
		fmt.Println(err)
	}
}
```
```golang
go run main.go
```
```bash
attention：add MICRO_REGISTRY=etcd used go run main.go --registry=etcd 
```

![](https://img-blog.csdnimg.cn/2020092223034366.png#pic_center)
failure
![](https://img-blog.csdnimg.cn/20200922225010494.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3UwMTIxMTUxOTc=,size_16,color_FFFFFF,t_70#pic_center)
```bash
attention：add go.mod last
replace google.golang.org/grpc => google.golang.org/grpc v1.26.0
```
![](https://img-blog.csdnimg.cn/20200923092941557.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3UwMTIxMTUxOTc=,size_16,color_FFFFFF,t_70#pic_center)


**use go micro provide HTTP API**
```bash
go get github.com/micro/micro/v2

finish， $GOPATH/bin make micro run，cp from /user/local/bin 
```
```bash
micro api --handler=rpc
```
```bash
default:8080 
```

![](https://img-blog.csdnimg.cn/20200922233014118.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3UwMTIxMTUxOTc=,size_16,color_FFFFFF,t_70#pic_center)

visit IP:8080  ![](https://img-blog.csdnimg.cn/20200922233410216.png#pic_center)

```bash
micro call go.micro.api.Live Live.Call '{"name": "momo"}'
```
![](https://img-blog.csdnimg.cn/20200923111240482.png#pic_center)


goim
==============
`Terry-Mao/goim` is a IM and push notification server cluster.

---------------------------------------
  * [Features](#features)
  * [Installing](#installing)
  * [Configurations](#configurations)
  * [Examples](#examples)
  * [Documents](#documents)
  * [More](#more)

---------------------------------------

## Features
 * Light weight
 * High performance
 * Pure Golang
 * Supports single push, multiple push, room push and broadcasting
 * Supports one key to multiple subscribers (Configurable maximum subscribers count)
 * Supports heartbeats (Application heartbeats, TCP, KeepAlive)
 * Supports authentication (Unauthenticated user can't subscribe)
 * Supports multiple protocols (WebSocket，TCP）
 * Scalable architecture (Unlimited dynamic job and logic modules)
 * Asynchronous push notification based on Kafka

## Installing
### Dependencies
```sh
$ yum -y install java-1.7.0-openjdk
```

### Install Kafka

Please follow the official quick start [here](http://kafka.apache.org/documentation.html#quickstart).

### Install Golang environment

Please follow the official quick start [here](https://golang.org/doc/install).

### Deploy goim
1.Download goim
```sh
$ yum install git
$ cd $GOPATH/src
$ git clone https://github.com/Terry-Mao/goim.git
$ cd $GOPATH/src/goim
$ go get ./...
```

2.Install router、logic、comet、job modules(You might need to change the configuration files based on your servers)
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

Everything is DONE!

### Run goim
You may need to change the log files location.
```sh
$ cd /$GOPATH/bin
$ nohup $GOPATH/bin/router -c $GOPATH/bin/router.conf 2>&1 > /data/logs/goim/panic-router.log &
$ nohup $GOPATH/bin/logic -c $GOPATH/bin/logic.conf 2>&1 > /data/logs/goim/panic-logic.log &
$ nohup $GOPATH/bin/comet -c $GOPATH/bin/comet.conf 2>&1 > /data/logs/goim/panic-comet.log &
$ nohup $GOPATH/bin/job -c $GOPATH/bin/job.conf 2>&1 > /data/logs/goim/panic-job.log &
```

If it fails, please check the logs for debugging.

### Testing

Check the push protocols here[push HTTP protocols](./docs/push.md)

## Configurations
TODO

## Examples
Websocket: [Websocket Client Demo](https://github.com/Terry-Mao/goim/tree/master/examples/javascript)

Android: [Android SDK](https://github.com/roamdy/goim-sdk)

iOS: [iOS](https://github.com/roamdy/goim-oc-sdk)

## Documents
[push HTTP protocols](./docs/en/push.md)

[Comet client protocols](./docs/en/proto.md)


==The problem of feedback==

**Please let us know if you have any problems during use. You can use the following contact information to communicate with us**




