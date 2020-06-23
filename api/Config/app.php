<?php
/**
 * 请在下面放置任何您需要的应用配置
 */

return array(

    /**
     * 应用接口层的统一参数
     */
    'apiCommonRules' => array(
        //'sign' => array('name' => 'sign', 'require' => true),
    ),
    'REDIS_HOST' => "127.0.0.1",
    'REDIS_AUTH' => "taiwanbeiyitesi11",
    'REDIS_PORT' => "6379",
    
    'sign_key' => '76576076c1f5f657b634e966c8836a06',
		
	'uptype'=>1,//上传方式：1表示 七牛，2表示 本地
		/**
     * 七牛相关配置
     */
    'Qiniu' =>  array(
        //统一的key
        'accessKey' => 'bUqi_epArHdCHHmQei7RLOhfyAUwL_A_gkLOD2Xx',
        'secretKey' => 'OB1b2SdHseZ4x9WcuQKXWdcar1Pw1cjZAT1Nr5D6',
        //自定义配置的空间
        'space_bucket' => 'twbystlive',
        'space_host' => 'http://qiniu.mo78s.cn',
    ),
		
		 /**
     * 本地上传
     */
    'UCloudEngine' => 'local',

    /**
     * 本地存储相关配置（UCloudEngine为local时的配置）
     */
    'UCloud' => array(
        //对应的文件路径
        'host' => 'http://www.mo78s.cn/api/upload' 
    ),
		
		/**
     * 云上传引擎,支持local,oss,upyun
     */
    //'UCloudEngine' => 'oss',

    /**
     * 云上传对应引擎相关配置
     * 如果UCloudEngine不为local,则需要按以下配置
     */
   /*  'UCloud' => array(
        //上传的API地址,不带http://,以下api为阿里云OSS杭州节点
        'api' => 'oss-cn-hangzhou.aliyuncs.com',

        //统一的key
        'accessKey' => '',
        'secretKey' => '',

        //自定义配置的空间
        'bucket' => '',
        'host' => 'http://image.xxx.com', //必带http:// 末尾不带/

        'timeout' => 90
    ), */
		

		
);
