<?php
namespace Common\Controller;
use Common\Controller\HomebaseController;
class MemberbaseController extends HomebaseController{
	
	function _initialize() {
		parent::_initialize();
		
		$this->check_login();
		$this->check_user();
	}
	
}