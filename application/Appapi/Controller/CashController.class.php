<?php
/**
 * 提现记录
 */
namespace Appapi\Controller;
use Common\Controller\HomebaseController;
class CashController extends HomebaseController {
    
    var $status=array(
        '0'=>'审核中',
        '1'=>'成功',
        '2'=>'失败',
    );

	function index(){       
		$uid=(int)I("uid");
		$token=I("token");
		
		if( !$uid || !$token || checkToken($uid,$token)==700 ){
			$this->assign("reason",'您的登陆状态失效，请重新登陆！');
			$this->display(':error');
			exit;
		} 
		$this->assign("uid",$uid);
		$this->assign("token",$token);
		

		$list=M("users_cashrecord")->where(["uid"=>$uid])->order("addtime desc")->limit(0,50)->select();
		foreach($list as $k=>$v){

			$list[$k]['addtime']=date('Y.m.d',$v['addtime']);
			$list[$k]['status_name']=$this->status[$v['status']];
		}
		
		$this->assign("list",$list);
		
		$this->display();
	    
	}
	
	public function getlistmore()
	{
		$uid=(int)I('uid');
		$token=I('token');
		
		$result=array(
			'data'=>array(),
			'nums'=>0,
			'isscroll'=>0,
		);
	
		if(checkToken($uid,$token)==700){
			echo json_encode($result);
			exit;
		} 
		
		$p=I('page');
		$pnums=50;
		$start=($p-1)*$pnums;

        $list=M("users_cashrecord")->where(["uid"=>$uid])->order("addtime desc")->limit($start,$pnums)->select();
		foreach($list as $k=>$v){

			$list[$k]['addtime']=date('Y.m.d',$v['addtime']);
			$list[$k]['status_name']=$this->status[$v['status']];
		}
		
		$nums=count($list);
		if($nums<$pnums){
			$isscroll=0;
		}else{
			$isscroll=1;
		}
		
		$result=array(
			'data'=>$list,
			'nums'=>$nums,
			'isscroll'=>$isscroll,
		);

		echo json_encode($result);
		exit;
	}

}