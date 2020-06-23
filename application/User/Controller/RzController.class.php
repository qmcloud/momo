<?php
/**
 * 会员认证
 */
namespace User\Controller;
use Common\Controller\HomebaseController;
class RzController extends HomebaseController {
	
	function index(){
		      
					$uid=I('uid');
					
					$this->assign("uid",$uid);
        
	       $this->display();
	    
	}
	
	function upload(){

    	$config=array(
			    'replace' => true,
    			'rootPath' => './'.C("UPLOADPATH"),
    			'savePath' => 'rz/',
    			'maxSize' => 0,//500K
    			'saveName'   =>     array('uniqid',''),
    			//'exts'       =>    array('jpg', 'png', 'jpeg'),
    			'autoSub'    =>    false,
    	);

    	$upload = new \Think\Upload($config);//
    	$info=$upload->upload();

    	//开始上传
    	if ($info) {
    	//上传成功
    	//写入附件数据库信息
    		$first=array_shift($info);
			
			if(!empty($first['url'])){
				$url=$first['url'];
			}else{
				$url=C("TMPL_PARSE_STRING.__UPLOAD__").'rz/'.$first['savename'];

				$url='http://'.$_SERVER['HTTP_HOST'].$url;
			}

				
    		 echo json_encode(array("ret"=>200,'data'=>array("url"=>$url),'msg'=>''));
    		//$this->ajaxReturn(sp_ajax_return(array("file"=>$file),"上传成功！",1),"AJAX_UPLOAD");
    	} else {
    		//上传失败，返回错误
    		//$this->ajaxReturn(sp_ajax_return(array(),$upload->getError(),0),"AJAX_UPLOAD");
				  echo json_encode(array("ret"=>0,'file'=>'','msg'=>$upload->getError()));
    	}	

	}	
	
	function auth(){
		     $uid=(int)I('uid');        
		     $reset=I('reset');        
				 $this->assign("uid",$uid);
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
	function authStep(){
		     $uid=I('uid');        

				 $this->assign("uid",$uid);

				 $this->display("authstep");
	      // $this->display();
	    
	}	
	function authsave(){
		    
				 $data['uid']=(int)I("uid");
				 $data['real_name']=I("real_name");
				 $data['mobile']=I("mobile");
				 $data['card_no']=I("card_no");
				 $data['bank_name']=I("bank_name");
				 $data['accounts_province']=I("accounts_province");
				 $data['accounts_city']=I("accounts_city");
				 $data['sub_branch']=I("sub_branch");
				 $data['cer_type']=I("cer_type");
				 $data['cer_no']=I("cer_no");
				 $data['front_view']=I("front_view");
				 $data['back_view']=I("back_view");
				 $data['handset_view']=I("handset_view");
				 $data['status']=0;
				 $data['addtime']=time();
				 $authid=M("users_auth")->where(["uid"=>$data['uid']])->getField("id");
				 if($authid){
					  $result=M("users_auth")->where("id='{$authid}'")->save($data);
				 }else{
					  $result=M("users_auth")->add($data);
				 }
				 
				
				if($result!==false){
					
					  echo json_encode(array("ret"=>200,'data'=>array(),'msg'=>''));
				}else{
					
					  echo json_encode(array("ret"=>0,'data'=>array(),'msg'=>'提交失败，请重新提交'));
				}
			   
	}	

	function success(){
		      
         $uid=I('uid');        

				 $this->assign("uid",$uid);  
	       $this->display();
	    
	}	
	function error(){
		      
         $uid=I('uid');        

				 $this->assign("uid",$uid);  
	       $this->display();
	    
	}	
}