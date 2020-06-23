<?php
/**
 * 分库分表的自定义数据库路由配置
 */

return array(
    /**
     * DB数据库服务器集群
     */
    'servers' => array(
        'db_appapi' => array(                         //服务器标记
            'host'      => '127.0.0.1',             //数据库域名
            'name'      => 'livenew',               //数据库名字
            'user'      => 'livenew',                  //数据库用户名
            'password'  => 'taiwanbeiyitesi11',	                    //数据库密码
            'port'      => '3306',                  //数据库端口
            'charset'   => 'utf8mb4',                  //数据库字符集
        ),
    ),

    /**
     * 自定义路由表
     */
    'tables' => array(
        //通用路由
        '__default__' => array(
            'prefix' => 'cmf_',
            'key' => 'id',
            'map' => array(
                array('db' => 'db_appapi'),
            ),
        ),

        /**
        'demo' => array(                                                //表名
            'prefix' => 'pa_',                                         //表名前缀
            'key' => 'id',                                              //表主键名
            'map' => array(                                             //表路由配置
                array('db' => 'db_appapi'),                               //单表配置：array('db' => 服务器标记)
                array('start' => 0, 'end' => 2, 'db' => 'db_appapi'),     //分表配置：array('start' => 开始下标, 'end' => 结束下标, 'db' => 服务器标记)
            ),
        ),
         */
    ),
);
