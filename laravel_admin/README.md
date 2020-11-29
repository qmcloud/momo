## 何为 laravel-admin
是一个可以快速帮你构建后台管理的工具，它提供的页面组件和表单元素等功能，能帮助你使用很少的代码就实现功能完善的后台管理功能

**特性**

 - 内置用户和权限系统
 - model-grid支持快速构建数据表格
 - model-form支持快速构建数据表单
 - model-tree支持快速构建树状数据
 - 内置40+种form元素组件、以及支持扩展组件
 - 支持Laravel的多种模型关系
 - mysql、mongodb、pgsql等多数据库支持
 - 支持引入第三方前端库
 - 数据库和artisan命令行工具的web实现
 - 支持自定义图表
 - 多种常用web组件
 - 支持本地和oss文件上传

## 目录结构
```
app/Admin
├── Controllers
│   ├── ExampleController.php
│   └── HomeController.php
├── bootstrap.php
└── routes.php
```
#### 克隆项目

```
git clone https://github.com/DOUBLE-Baller/momo.git
```

#### 进入项目
```shell
cd momo
```

#### 安装功能包
```shell
composer install 
```

#### 复制.env文件
```shell
cp .env.example .env
```

#### 生成密钥
```shell
php artisan key:generate
```

#### 运行迁移
```shell
# 注意修改.env文件的数据库信息
php artisan migrate
```

#### 建立文件链接(如果报错请百度解决，都是小问题)
```shell
php artisan storage:link
```

#### 初始化数据库
```shell
php artisan db:seed-sql 
# 输入yes
```

> 后台地址：http://xxxx/admin 账户：admin，密码：admin

#### 创建后台控制器

```shell
# 以创建用户管理为例
php artisan admin:controller UserController
```

> 注意：控制器里面修改相应模型，变量，视图即可，视图请参考`auth`里面的代码

#### 使用说明

> 搜索和导出功能需自己完善

- 路由文件：app/Admin/routes.php
- 配置文件：config/admin.php
- 语言文件：resources/lang/zh-CN/admin.php
- 后台控制器路径：app/Admin/Controllers
- 后台视图路径：app/Admin/Views
- 视图请参考`auth`里面的代码
- 视图页面代码请参考adminlte2.4文件
- 资源文件：public/plugins
- 获取登录用户`Admin::user()`
- 获取登录用户ID`Admin::id()`
- 判断登录用户是否是某个角色(判断角色标识)`Admin::isAdmin('slug'['slug','slug'])`
- 视图获取登录用户`admin('user')`
- 视图获取登录用户ID`admin('id')`
- 视图判断登录用户是否是某个角色(判断角色标识)`admin('isAdmin','slug'['slug','slug'])`