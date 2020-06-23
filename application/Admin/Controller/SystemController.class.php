<?php

/**
 * 系统消息
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class SystemController extends AdminbaseController {
    function index(){

		$config=getConfigPri();
			
		$this->assign('config', $config);
			
    	
    	$this->display("edit");
    }
		
	function send(){
		$content=I("content");
		
		if(!$content){
			$data=array(
				"error"=>10001,
				"data"=>'',
				"msg"=>'内容不能为空'
			);
            echo json_encode($data);
            exit;
		}
		//$id=$_SESSION['ADMIN_ID'];
		//$user=M("users")->where("id={$id}")->find();	
        $action="发送系统消息：{$content}";
                    setAdminLog($action);
		
		$data=array(
			"error"=>0,
			"data"=>'',
			"msg"=>''
		);
				
		echo json_encode($data);
		exit;
	}		
		
}
