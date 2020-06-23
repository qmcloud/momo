<?php
namespace Common\Controller;
use Common\Controller\HomebaseController;
class AppapibaseController extends HomebaseController{
	
	function _initialize() {
		parent::_initialize();
		
		$uid=I("uid");
		$token=I("token");

		if(!IS_AJAX && ( !$uid || !$token || checkToken($uid,$token)==700 )){
			$this->assign("reason",'您的登陆状态失效，请重新登陆！');
			$this->display(':error');
			exit;
		} 
	}
	
}