<?php

/**
 * 提现
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class FeedbackController extends AdminbaseController {
    function index(){

					if($_REQUEST['status']!=''){
						  $map['status']=$_REQUEST['status'];
							$_GET['status']=$_REQUEST['status'];
					 }
				   if($_REQUEST['start_time']!=''){
						  $map['addtime']=array("gt",strtotime($_REQUEST['start_time']));
							$_GET['start_time']=$_REQUEST['start_time'];
					 }
					 
					 if($_REQUEST['end_time']!=''){
						 
						   $map['addtime']=array("lt",strtotime($_REQUEST['end_time']));
							 $_GET['end_time']=$_REQUEST['end_time'];
					 }
					 if($_REQUEST['start_time']!='' && $_REQUEST['end_time']!='' ){
						 
						 $map['addtime']=array("between",array(strtotime($_REQUEST['start_time']),strtotime($_REQUEST['end_time'])));
						 $_GET['start_time']=$_REQUEST['start_time'];
						 $_GET['end_time']=$_REQUEST['end_time'];
					 }
 
					 if($_REQUEST['keyword']!=''){
						 $map['uid']=array("like","%".$_REQUEST['keyword']."%"); 
						 $_GET['keyword']=$_REQUEST['keyword'];
					 }		
			
    	$feedback=M("feedback");
    	$count=$feedback->where($map)->count();
    	$page = $this->page($count, 20);
    	$lists = $feedback
    	->where($map)
    	->order("addtime DESC")
    	->limit($page->firstRow . ',' . $page->listRows)
    	->select();
			
			foreach($lists as $k=>$v){
				   $userinfo=M("users")->field("user_nicename")->where(["id"=>$v['uid']])->find();
				   $lists[$k]['userinfo']= $userinfo;
					 
			}			
			
    	$this->assign('lists', $lists);
    	$this->assign('formget', $_GET);
    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display();
    }
		
		function setstatus(){
			 	$id=intval($_GET['id']);
					if($id){
						 $data['status']=1;
						 $data['uptime']=time();
						$result=M("feedback")->where(["id"=>$id])->save($data);				
							if($result){
                                $action="用户反馈标记处理：{$id}";
                    setAdminLog($action);
									$this->success('标记成功');
							 }else{
									$this->error('标记失败');
							 }			
					}else{				
						$this->error('数据传入失败！');
					}								  		
		}		
		
		function del(){
			 	$id=intval($_GET['id']);
					if($id){
						$result=M("feedback")->delete($id);				
							if($result){
                                $action="删除用户反馈：{$id}";
                    setAdminLog($action);
									$this->success('删除成功');
							 }else{
									$this->error('删除失败');
							 }			
					}else{				
						$this->error('数据传入失败！');
					}								  
		}		

		
		function edit(){
			 	$id=intval($_GET['id']);
					if($id){
						$feedback=M("feedback")->find($id);
						$feedback['userinfo']=M("users")->field("user_nicename")->where(["id"=>$feedback['uid']])->find();
						$this->assign('feedback', $feedback);						
					}else{				
						$this->error('数据传入失败！');
					}								  
					$this->display();				
		}
		
		function edit_post(){
				if(IS_POST){		
            if($_POST['status']=='0'){							
							  $this->error('未修改状态');			
						}
				
					 $feedback=M("feedback");
					 $feedback->create();
					 $feedback->uptime=time();
					 $result=$feedback->save(); 
					 if($result){
						  $this->success('修改成功',U('Userfeedback/index'));
					 }else{
						  $this->error('修改失败');
					 }
				}			
		}		
    
}
