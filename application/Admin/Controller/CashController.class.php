<?php

/**
 * 提现
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class CashController extends AdminbaseController {
    var $type=array(
        '1'=>'支付宝',
        '2'=>'微信',
        '3'=>'银行卡',
    );
    function index(){

        if($_REQUEST['status']!=''){
              $map['status']=$_REQUEST['status'];
                $_GET['status']=$_REQUEST['status'];
                $cash['type']=1;
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
             $map['uid|orderno|trade_no']=array("like","%".$_REQUEST['keyword']."%"); 
             $_GET['keyword']=$_REQUEST['keyword'];
         }		
			
    	$cashrecord=M("users_cashrecord");
    	$count=$cashrecord->where($map)->count();
    	$page = $this->page($count, 20);
    	$lists = $cashrecord
    	->where($map)
    	->order("addtime DESC")
    	->limit($page->firstRow . ',' . $page->listRows)
    	->select();
			foreach($lists as $k=>$v){
				   $userinfo=M("users")->field("user_nicename")->where(["id"=>$v['uid']])->find();
				   $lists[$k]['userinfo']= $userinfo; 
			}	
			$cashrecord_total = $cashrecord->where($map)->sum("money");	
			if($_REQUEST['status']=='')
			{
				$success=$map;
				$success['status']=1;
				$fail=$map;
				$fail['status']=0;
				$cashrecord_success = $cashrecord->where($success)->sum("money");	
				$cashrecord_fail = $cashrecord->where($fail)->sum("money");	
				$cash['success']=$cashrecord_success;
				$cash['fail']=$cashrecord_fail;
				$cash['type']=0;
			}
			$cash['total']=$cashrecord_total;
    	$this->assign('cash', $cash);
    	$this->assign('lists', $lists);
    	$this->assign('formget', $_GET);
    	$this->assign('type', $this->type);
    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display();
    }
		
		function del(){
            $id=intval($_GET['id']);
                if($id){
                    $result=M("users_cashrecord")->delete($id);				
                        if($result){
                            $action="删除提现记录：{$id}";
                setAdminLog($action);
                                $this->success('删除成功');
                         }else{
                                $this->error('删除失败');
                         }			
                }else{				
                    $this->error('数据传入失败！');
                }								  
                $this->display();				
		}		

		
		function edit(){
			 	$id=intval($_GET['id']);
					if($id){
						$cash=M("users_cashrecord")->find($id);
						$cash['userinfo']=M("users")->field("user_nicename")->where(["id"=>$cash['uid']])->find();
						$cash['auth']=M("users_auth")->field("*")->where(["uid"=>$cash['uid']])->find();
						$this->assign('cash', $cash);						
						$this->assign('type', $this->type);						
					}else{				
						$this->error('数据传入失败！');
					}								  
					$this->display();				
		}
		
		function edit_post(){
				if(IS_POST){		
                        if($_POST['status']=='0'){							
							  $this->error('未修改订单状态');			
						}
				
					 $cash=M("users_cashrecord");
					 $cash->create();
					 $cash->uptime=time();
					 $result=$cash->save(); 
					 if($result){
						  if($_POST['status']=='2'){
                              $uid=(int)I('uid');
								 M("users")->where(["id"=>$uid])->setInc("votes",$_POST['votes']);
                                 
                                $action="修改提现记录：{$_POST['id']} - 拒绝";
							}else if($_POST['status']=='1'){
                                $action="修改提现记录：{$_POST['id']} - 同意";
                            }else if($_POST['status']=='0'){
                                $action="修改提现记录：{$_POST['id']} - 审核中";
                            }
                            setAdminLog($action);
						  $this->success('修改成功',U('Cash/index'));
					 }else{
						  $this->error('修改失败');
					 }
				}			
		}
		function export()
		{
			if($_REQUEST['status']!=''){
                $map['status']=$_REQUEST['status'];
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
                $map['uid|orderno|trade_no']=array("like","%".$_REQUEST['keyword']."%"); 
            }
            $xlsName  = "Excel";
            $cashrecord=M("users_cashrecord");
            $xlsData=$cashrecord->where($map)->order("addtime DESC")->select();
            foreach ($xlsData as $k => $v)
            {
                $userinfo=M("users")->field("user_nicename")->where("id='$v[uid]'")->find();
                $xlsData[$k]['user_nicename']= $userinfo['user_nicename']."(".$v['uid'].")";
                $xlsData[$k]['addtime']=date("Y-m-d H:i:s",$v['addtime']); 
                $xlsData[$k]['uptime']=date("Y-m-d H:i:s",$v['uptime']); 
                if($v['status']=='0'){ $xlsData[$k]['status']="处理中";}else if($v['status']=='2'){$xlsData[$k]['status']="提现失败";}else{ $xlsData[$k]['status']="提现完成";} 
            }
            $action="导出提现记录：".M("users_cashrecord")->getLastSql();
                setAdminLog($action);
            $cellName = array('A','B','C','D','E','F','G','H');
            $xlsCell  = array(
                array('id','序号'),
                array('user_nicename','会员'),
                array('money','提现金额'),
                array('votes','兑换点数'),
                array('trade_no','第三方支付订单号'),
                array('status','状态'),
                array('addtime','提交时间'),
                array('uptime','处理时间'),
            );
            exportExcel($xlsName,$xlsCell,$xlsData,$cellName);
		}
    
}
