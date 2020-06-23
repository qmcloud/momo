<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Tuolaji <479923197@qq.com>
// +----------------------------------------------------------------------
/**
 * 参    数：
 * 作    者：lht
 * 功    能：OAth2.0协议下第三方登录数据报表
 * 修改日期：2013-12-13
 */
namespace Api\Controller;
use Common\Controller\AdminbaseController;
class OauthadminController extends AdminbaseController {
	
	//设置
	function setting(){
		$host=sp_get_host();
		$callback_uri_root = $host.__ROOT__.'/index.php?g=api&m=oauth&a=callback&type=';
		$this->assign("callback_uri_root",$callback_uri_root);
		$this->display();
	}
	
	//设置
	function setting_post(){
		if($_POST){
			$qq_key=$_POST['qq_key'];
			$qq_sec=$_POST['qq_sec'];
			$sina_key=$_POST['sina_key'];
			$sina_sec=$_POST['sina_sec'];
			
			$host=sp_get_host();
			
			$call_back = $host.__ROOT__.'/index.php?g=api&m=oauth&a=callback&type=';
			$data = array(
					'THINK_SDK_QQ' => array(
							'APP_KEY'    => $qq_key,
							'APP_SECRET' => $qq_sec,
							'CALLBACK'   => $call_back . 'qq',
					),
					'THINK_SDK_SINA' => array(
							'APP_KEY'    => $sina_key,
							'APP_SECRET' => $sina_sec,
							'CALLBACK'   => $call_back . 'sina',
					),
			);
			
			$result=sp_set_dynamic_config($data);
			
			if($result){
				$this->success("更新成功！");
			}else{
				$this->error("更新失败！");
			}
		}
	}
}