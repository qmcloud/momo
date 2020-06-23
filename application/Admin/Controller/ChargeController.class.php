<?php

/**
 * 充值记录
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class ChargeController extends AdminbaseController {
    var $status=array("0"=>"未支付","1"=>"已完成");
    var $type=array("1"=>"支付宝","2"=>"微信","3"=>"苹果支付","4"=>"支付宝当面付");
    var $ambient=array(
            "1"=>array(
                '0'=>'App',
                '1'=>'PC',
            ),
            "2"=>array(
                '0'=>'App',
                '1'=>'公众号',
                '2'=>'PC',
            ),
            "3"=>array(
                '0'=>'沙盒',
                '1'=>'生产',
            ),
            "4"=>array(
                '0'=>'App',
                '1'=>'PC',
            )
        );
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
            $map['uid|orderno|trade_no']=array("like","%".$_REQUEST['keyword']."%"); 
            $_GET['keyword']=$_REQUEST['keyword'];
        }

    	$charge=M("users_charge");
    	$count=$charge->where($map)->count();
    	$page = $this->page($count, 20);
    	$lists = $charge
    	->where($map)
    	->order("addtime DESC")
    	->limit($page->firstRow . ',' . $page->listRows)
    	->select();
		
		$moneysum = $charge
					->where($map)
					->sum("money");	
					
			foreach($lists as $k=>$v){
				   $userinfo=M("users")->field("user_nicename")->where("id='$v[uid]'")->find();
				   $lists[$k]['userinfo']= $userinfo;
					 
			}
            
    	
    	$this->assign('moneysum', $moneysum);
    	$this->assign('lists', $lists);
    	$this->assign('formget', $_GET);
    	$this->assign("page", $page->show('Admin'));
        
        $this->assign('status', $this->status);
        $this->assign('type', $this->type);
        $this->assign('ambient', $this->ambient);
    	
    	$this->display();
    }
    
    function setPay(){
        $id=intval($_GET['id']);
        if($id){
            $result=M("users_charge")->where(["id"=>$id,"status"=>0])->find();				
            if($result){
                
                /* 更新会员虚拟币 */
                $coin=$result['coin']+$result['coin_give'];
                M("users")->where("id='{$result['touid']}'")->setInc("coin",$coin);
                /* 更新 订单状态 */
                M("users_charge")->where("id='{$result['id']}'")->save(array("status"=>1));
                    
                $action="确认充值：{$id}";
                setAdminLog($action);
                $this->success('操作成功');
             }else{
                $this->error('数据传入失败！');
             }			
        }else{				
            $this->error('数据传入失败！');
        }								          
    }
		
		function del(){
            $id=intval($_GET['id']);
            if($id){
                $result=M("users_charge")->delete($id);				
                if($result){
                    $action="删除充值记录：{$id}";
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
				$charge=M("users_charge");
				$xlsData=$charge->where($map)->Field('id,uid,money,coin,coin_give,orderno,type,trade_no,status,addtime')->order("addtime DESC")->select();
                foreach ($xlsData as $k => $v)
                {
                    $userinfo=M("users")->field("user_nicename")->where("id='$v[uid]'")->find();
                    $xlsData[$k]['user_nicename']= $userinfo['user_nicename']."(".$v['uid'].")";
                    $xlsData[$k]['addtime']=date("Y-m-d H:i:s",$v['addtime']); 
                    $xlsData[$k]['type']=$this->type[$v['type']];
                    $xlsData[$k]['status']=$this->status[$v['status']];
                }
        
                $action="导出充值记录：".M("users_charge")->getLastSql();
                setAdminLog($action);
				$cellName = array('A','B','C','D','E','F','G','H','I','J');
				$xlsCell  = array(
                    array('id','序号'),
                    array('user_nicename','会员'),
                    array('money','人民币金额'),
                    array('coin','兑换点数'),
                    array('coin_give','赠送点数'),
                    array('orderno','商户订单号'),
                    array('type','支付类型'),
                    array('trade_no','第三方支付订单号'),
                    array('status','订单状态'),
                    array('addtime','提交时间')
                );
                exportExcel($xlsName,$xlsCell,$xlsData,$cellName);
		}

}
