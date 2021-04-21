## PHP 为什么 “慢” ？？？
### Laravel + Swoole 提速 30倍

 - 复用容器
 - 启用协程
 - 数据库连接池

 ----    

>  - 复用容器
>  熟悉 Laravel 的朋友都知道 IoC 容器是整个框架的核心，几乎所有 Laravel 提供的服务都被注册在 IoC 容器中。每当容器启动时，Laravel 就会将大部分服务注册到容器中来，有些服务还会去加载文件，比如配置、路由等，可以说启动容器是比较 “耗时” 的。我们再次观察上面的脚本，可以看到 request 回调的第一行就是创建 IoC 容器（$app），这也意味着每次在处理请求时都会创建一次容器，这样不仅重复执行了许多代码，还造成不小的 IO 开销，所以上述脚本显然不是最优的做法。那我们试试只创建一个容器，再让所有的请求都复用这个容器。我们可以在 worker 进程启动时（也就是 workerStart 回调中）创建并启动容器，这样在 request 回调中就能复用了。现在将 swoole.php 如下：

```php
<?php

require __DIR__.'/vendor/autoload.php';

use HuangYi\Shadowfax\Http\Request;
use HuangYi\Shadowfax\Http\Response;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request as IlluminateRequest;
use Swoole\Http\Server;

$server = new Server('127.0.0.1', 9501);

$server->set([
    'worker_num' => 1,
    'enable_coroutine' => false,
]);

$app = null;

$server->on('workerStart', function () use (&$app) {
    $app = require __DIR__.'/bootstrap/app.php';

    $app->instance('request', IlluminateRequest::create('http://localhost'));

    $app->make(Kernel::class)->bootstrap();
});

$server->on('request', function ($request, $response) use (&$app) {
    $kernel = $app->make(Kernel::class);

    $illuminateResponse = $kernel->handle(
        $illuminateRequest = Request::make($request)->toIlluminate()
    );

    Response::make($illuminateResponse)->send($response);

    $kernel->terminate($illuminateRequest, $illuminateResponse);
});

$server->start();
```
>  - 启用协程
>  协程是 Swoole 的最强武器，也是实现高并发的精髓所在。那么在 Laravel 中使用协程会有问题吗？首先启动 Swoole 的协程特性，将 enable_coroutine 设置为 true 即可，然后在 routes/web.php 里面添加两个路由：

```php
<?php

use Swoole\Coroutine;

app()->singleton('counter', function () {
    $counter = new stdClass;
    $counter->number = 0;

    return $counter;
});

Route::get('one', function () {
    app('counter')->number = 1;

    Coroutine::sleep(5);

    echo sprintf("one: %d\n", app('counter')->number);
});

Route::get('two', function () {
    app('counter')->number = 2;

    Coroutine::sleep(5);

    echo sprintf("two: %d\n", app('counter')->number);
});
```

> 上述代码首先在容器里面注册了一个 counter 单例，路由 one 将 counter 单例的 number 属性设置为 1，然后模拟协程被挂起 5 秒，恢复后打印出 number 属性的值。路由 two 也类似，只是将 number 属性设置为了 2。启动服务器后，我们先访问 one，然后立马访问 two（间隔不要超过 5 秒）。我们可以观察到 Console 输出的信息为：
```shell
one: 2
two: 2
```
>  - 数据库连接池
> 注意： 在协程环境下使用数据库如果不配合连接池，就会造成连接异常。当然，使用 Swoole 的 Channel 来创建连接池非常简单，但是如果直接在业务代码中使用连接池，程序员需要自行控制何时取何时回收，而且还不能使用 Laravel 的 Model 了，这点我是绝对不能接受的。还有一点，由于在业务代码中使用了 Swoole 的接口，这意味着你的程序必须运行在 Swoole 之上，再也无法切回 PHP-FPM 了。Shadowfax 做到了无感知的使用连接池，开发者依然像平时那样用 Model 来查询或者更新数据，唯一需要做的就是将程序中使用到的数据库连接名配置到 db_pools 当中即可。Shadowfax 是如何做到的呢？我们只需要搞清楚一点就能明白原理了，Laravel 中的数据库连接都是通过 Illuminate\Database\DatabaseManager::connection() 方法来获取的，我们可以继承这个类并改造 connection() 方法，如果取的是 db_pools 中配置的连接，那么就从对应的连接池中获取。最后使用这个改造后的类注覆盖原来的 db 服务即可。具体的实现就请阅读源码 。

```php
<?php

namespace HuangYi\Shadowfax\Laravel;

use Illuminate\Database\Connection;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Database\DatabaseManager as LaravelDatabaseManager;

class DatabaseManager extends LaravelDatabaseManager
{
    use HasConnectionPools;

    /**
     * The callback to be executed to reconnect to a database in pool.
     *
     * @var callable
     */
    protected $poolReconnector;

    /**
     * Create a new DatabaseManager instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  \Illuminate\Database\Connectors\ConnectionFactory  $factory
     * @param  array  $poolsConfig
     * @return void
     */
    public function __construct($app, ConnectionFactory $factory, array $poolsConfig = [])
    {
        parent::__construct($app, $factory);

        $this->poolsConfig = $poolsConfig;

        $this->poolReconnector = function ($connection) {
            $this->poolReconnect($connection);
        };
    }

    /**
     * Get a database connection instance.
     *
     * @param  string|null  $name
     * @return \Illuminate\Database\ConnectionInterface
     */
    public function connection($name = null)
    {
        $name = $name ?: $this->getDefaultConnection();

        if (! $this->isPoolConnection($name)) {
            return parent::connection($name);
        }

        if ($connection = $this->getConnectionFromContext($name)) {
            return $connection;
        }

        return $this->getConnectionFromPool($name);
    }

    /**
     * Resolve the connection.
     *
     * @param  string  $name
     * @return \Illuminate\Database\Connection
     */
    protected function resolveConnection($name)
    {
        [$database, $type] = $this->parseConnectionName($name);

        $connection = $this->configure($this->makeConnection($database), $type);

        $connection->setReconnector($this->poolReconnector);

        return $connection;
    }

    /**
     * Get the connection key in coroutine context.
     *
     * @param  string  $name
     * @return string
     */
    protected function getConnectionKeyInContext($name)
    {
        return 'db.connections.'.$name;
    }

    /**
     * Reconnect to the given database.
     *
     * @param  string|null  $name
     * @return \Illuminate\Database\Connection
     */
    public function reconnect($name = null)
    {
        $name = $name ?: $this->getDefaultConnection();

        if (! $this->isPoolConnection($name)) {
            return parent::reconnect($name);
        }
    }

    /**
     * Reconnect the connection in pool.
     *
     * @param  \Illuminate\Database\Connection  $connection
     * @return \Illuminate\Database\Connection
     */
    public function poolReconnect(Connection $connection)
    {
        $connection->disconnect();

        $fresh = $this->makeConnection($connection->getName());

        return $connection
            ->setPdo($fresh->getRawPdo())
            ->setReadPdo($fresh->getRawReadPdo());
    }
}
```

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