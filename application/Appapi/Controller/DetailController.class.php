<?php
/**
 * 我的明细
 */
namespace Appapi\Controller;
use Common\Controller\HomebaseController;
class DetailController extends HomebaseController {

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
		
		$Coinrecird=M("users_coinrecord");
		$Gift=M("gift");
		$list=$Coinrecird->field("uid,giftid,sum(giftcount) as giftcounts,sum(totalcoin) as total")->where(["action"=>'sendgift',"touid"=>$uid])->group("uid,giftid,showid")->order("addtime desc")->limit(0,50)->select();
		foreach($list as $k=>$v){
			$giftinfo=$Gift->field("giftname")->where("id={$v['giftid']}")->find();
			if(!$giftinfo){
				$giftinfo=array(
					"giftname"=>'礼物已删除'
				);
			}
			$list[$k]['giftinfo']=$giftinfo;
			$userinfo=getUserInfo($v['uid']);
			if(!$userinfo){
				$userinfo=array(
					"user_nicename"=>'用户已删除'
				);
			}
			$list[$k]['userinfo']=$userinfo;
		}
		
		$this->assign("list",$list);
		
		$Liverecord=M("users_liverecord");
		$list_live=$Liverecord->field("starttime,endtime")->where(["uid"=>$uid])->order("starttime desc")->limit(0,50)->select();
		foreach($list_live as $k=>$v){
			$cha=$v['endtime']-$v['starttime'];
			$list_live[$k]['length']=getSeconds($cha,1);
		}

		$this->assign("list_live",$list_live);
		
		$this->display();
	    
	}
	
	public function receive_more()
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
		
		$Coinrecird=M("users_coinrecord");
		$Gift=M("gift");
		$list=$Coinrecird->field("uid,giftid,sum(giftcount) as giftcounts,sum(totalcoin) as total")->where(["action"=>'sendgift', "touid"=>$uid])->group("uid,giftid,showid")->order("addtime desc")->limit($start,$pnums)->select();
		foreach($list as $k=>$v){
			$giftinfo=$Gift->field("giftname")->where("id={$v['giftid']}")->find();
			if(!$giftinfo){
				$giftinfo=array(
					"giftname"=>'礼物已删除'
				);
			}
			$list[$k]['giftinfo']=$giftinfo;
			$userinfo=getUserInfo($v['uid']);
			if(!$userinfo){
				$userinfo=array(
					"user_nicename"=>'用户已删除'
				);
			}
			$list[$k]['userinfo']=$userinfo;
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
	
	public function liverecord_more()
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
		
		$Liverecord=M("users_liverecord");
		$list=$Liverecord->field("starttime,endtime")->where(["uid"=>$uid])->order("starttime desc")->limit($start,$pnums)->select();
		foreach($list as $k=>$v){
			$list[$k]['starttime']=date("Y-m-d H:i",$v['starttime']);
			$list[$k]['endtime']=date("Y-m-d H:i",$v['endtime']);
			$cha=$v['endtime']-$v['starttime'];
			$list[$k]['length']=getSeconds($cha,1);
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