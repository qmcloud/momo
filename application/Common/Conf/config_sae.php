<?php
$kv = new SaeKV();

// 初始化SaeKV对象
$ret = $kv->init();
$ret = $kv->get('THINKCMF_DYNAMIC_CONFIG');
$runtime_config= $ret ? unserialize($ret) : array();
$_msaestorage=new SaeStorage();

$sae = array(
    'DB_TYPE' => 'mysql',
	'DB_DEPLOY_TYPE'=> 1,
	'DB_RW_SEPARATE'=>true,
    'DB_HOST' => SAE_MYSQL_HOST_M,
    'DB_NAME' => SAE_MYSQL_DB,
    'DB_USER' => SAE_MYSQL_USER,
    'DB_PWD' => SAE_MYSQL_PASS,
    'DB_PORT' => SAE_MYSQL_PORT,
    'DB_PREFIX' => 'sp_',
    //cookies
    "COOKIE_PREFIX" => 'U49dZ1_',
	'FILE_UPLOAD_TYPE'      =>  'Sae',    // 文件上传方式
	
	'TMPL_PARSE_STRING'=>array(
        '__UPLOAD__'=>$_msaestorage->getUrl('data','upload')."/",
	)
);
//print_r($runtime_config); die;
return  array_merge($sae, $runtime_config);
