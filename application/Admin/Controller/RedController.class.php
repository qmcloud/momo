<?php

/**
 * 红包
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class RedController extends AdminbaseController {
    var $type=array(
        '0'=>'平均',
        '1'=>'手气',
    );
    var $type_grant=array(
        '0'=>'立即',
        '1'=>'延迟',
    );
    function index(){

		$map=array();
			
 
		if($_REQUEST['uid']!=''){
			$map['uid']=$_REQUEST['uid']; 
			$_GET['uid']=$_REQUEST['uid'];
		}

			
    	$Red=M("red");

    	$count=$Red->where($map)->count();
    	$page = $this->page($count, 20);
    	$lists = $Red
			->where($map)
			->order("id DESC")
			->limit($page->firstRow . ',' . $page->listRows)
			->select();
			
		foreach($lists as $k=>$v){
            $v['userinfo']=getUserInfo($v['uid']);
            $v['anchorinfo']=getUserInfo($v['liveuid']);
            $lists[$k]=$v;
		}
			
    	$this->assign('lists', $lists);
    	$this->assign('type', $this->type);
    	$this->assign('type_grant', $this->type_grant);
    	$this->assign('formget', $_GET);
    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display();
    }

    function index2(){
        $redid=I("redid");
		$map=array();
        
        $map['redid']=$redid; 
 
		if($_REQUEST['uid']!=''){
			$map['uid']=$_REQUEST['uid']; 
			$_GET['uid']=$_REQUEST['uid'];
		}

    	$Redrecord=M("red_record");
    	$count=$Redrecord->where($map)->count();
    	$page = $this->page($count, 20);
    	$lists = $Redrecord
			->where($map)
			->order("addtime DESC")
			->limit($page->firstRow . ',' . $page->listRows)
			->select();
			
		foreach($lists as $k=>$v){
			$userinfo=getUserInfo($v['uid']);
			$lists[$k]['userinfo']=$userinfo;
		}
			
    	$this->assign('lists', $lists);
    	$this->assign('formget', $_GET);
    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display();
    }
	
}
