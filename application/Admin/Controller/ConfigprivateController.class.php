<?php
/* 
   扩展配置
 */

namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class ConfigprivateController extends AdminbaseController{
    
	function index(){
		$config=M("options")->where("option_name='configpri'")->getField("option_value");

		$this->assign('config',json_decode($config,true));
		$this->display();
	}
	
	function set_post(){
		if(IS_POST){
			
            $config=I("post.post");
            foreach($config as $k=>$v){
                $config[$k]=trim(html_entity_decode($v));
            }
            
            if ( M("options")->where("option_name='configpri'")->save(['option_value'=>json_encode($config)] )!==false) {
                $action="修改私密设置";
                setAdminLog($action);
        
                $key='getConfigPri';
                setcaches($key,$config);
                
                $this->success("保存成功！");
            } else {
                $this->error("保存失败！");
            }
		
		}
	}
}