<?php
/**
 * 商城
 */
namespace Appapi\Controller;
use Common\Controller\HomebaseController;
class MallController extends HomebaseController {
	public $long=array(
		'1'=>'1个月',
		'3'=>'3个月',
		'6'=>'6个月',
		'12'=>'12个月',
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
        
        $user=M("users")->field("id,user_nicename,coin")->where(["id"=>$uid])->find();;
        $this->assign("user",$user);
        
        
        /* vip */
        $vip_list=M("vip")->order("orderno asc")->select();
        $this->assign("long",$this->long);
        $this->assign("vip_list",$vip_list);
        
        /* 用户VIP */
		$nowtime=time();
		$Users_vip=M("users_vip");
		
        $vip_txt='开通';
        $where['uid']=$uid;
        $where['endtime']=['gt',$nowtime];
		$uservip=$Users_vip->where($where)->find();
        if($uservip){
            $vip_txt='续费';
            $uservip['endtime']=date("Y.m.d",$uservip['endtime']);
        }
		$this->assign("uservip",$uservip);
		$this->assign("vip_txt",$vip_txt);
        
        /* 靓号 */
        $Liang=M("liang");
		$liang_list=$Liang->where("status=0")->order("orderno asc,id desc")->limit(21)->select();
        foreach($liang_list as $k=>$v){
            
            $liang_list[$k]['coin_date']=number_format($v['coin']).$this->config['name_coin'];
            
        }

		$this->assign("liang_list",$liang_list);
        
        /* 坐骑 */
        $car_key='carinfo';
        $car_list=getcaches($car_key);
        if(!$car_list){
            $car_list=M("car")->order("orderno asc")->select();
            if($car_list){
                setcaches($car_key,$car_list);
            }
        }
        
        foreach($car_list as $k=>$v){
            $car_list[$k]['thumb']=get_upload_path($v['thumb']);
            $car_list[$k]['swf']=get_upload_path($v['swf']);
        }
        
        $this->assign("car_list",$car_list);
		
		$this->display();
	    
	}

    /* 购买VIP */
	function buyvip(){
		$uid=(int)I("uid");
		$token=I("token");
		$vipid=(int)I("vipid");
		
		$rs=array('code'=>0,'info'=>array(),'msg'=>'购买成功');
		
		if( !$uid || !$token || checkToken($uid,$token)==700){
			$rs['code']=700;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			echo json_encode($rs);
			exit;
		} 

		$vipinfo=M("vip")->where(["id"=>$vipid])->find();
		if(!$vipinfo){
			$rs['code']=1001;
			$rs['msg']='VIP信息错误';
			echo json_encode($rs);
			exit;
		}
		$User=M('users');

		$total=$vipinfo['coin'];
		$giftid=$vipinfo['id'];
		$addtime=time();
		$showid=0;
		$giftcount=$vipinfo['length'];

        /* 更新用户余额 消费 */
		$ifok=M()->execute("update __PREFIX__users set coin=coin-{$total},consumption=consumption+{$total} where id='{$uid}' and coin>={$total}");
		if(!$ifok){
            $rs['code']=1002;
			$rs['msg']='余额不足';
			echo json_encode($rs);
			exit;
        }
        
        setAgentProfit($uid,$total);
        
		$endtime=$addtime+60*60*24*30*$giftcount;
           
		$Users_vip=M("users_vip");
		
		$uservip=$Users_vip->where(["uid"=>$uid])->find();
		
		if($uservip){
			if($uservip['endtime'] > $addtime){
                $endtime=$uservip['endtime']+60*60*24*30*$giftcount;
			}
			$data=array(
				'endtime'=>$endtime,
			);
			$Users_vip->where(["uid"=>$uid])->save($data);
		}else{
			
			$data=array(
				'uid'=>$uid,
				'addtime'=>$addtime,
				'endtime'=>$endtime,
			);
			$Users_vip->add($data);
		}
		
        

		/* 添加记录 */
		M("users_coinrecord")->add(array("type"=>'expend',"action"=>'buyvip',"uid"=>$uid,"touid"=>$uid,"giftid"=>$giftid,"giftcount"=>$giftcount,"totalcoin"=>$total,"showid"=>$showid,"addtime"=>$addtime ));	
		
		$result=date("Y.m.d",$endtime);
		
		$key='vip_'.$uid;
		$isexist=M("users_vip")->where(["uid"=>$uid])->find();		
		if($isexist){
			setcaches($key,$isexist);
		}
        
        $userinfo=$User->field("coin")->where(["id"=>$uid])->find();

		$rs['info']['endtime']=$result;
		$rs['info']['coin']=$userinfo['coin'];
		echo json_encode($rs);
		exit;
	}   
    
    /* 靓号加载更多 */
    function getliangmore(){
        
        $rs=array('code'=>0,'info'=>array(),'msg'=>'');
        
        $p=(int)I("p");
        if(!$p){
            $p=1;
        }
        $nums=21;
        $start=($p-1) * $nums;
        $isscroll=1;
        
        $Liang=M("liang");
		$liang_list=$Liang->where("status=0")->order("orderno asc,id desc")->limit($start,$nums)->select();
        foreach($liang_list as $k=>$v){
            $liang_list[$k]['coin_date']=number_format($v['coin']).$this->config['name_coin']; 
        }      

        $list_num=count($liang_list);
        
        if($list_num < $nums){
            $isscroll=0;
        }
        
        $rs['info']['list']=$liang_list;
        $rs['info']['nums']=$list_num;
        $rs['info']['isscroll']=$isscroll;

        echo json_encode($rs);
        exit;
    }
    /* 购买靓号 */
    function buyliang(){
		$uid=(int)I("uid");
		$token=I("token");
		$liangid=(int)I("liangid");
		
		$rs=array('code'=>0,'info'=>array(),'msg'=>'购买成功');
		
		if( !$uid || !$token || checkToken($uid,$token)==700){
			$rs['code']=700;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			echo json_encode($rs);
			exit;
		} 
		$Liang=M("liang");
		
		$lianginfo=$Liang->where(["id"=>$liangid])->find();
		if(!$lianginfo){
			$rs['code']=1001;
			$rs['msg']='靓号信息错误';
			echo json_encode($rs);
			exit;
		}
		
		if($lianginfo['status']==1){
			$rs['code']=1003;
			$rs['msg']='该靓号已出售';
			echo json_encode($rs);
			exit;
		}
		if($lianginfo['status']==2){
			$rs['code']=1003;
			$rs['msg']='该靓号已下架';
			echo json_encode($rs);
			exit;
		}
		
		
		
		$total=$lianginfo['coin'];
		$giftid=$lianginfo['id'];
		$addtime=time();
		$showid=0;
		$giftcount=1;

		/* 更新用户余额 消费 */
		$ifok=M()->execute("update __PREFIX__users set coin=coin-{$total},consumption=consumption+{$total} where id='{$uid}' and coin >= {$total}");
        if(!$ifok){
            $rs['code']=1002;
			$rs['msg']='余额不足';
			echo json_encode($rs);
			exit;
        }
        setAgentProfit($uid,$total);
        
		/* 添加记录 */
		M("users_coinrecord")->add(array("type"=>'expend',"action"=>'buyliang',"uid"=>$uid,"touid"=>$uid,"giftid"=>$giftid,"giftcount"=>$giftcount,"totalcoin"=>$total,"showid"=>$showid,"addtime"=>$addtime ));
		
		$data=array(
			'uid'=>$uid,
			'status'=>1,
			'buytime'=>$addtime,
		);
		$lianginfo=$Liang->where(["id"=>$liangid])->save($data);
        $User=M('users');
        $userinfo=$User->field("coin")->where(["id"=>$uid])->find();
		
		//$rs['msg']='您已成功购买'.$carinfo['name'].'坐骑，请前往“装备中心”进行查看';
        $rs['info']['coin']=$userinfo['coin'];
		echo json_encode($rs);
		exit;
	}

    /* 购买坐骑 */
    function buycar(){
		$uid=(int)I("uid");
		$token=I("token");
		$carid=(int)I("carid");
		
		$rs=array('code'=>0,'info'=>array(),'msg'=>'购买成功');
		
		if( !$uid || !$token || checkToken($uid,$token)==700){
			$rs['code']=700;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			echo json_encode($rs);
			exit;
		} 

		$carinfo=M("car")->where(["id"=>$carid])->find();
		if(!$carinfo){
			$rs['code']=1001;
			$rs['msg']='坐骑信息错误';
			echo json_encode($rs);
			exit;
		}
		
		$User=M('users');
		
		$total=$carinfo['needcoin'];
		$giftid=$carinfo['id'];
		$addtime=time();
		$showid=0;
		$giftcount=1;

		/* 更新用户余额 消费 */
		$ifok=M()->execute("update __PREFIX__users set coin=coin-{$total},consumption=consumption+{$total} where id='{$uid}' and coin >= {$total}");
        if(!$ifok){
            $rs['code']=1002;
			$rs['msg']='余额不足';
			echo json_encode($rs);
			exit;
        }
        
        setAgentProfit($uid,$total);
        
		$endtime=$addtime+60*60*24*30*$giftcount;
		$Users_car=M("users_car");
		
		$usercar=$Users_car->where(["uid"=>$uid, "carid"=>$carid])->find();
		
		if($usercar){
			if($usercar['endtime'] > $addtime){
				$endtime=$usercar['endtime']+60*60*24*30*$giftcount;
			}
			$data=array(
				'endtime'=>$endtime,
			);
			$Users_car->where(["id"=>$usercar['id']])->save($data);
		}else{
			$data=array(
				'uid'=>$uid,
				'addtime'=>$addtime,
				'endtime'=>$endtime,
				'carid'=>$carid,
			);
			$Users_car->add($data);
		}
		
		

		/* 添加记录 */
		M("users_coinrecord")->add(array("type"=>'expend',"action"=>'buycar',"uid"=>$uid,"touid"=>$uid,"giftid"=>$giftid,"giftcount"=>$giftcount,"totalcoin"=>$total,"showid"=>$showid,"addtime"=>$addtime ));	
        
        $userinfo=$User->field("coin")->where(["id"=>$uid])->find();
		//$rs['msg']='您已成功购买'.$carinfo['name'].'坐骑，请前往“装备中心”进行查看';
        $rs['info']['coin']=$userinfo['coin'];
		echo json_encode($rs);
		exit;
	}
}