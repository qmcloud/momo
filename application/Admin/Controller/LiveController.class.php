<?php

/**
 * 直播记录
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class LiveController extends AdminbaseController {
    function index(){
			$config=getConfigPub();
			$map=array();
			
				   if($_REQUEST['start_time']!=''){
						  $map['starttime']=array("gt",strtotime($_REQUEST['start_time']));
							$_GET['start_time']=$_REQUEST['start_time'];
					 }
					 
					 if($_REQUEST['end_time']!=''){
						 
						   $map['starttime']=array("lt",strtotime($_REQUEST['end_time']));
							 $_GET['end_time']=$_REQUEST['end_time'];
					 }
					 if($_REQUEST['start_time']!='' && $_REQUEST['end_time']!='' ){
						 
						 $map['starttime']=array("between",array(strtotime($_REQUEST['start_time']),strtotime($_REQUEST['end_time'])));
						 $_GET['start_time']=$_REQUEST['start_time'];
						 $_GET['end_time']=$_REQUEST['end_time'];
					 }
 
					 if($_REQUEST['keyword']!=''){
						 $map['uid']=$_REQUEST['keyword']; 
						 $_GET['keyword']=$_REQUEST['keyword'];
					 }
			
	
			
    	$live=M("users_liverecord");
    	$count=$live->where($map)->count();
    	$page = $this->page($count, 20);
    	$lists = $live
    	->where($map)
    	->order("id DESC")
    	->limit($page->firstRow . ',' . $page->listRows)
    	->select();
			
			foreach($lists as $k=>$v){
				 $userinfo=M("users")->field("user_nicename")->where(["id"=>$v['uid']])->find();
				 $lists[$k]['userinfo']=$userinfo;
			}
			
    	$this->assign('config', $config);
    	$this->assign('lists', $lists);
    	$this->assign('formget', $_GET);
    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display();
    }
		function del()
		{
			$id=intval($_GET['id']);
			if($id){
				$result=M("users_liverecord")->delete($id);				
				if($result){
						$this->success('删除成功');
				}else{
					$this->error('删除失败');
				}			
			}else{				
				$this->error('数据传入失败！');
			}								  
			$this->display();		
		}
		
}
