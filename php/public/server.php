<?php

define('APP_PATH', __DIR__ . '/../application/');

define('BIND_MODULE','index/Swoole');//后面是你的swolle文件位置

//加载composer autoload文件
require __DIR__ . '/vendor/autoload.php';

// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
