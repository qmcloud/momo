<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace plugins\Demo\Controller; //Demo插件英文名，改成你的插件英文就行了
use Api\Controller\PluginController;//插件控制器基类

class IndexController extends PluginController{
	
	function index(){
		//$plugin_demo_model=D("plugins://Demo/PluginDemo");//实例化自定义模型PluginDemo ,需要创建plugin_demo表
		//$plugin_demo_model->test();//调用自定义模型PluginDemo里的test方法
		
		$users_model=D("Users");//实例化Common模块下的Users模型
		//$users_model=D("Common/Users");//也可以这样实例化Common模块下的Users模型
		$users=$users_model->limit(0,5)->select();
		
		$this->assign("users",$users);
		
		$this->display(":index");
	}

}
