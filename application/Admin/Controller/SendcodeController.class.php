<?php

/**
 * 信息记录
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class SendcodeController extends AdminbaseController {
    var $msg_type=array(
        "1"=>"短信验证码",
        "2"=>"邮箱验证码",
    );
    function index(){

        if($_REQUEST['type']!=''){
            $map['type']=$_REQUEST['type'];
            $_GET['type']=$_REQUEST['type'];
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
            $map['account']=array("like","%".$_REQUEST['keyword']."%"); 
            $_GET['keyword']=$_REQUEST['keyword'];
        }		
			
    	$Sendcode=M("sendcode");
    	$count=$Sendcode->where($map)->count();
    	$page = $this->page($count, 20);
    	$lists = $Sendcode
            ->where($map)
            ->order("addtime DESC")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        
        foreach($lists as $k=>$v){
            
            $v['account']=m_s($v['account']);
            
            $lists[$k]=$v;
        }

    	$this->assign('msg_type', $this->msg_type);
    	$this->assign('lists', $lists);
    	$this->assign('formget', $_GET);
    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display();
    }
		
    function del(){
        $id=intval($_GET['id']);
        if($id){
            $result=M("sendcode")->delete($id);				
                if($result){
                        $this->success('删除成功');
                 }else{
                        $this->error('删除失败');
                 }			
        }else{				
            $this->error('数据传入失败！');
        }								  			
    }		
    
    function export()
    {
        if($_REQUEST['type']!=''){
            $map['type']=$_REQUEST['type'];
        }
        if($_REQUEST['start_time']!=''){
            $map['addtime']=array("gt",strtotime($_REQUEST['start_time']));
        }			 
        if($_REQUEST['end_time']!=''){	 
            $map['addtime']=array("lt",strtotime($_REQUEST['end_time']));
        }
        if($_REQUEST['start_time']!='' && $_REQUEST['end_time']!='' ){	 
            $map['addtime']=array("between",array(strtotime($_REQUEST['start_time']),strtotime($_REQUEST['end_time'])));
        }
        if($_REQUEST['keyword']!=''){
            $map['account']=array("like","%".$_REQUEST['keyword']."%"); 
        }
        $xlsName  = "Excel";
        $Sendcode=M("sendcode");
        $xlsData=$Sendcode->where($map)->order("addtime DESC")->select();
        foreach ($xlsData as $k => $v)
        {
            $xlsData[$k]['msg_type']=$this->msg_type[$v['type']];
            $xlsData[$k]['addtime']=date("Y-m-d H:i:s",$v['addtime']);             
        }
                $cellName = array('A','B','C','D','E');
                $xlsCell  = array(
            array('id','序号'),
            array('msg_type','信息类型'),
            array('account','接收账号'),
            array('content','信息内容'),
            array('addtime','提交时间'),
        );
        exportExcel($xlsName,$xlsCell,$xlsData,$cellName);
    }
    
}
