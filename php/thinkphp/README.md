# ThinkPHP 5.0（FastAdmin 团队长期维护）

## 维护原因

FastAdmin 致力于服务开发者，为开发者节省时间，为了 FastAdmin 开源项目持续发展下去。

## 参与开源

[贡献代码](https://doc.fastadmin.net/doc/contributing.html)


## 使用方法

- 本框架已经应用在 FastAdmin 后台框架中，在 FastAdmin 项目中使用 `composer update topthink/framework -vvv` 命令即可更新。

- 非 FastAdmin 的 ThinkPHP5.0 项目使用此仓库，请修改 `composer.json` 添加以下配置，在执行`composer update topthink/framework -vvv` 命令，具体可以参考 FastAdmin 项目目录下的 `composer.json` 文件内容。
    ```
    "repositories": [
        {
            "type": "git",
            "url": "https://gitee.com/fastadminnet/framework.git"
        }
    ]
    ```

## 环境要求

php 7.1+



## ThinkPHP 介绍

ThinkPHP5 在保持快速开发和大道至简的核心理念不变的同时，优化核心，减少依赖，基于全新的架构思想和命名空间实现，是 ThinkPHP 突破原有框架思路的颠覆之作，其主要特性包括：

 + 基于命名空间和众多PHP新特性
 + 核心功能组件化
 + 强化路由功能
 + 更灵活的控制器
 + 重构的模型和数据库类
 + 配置文件可分离
 + 重写的自动验证和完成
 + 简化扩展机制
 + API支持完善
 + 改进的Log类
 + 命令行访问支持
 + REST支持
 + 引导文件支持
 + 方便的自动生成定义
 + 真正惰性加载
 + 分布式环境支持
 + 支持Composer
 + 支持MongoDb

详细开发文档参考 [ThinkPHP5完全开发手册](http://www.kancloud.cn/manual/thinkphp5) 以及[ThinkPHP5入门系列教程](http://www.kancloud.cn/special/thinkphp5_quickstart)

## 版权信息

ThinkPHP遵循Apache2开源协议发布，并提供免费使用。

本项目包含的第三方源码和二进制文件之版权信息另行标注。

版权所有Copyright © 2006-2022 by ThinkPHP (http://thinkphp.cn)

All rights reserved。

ThinkPHP® 商标和著作权所有者为上海顶想信息科技有限公司。

更多细节参阅 [LICENSE.txt](LICENSE.txt)
