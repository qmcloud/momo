<?php
/**
 * 会员认证
 */
namespace Appapi\Controller;
use Common\Controller\HomebaseController;
class AuthController extends HomebaseController {
	
	public function index(){
		$uid=(int)I("uid");
		$token=I("token");
		
		if( !$uid || !$token || checkToken($uid,$token)==700 ){
			$this->assign("reason",'您的登陆状态失效，请重新登陆！');
			$this->display(':error');
			exit;
		} 
		$this->assign("uid",$uid);
		$this->assign("token",$token); 
        
		$reset=I('reset');        

		if($reset!=1){				 
			$auth=M("users_auth")->where(["uid"=>$uid])->find();
			if($auth){
				if($auth['status']==0){
					$this->display("success");
					exit;
				}else if($auth['status']==1){
					$this->assign("auth",$auth);
					$this->display("authstep2");
					exit;
				}else if($auth['status']==2){
					$this->assign("reason",nl2br($auth['reason']));
					$this->display("error");
					exit;
				}
			}

		}

		$this->display();
	    
	}

	/* 图片上传 */
	public function upload(){
		$saveName=I('saveName'); 
	
    	$config=array(
			    'replace' => true,
    			'rootPath' => './'.C("UPLOADPATH"),
    			'savePath' => 'rz/',
    			'maxSize' => 0,//500K
    			'saveName'   =>    array('uniqid',''),
    			'exts'       =>    array('jpg', 'png', 'jpeg'),
    			'autoSub'    =>    false,
    	);

    	$upload = new \Think\Upload($config);//
    	$info=$upload->upload();

    	//开始上传
    	if ($info) {
			//上传成功
			$oriName = $_FILES['file']['name'];
			//写入附件数据库信息
			$first=array_shift($info);
			if(!empty($first['url'])){
				$url=$first['url'];				
			}else{
				$url=C("TMPL_PARSE_STRING.__UPLOAD__").$config['savePath'].$first['savename'];
			}
    		echo json_encode(array("ret"=>200,'data'=>array("url"=>$url),'msg'=>''));
    		//$this->ajaxReturn(sp_ajax_return(array("file"=>$file),"上传成功！",1),"AJAX_UPLOAD");
    	} else {
    		//上传失败，返回错误
    		//$this->ajaxReturn(sp_ajax_return(array(),$upload->getError(),0),"AJAX_UPLOAD");
			echo json_encode(array("ret"=>0,'file'=>'','msg'=>$upload->getError()));
    	}	
        exit;
	}	
	/* 认证页面 */
	public function authstep(){
		$uid=I("uid");
		$token=I("token");
		
		if( !$uid || !$token || checkToken($uid,$token)==700 ){
			$this->assign("reason",'您的登陆状态失效，请重新登陆！');
			$this->display(':error');
			exit;
		} 
		$this->assign("uid",$uid);
		$this->assign("token",$token); 
		$this->display();
	    
	}	
	/* 认证保存 */
	public function authsave(){
        
        $uid=(int)I("uid");
		$token=I("token");
		
		if( !$uid || !$token || checkToken($uid,$token)==700 ){
            echo json_encode(array("ret"=>0,'data'=>array(),'msg'=>'您的登陆状态失效，请重新登陆！'));
			exit;
		} 
        

		$data['uid']=(int)I("uid");
		$data['real_name']=I("real_name");
		$data['mobile']=I("mobile");
		$data['cer_no']=I("cer_no");
		$data['front_view']=I("front_view");
		$data['back_view']=I("back_view");
		$data['handset_view']=I("handset_view");
		$data['status']=0;
		$data['addtime']=time();
		$authid=M("users_auth")->where(["uid"=>$data['uid']])->getField("uid");
		if($authid){
			$result=M("users_auth")->where(["uid"=>$authid])->save($data);
		}else{
			$result=M("users_auth")->add($data);
		}

		if($result!==false){
			echo json_encode(array("ret"=>200,'data'=>array(),'msg'=>''));
		}else{
			echo json_encode(array("ret"=>0,'data'=>array(),'msg'=>'提交失败，请重新提交'));
		}
        exit;
	}	
	/* 成功 */
	public function success(){   
		$this->display();
	}	
	/* 失败 */
	public function error(){   
		$this->display();
	}	
}