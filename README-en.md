### Live streaming source code, short video, live streaming sales, game accompaniment, imitation heart, hunting game, TT voice chat, beauty appointment, accompaniment system source code open black, appointment source code

----------------
[简体中文](./README.md) | English

<div align=center>
<img src="https://img.shields.io/badge/php-7.3-blue"/>
<img src="https://img.shields.io/badge/golang-1.13-blue"/>
<img src="https://img.shields.io/badge/gin-1.4.0-lightBlue"/>
<img src="https://img.shields.io/badge/vue-2.6.10-brightgreen"/>
<img src="https://img.shields.io/badge/element--ui-2.12.0-green"/>
<img src="https://img.shields.io/badge/gorm-1.9.12-red"/>
</div>

### Front: VUE + Android + IOS + Uniapp

### Microservices ：

- **Goim** ：the official website of Bilibili IM architecture
- **Streaming Media Server** ： A streaming media server developed by Golang, supporting RTMP/WebRTC/HLS/HTTP FLV/SRT/GB28181
- **webrtc** ： Meetecho is an excellent general-purpose WebRTC server (SFU).
- **MongoDB** ：A document based distributed database.
- **Redis**：In memory data structure storage used as a database, cache, and message broker.
- **kafka** ：queue group chat, private chat, message notification, etc.
- **Nginx** ：High performance load balancer, web server, and reverse proxy with HTTPS/Quiche and Brtoli support;
- **K8S+Docker**：A platform for building, deploying, and managing containerized applications.
- **admin panel**: php + vue + Element-UI  | golang + vue + Element-UI 
----------------

blog：https://blog.csdn.net/u012115197/article/details/106916635

Gitee：https://gitee.com/baoyalive/baoyalive.git （history code backup）

Demo ：[http://www.onionnews.cn/](http://www.onionnews.cn/)

----------------

### Technology Stack


## php【Open Source】

1. The web system provides page and interface logic.

2. REDIS service provides caching and storage of dynamic data.

3. MYSQL service provides storage for static data.

4. Video services provide live streaming, roadside streaming, transcoding, storage, on-demand, and support for Tencent Cloud, Alibaba Cloud, Qiniu, and other self built streaming media servers

5. Chat services provide live group chats, private chats, message notifications, etc.

6. Backend framework: ThinkPHP framework.
 
------------
## Golang microservice architecture version [not open source]

**Introduction to Microservices**

1. Easily obtain stability that supports millions of daily active services

2. Built in microservice governance capabilities such as cascading timeout control, current limiting, adaptive circuit breaker, and adaptive load shedding, without the need for configuration or additional code

3. Microservice governance middleware can be seamlessly integrated into other existing frameworks for use

4. Minimalist API description, one click generation of code for each end

5. Automatically verify the legality of client request parameters

6. A large number of microservice governance and concurrency toolkits

**Golang microservice architecture diagram**

![](https://github.com/DOUBLE-Baller/momo/blob/master/doc/doc.jpg?raw=true)

**Code directory description**

```
├── ergo
│   ├── app  
│   ├── backend 
│   ├── backendweb 
│   ├── script 
│   ├── .gitignore 
│   ├── LICENSE
```
**Gateway**

```

Nginx is used as the gateway, utilizing nginx's auth module to call the backend service for unified authentication. Internal authentication is not performed within the business, but if there is a significant amount of business funds involved, secondary authentication can be performed within the business.

In addition, many students think that nginx is not good at making gateways. The principle is basically the same, and they can replace it with apisix, kong, etc. on their own

```

**Development mode**

```

This project uses microservice development, with API (HTTP)+RPC (GRPC). The API acts as an aggregation service, and complex and other business calls are uniformly written in RPC. If some simple businesses are not dependent on other services, they can be directly written in the logic of the API

```

**Log**

```

Regarding logs, they will be collected using Filebeat and reported to Kafka. Logstash will synchronize the Kafka data source to Elasticsearch, and then analyze, process, and display it through Kibana.

```

**Monitoring**

```

The monitoring adopts Prometheus, which only requires configuration. You can refer to the configuration in the project here

```

**Link Tracking**

```

Default Jaeger and Zipkin support, just configure it, you can check the configuration

```

**Message queue**

```

Here, kq is used, which is a high-performance message queue based on Kafka

```

**Delay queue, scheduled tasks**

```

Delay queue and scheduled tasks. This project uses Asynq, a simple middleware developed by the Google team for Redis. Asynq also supports message queues. You can also replace the KQ message queue with Kafka

```

**Distributed Transaction DTM**

```

Distributed transactions use DTM, with a single node processing 10000 transactions per second, which is sufficient for regular flash sales.

```

**K8S deployment**

```

Easy to use: Provides a visual web UI that greatly reduces the barrier to deployment and management of Kubernetes

On demand creation: Call cloud platform APIs to quickly create and deploy Kubernetes clusters with just one click

On demand scaling: Quickly scale Kubernetes clusters to optimize resource utilization efficiency

On demand patching: Quickly upgrade and patch Kubernetes clusters

Offline deployment: Supports Kubernetes cluster deployment completely offline

Self repair: Ensuring cluster availability by rebuilding faulty nodes

Full stack monitoring: provides event, monitoring, alarm, and logging solutions from Pod, Node to cluster

Multi AZ support: Distribute Master nodes across different fault domains to ensure high availability of the cluster

App Store: Built in Apps App Store

GPU support: Supports GPU nodes to assist in running applications such as deep learning

```

### Business cooperation (UI design, custom development, system refactoring, agency promotion, etc.)

**WeChat**: BCFind5 

**QQ**: 407193275 

**TG**: @qmcloud
