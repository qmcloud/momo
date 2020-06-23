<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class MainController extends AdminbaseController {
	
    public function index(){
    	//会员统计
			$users=M("users");
			$users_auth=M("users_auth");
			$users_live=M("users_live");
			$users_admin=array();
			$users_admin['register']=$users->where("id>0 and user_type=2")->count();
			$users_admin['auth']=$users_auth->where("status=1")->count();
			$users_admin['live']=$users_live->where("islive=1")->count();
			$this->assign('users_admin', $users_admin);
			//充值数据
			$y = date("Y");$m = date("m");$d = date("d");
			$dayTime= mktime(0,0,0,$m,$d,$y);//今天凌晨的时间戳
			$todayTime= mktime(0,0,0,$m,($d+1),$y);//第二天凌晨的时间戳
			$users_charge=M("users_charge");
			$users_charge_admin=M("users_charge_admin");
			$charge_admin=array();
			//今日线上充值金额
			$money_my = $users_charge->where("status='1' and addtime<'{$todayTime}' and addtime>'{$dayTime}'")->sum("money");	
			$money_my_count = $users_charge->where("status='1' and addtime<'{$todayTime}' and addtime>'{$dayTime}'")->count();	
			if($money_my==null){$money_my="0.00";}
			$charge_admin['money_my']=$money_my;
			$charge_admin['money_my_count']=$money_my_count;
			//今日管理员充值
			$money_amdin = $users_charge_admin->where("addtime<'{$todayTime}' and addtime>'{$dayTime}'")->sum("coin");	
			$money_amdin_count = $users_charge_admin->where("addtime<'{$todayTime}' and addtime>'{$dayTime}'")->count();	
			if($money_amdin==null){$money_amdin="0.00";}
			$charge_admin['money_amdin']=$money_amdin;
			$charge_admin['money_amdin_count']=$money_amdin_count;
			$this->assign('charge_admin', $charge_admin);
			//充值来源
			$source=array();
			$source_zfb = $users_charge->where("status='1' and type=1 and addtime<'{$todayTime}' and addtime>'{$dayTime}'")->sum("money");	
			$source_wx = $users_charge->where("status='1' and type=2 and addtime<'{$todayTime}' and addtime>'{$dayTime}'")->sum("money");	
			$source_ios = $users_charge->where("status='1' and type=3 and addtime<'{$todayTime}' and addtime>'{$dayTime}'")->sum("money");	
			if($source_zfb==null){$source_zfb="0.00";}
			if($source_wx==null){$source_wx="0.00";}
			if($source_ios==null){$source_ios="0.00";}
			$source['source_zfb']=$source_zfb;
			$source['source_wx']=$source_wx;
			$source['source_ios']=$source_ios;
			$this->assign('source', $source);
			//主播审核
			$examine=$users_auth->where("status=0")->count();
			$this->assign('examine', $examine);
			//提现数据
			$cashrecord=array();
			$users_cashrecord=M("users_cashrecord");
			$cashrecord_total = $users_cashrecord->where("status=1 or status=0")->sum("money");	
			$cashrecord_success = $users_cashrecord->where("status=1")->sum("money");	
			$cashrecord_fail = $users_cashrecord->where("status=0")->sum("money");	
			if($cashrecord_total==null){$cashrecord_total="0.00";}if($cashrecord_success==null){$cashrecord_success="0.00";}if($cashrecord_fail==null){$cashrecord_fail="0.00";}
			$hotfamily = $users_cashrecord -> query('select count(*) as total from cmf_users_cashrecord where status=0 group by uid');
			$cashrecord_info=count($hotfamily);
			$cashrecord['fail']=$cashrecord_fail;
			$cashrecord['total']=$cashrecord_total;
			$cashrecord['success']=$cashrecord_success;
			$cashrecord['info']=$cashrecord_info;
			$this->assign('cashrecord', $cashrecord);
			//热门主播
			$prefix= C("DB_PREFIX");
			$hot=M("users_live l")
					->field("l.user_nicename,l.avatar,l.uid,l.stream,l.title,l.city,l.islive")
					->join("left join {$prefix}users u on u.id=l.uid")
					->where("l.islive='1' and u.ishot='1'")
					->order("u.isrecommend desc,l.starttime desc")
					->limit(3)
					->select();
			$this->assign("hot",$hot);	
    	$this->display();
    }
}