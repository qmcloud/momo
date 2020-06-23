<?php
/* 
   扩展配置
 */

namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class ConfigController extends AdminbaseController{
	
	function index(){
		
		$config=M("options")->where("option_name='configpub'")->getField("option_value");

		$this->assign('config',json_decode($config,true) );

		$this->display();
	}
	
	function set_post(){
		if(IS_POST){
			
			 $config=I("post.post");

			 $config['login_type']=implode(",",$config['login_type']);
			 $config['share_type']=implode(",",$config['share_type']);
			 $config['live_type']=implode(",",$config['live_type']);

			foreach($config as $k=>$v){
				$config[$k]=html_entity_decode($v);
			}
				
				if ( M("options")->where("option_name='configpub'")->save(['option_value'=>json_encode($config)] )!==false) {
 
                    $key='getConfigPub';
                    setcaches($key,$config);
                
                    $action="修改公共设置";
                    setAdminLog($action);
					$this->success("保存成功！");
				} else {
					$this->error("保存失败！");
				}
		
		}
	}

}