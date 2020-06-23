<?php

/**
 * 分销
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class AgentController extends AdminbaseController {
    function index(){

		$map=array();
			
 
		if($_REQUEST['uid']!=''){
			$map['uid']=$_REQUEST['uid']; 
			$_GET['uid']=$_REQUEST['uid'];
		}

		if($_REQUEST['one_uid']!=''){
			$map['one_uid']=$_REQUEST['one_uid']; 
			$_GET['one_uid']=$_REQUEST['one_uid'];
		}
			
	
			
    	$Agent=M("users_agent");
    	$Users=M("users");
    	$count=$Agent->where($map)->count();
    	$page = $this->page($count, 20);
    	$lists = $Agent
			->where($map)
			->order("id DESC")
			->limit($page->firstRow . ',' . $page->listRows)
			->select();
			
		foreach($lists as $k=>$v){
			$userinfo=$Users->field("user_nicename")->where("id='{$v['uid']}'")->find();
			$lists[$k]['userinfo']=$userinfo;
			if($v['one_uid']){
				$oneuserinfo=$Users->field("user_nicename")->where("id='{$v['one_uid']}'")->find();
			}else{
				$oneuserinfo['user_nicename']='未设置';
			}
			$lists[$k]['oneuserinfo']=$oneuserinfo;
			
			if($v['two_uid']){
				$twouserinfo=$Users->field("user_nicename")->where("id='{$v['two_uid']}'")->find();
			}else{
				$twouserinfo['user_nicename']='未设置';
			}
			$lists[$k]['twouserinfo']=$twouserinfo;

		}
			
    	$this->assign('lists', $lists);
    	$this->assign('formget', $_GET);
    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display();
    }

    function index2(){

		$map=array();
			
 
		if($_REQUEST['uid']!=''){
			$map['uid']=$_REQUEST['uid']; 
			$_GET['uid']=$_REQUEST['uid'];
		}

    	$live=M("users_agent_profit");
    	$count=$live->where($map)->count();
    	$page = $this->page($count, 20);
    	$lists = $live
			->where($map)
			->order("id DESC")
			->limit($page->firstRow . ',' . $page->listRows)
			->select();
			
		foreach($lists as $k=>$v){
			 $userinfo=M("users")->field("user_nicename")->where("id='{$v['uid']}'")->find();
			 $lists[$k]['userinfo']=$userinfo;
		}
			
    	$this->assign('lists', $lists);
    	$this->assign('formget', $_GET);
    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display();
    }
	
	
	function del()
	{
		$id=intval($_GET['id']);
		if($id){
			$result=M("users_agent")->delete($id);				
			if($result){
					$this->success('删除成功');
			}else{
				$this->error('删除失败');
			}			
		}else{				
			$this->error('数据传入失败！');
		}								  	
	}
		
}
