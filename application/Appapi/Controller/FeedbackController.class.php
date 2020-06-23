<?php
/**
 * 用户反馈
 */
namespace Appapi\Controller;
use Common\Controller\HomebaseController;

class FeedbackController extends HomebaseController{
	
	function index(){
		 $uid=I("uid");
		 $token=I("token");
		 $model=I("model");
		 $version=I("version");
         
        if( !$uid || !$token || checkToken($uid,$token)==700 ){
			$this->assign("reason",'您的登陆状态失效，请重新登陆！');
			$this->display(':error');
			exit;
		} 
        
		 $this->assign("uid",$uid);
		 $this->assign("token",$token);
		 $this->assign("version",$version);
		 $this->assign("model",$model);
		 $this->display();
	}
	
	function feedbackSave(){
        $uid=I("uid");
		$token=I("token");
		
		if( !$uid || !$token || checkToken($uid,$token)==700 ){
            echo json_encode(array("status"=>400,'errormsg'=>'您的登陆状态失效，请重新登陆！'));
			exit;
		} 
        
		$data['uid']=I('uid');
		$data['version']=checkNull(I('version'));
		$data['model']=checkNull(I('model'));
		$data['content']=checkNull(I('content'));
		$data['thumb']=checkNull(I('thumb'));
		$data['addtime']=time();

		$result=M("feedback")->add($data);
		if($result){
				echo json_encode(array("status"=>0,'msg'=>''));
		}else{
			 	echo json_encode(array("status"=>400,'errormsg'=>'提交失败'));
		}
	
	}	
    
	/* 图片上传 */
	public function upload(){
    	$config=array(
			    'replace' => true,
    			'rootPath' => './'.C("UPLOADPATH"),
    			'savePath' => 'feedback/',
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
}