<?php

/**
 * 管理员日志
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class AdminlogController extends AdminbaseController {

    function index(){
        
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
            $map['adminid']=array("like","%".$_REQUEST['keyword']."%"); 
            $_GET['keyword']=$_REQUEST['keyword'];
        }		
			
    	$AdminLog=M("admin_log");
    	$count=$AdminLog->where($map)->count();
    	$page = $this->page($count, 20);
    	$lists = $AdminLog
            ->where($map)
            ->order("addtime DESC")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();

    	$this->assign('lists', $lists);
    	$this->assign('formget', $_GET);
    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display();
    }
		
    function del(){
        $id=intval($_GET['id']);
        if($id){
            $result=M("admin_log")->delete($id);				
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
            $map['adminid']=array("like","%".$_REQUEST['keyword']."%"); 
        }
        $xlsName  = "Excel";
        $AdminLog=M("admin_log");
        $xlsData=$AdminLog->where($map)->order("addtime DESC")->select();
        foreach ($xlsData as $k => $v)
        {
            $xlsData[$k]['ip']=long2ip($v['ip']);
            $xlsData[$k]['addtime']=date("Y-m-d H:i:s",$v['addtime']);             
        }
                $cellName = array('A','B','C','D','E');
                $xlsCell  = array(
                    array('id','序号'),
                    array('admin','管理员'),
                    array('action','行为'),
                    array('ip','IP'),
                    array('addtime','提交时间'),
        );
        exportExcel($xlsName,$xlsCell,$xlsData,$cellName);
    }
    
}
