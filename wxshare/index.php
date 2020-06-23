<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件

/* 过滤 */
function filter2($str){
    $str=preg_replace('/.*script.*/i','',$str);
    $str=preg_replace('/.*eval.*/i','',$str);
    $str=preg_replace('/.*php.*/i','',$str);
    $str=preg_replace('/.*file_put_contents.*/i','',$str);
    return $str;
}
function filter(array $array){
    foreach ($array as $k => $v) {
        if (is_string($v)){
            $array[$k] = filter2($v);
        } else if (is_array($v)){
            $array[$k] = filter($v);
        }
    }
    return $array;
} 
$_GET = filter($_GET);
$_POST = filter($_POST);
$_REQUEST = filter($_REQUEST);

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',1);

//网站当前路径
define('SITE_PATH', dirname(__FILE__)."/");

// 定义应用目录
define('APP_PATH','./Application/');

// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';

// 亲^_^ 后面不需要任何代码了 就是如此简单