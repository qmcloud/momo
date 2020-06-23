<?php
namespace Api\Controller;
use Common\Controller\AdminbaseController;
class MapController extends AdminbaseController {


	function _initialize() {
	}
	
	function index(){
		$lng=I("get.lng","121.481798");
		$lat=I("get.lat","31.238845");
		$this->assign("lng",$lng);
		$this->assign("lat",$lat);
		$this->display();
	}
	
}