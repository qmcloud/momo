<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class StorageController extends AdminbaseController{
	
	function _initialize() {
		parent::_initialize();
	}
	function index(){
		$this->assign(sp_get_cmf_settings('storage'));
		$this->display();
	}
	
	function setting_post(){
		if(IS_POST){
			
			$support_storages=array("Local","Qiniu");
			$type=$_POST['type'];
			if(in_array($type, $support_storages)){
				$result=sp_set_cmf_setting(array('storage'=>$_POST));
				if($result!==false){
					sp_set_dynamic_config(array("FILE_UPLOAD_TYPE"=>$type,"UPLOAD_TYPE_CONFIG"=>$_POST[$type]));
                    
                    $action="编辑文件存储：{$type}";
                    setAdminLog($action);
					$this->success("设置成功！");
				}else{
					$this->error("设置出错！");
				}
			}else{
				$this->error("文件存储类型不存在！");
			}
		
		}
	}
	
	
	
	
	
	
	
}