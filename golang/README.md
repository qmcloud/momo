## golang微服务架构版本【未开源】

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
**分布式事务DTM**
```
分布式事务准备使用的是dtm.性能够用。
```
**K8S部署**
```
简单易用: 提供可视化的 Web UI，极大降低 Kubernetes 部署和管理门槛.
按需创建: 调用云平台 API，一键快速创建和部署 Kubernetes 集群
按需伸缩: 快速伸缩 Kubernetes 集群，优化资源使用效率
按需修补: 快速升级和修补 Kubernetes 集群.
离线部署: 支持完全离线下的 Kubernetes 集群部署
自我修复: 通过重建故障节点确保集群可用性
全栈监控: 提供从Pod、Node到集群的事件、监控、告警、和日志方案
Multi-AZ 支持: 将 Master 节点分布在不同的故障域上确保集群高可用
应用商店: 内置 Apps 应用商店
GPU 支持: 支持 GPU 节点，助力运行深度学习等应用.

```

### ==问题反馈==

**在使用中有任何问题，欢迎反馈给我们**

https://github.com/DOUBLE-Baller/momo/issues

