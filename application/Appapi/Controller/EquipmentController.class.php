<?php
/**
 * 装备中心
 */
namespace Appapi\Controller;
use Common\Controller\HomebaseController;
class EquipmentController extends HomebaseController {

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
        
        
		/* 靓号信息 */
		$Liang=M("liang");
		$liang_list=$Liang->where(["uid"=>$uid])->order("buytime desc")->select();

		$this->assign("liang_list",$liang_list);
        
        
		/* 坐骑信息 */
        $car_key='carinfo';
        $car_list=getcaches($car_key);
        if(!$car_list){
            $car_list=M("car")->order("orderno asc")->select();
            if($car_list){
                setcaches($car_key,$car_list);
            }
        }
        
        foreach($car_list as $k=>$v){
            $v['thumb']=get_upload_path($v['thumb']);
            $v['swf']=get_upload_path($v['swf']);
            
            $carlist2[$v['id']]=$v;
        }

		/* 用户坐骑 */
		$nowtime=time();
		$Car_u=M("users_car");
        $where['uid']=$uid;
        $where['endtime']=['gt',$nowtime];
		$user_carlist=$Car_u->where($where)->select();
		
		foreach($user_carlist as $k=>$v){
			if($carlist2[$v['carid']]){
				$user_carlist[$k]['carinfo']=$carlist2[$v['carid']];
				$user_carlist[$k]['endtime_date']=date("Y-m-d",$v['endtime']);
			}else{
				unset($user_carlist[$k]);
			}
		}

		$this->assign("user_carlist",$user_carlist);

		
		
		$this->display();
	    
	}
    
    /* 设置靓号 */
	function setliang(){
		$rs=array('code'=>0,'info'=>array(),'msg'=>'更换成功');
		$uid=(int)I("uid");
		$token=I("token");
		$liangid=(int)I("liangid");
		$state=I("state");

		
		if( !$uid || !$token || checkToken($uid,$token)==700){
			$rs['code']=700;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			echo json_encode($rs);
			exit;
		} 
		$Liang=M("liang");
        
        $isexist=$Liang->where(["uid"=>$uid, "id"=>$liangid])->find();
        if(!$isexist){
            $rs['code']=1001;
			$rs['msg']='信息错误';
			echo json_encode($rs);
			exit;
        }
        
		$Liang->where(["uid"=>$uid])->save(array('state'=>0) );
		
		$setstatus=$state?0:1;
		$data=array(
			'state'=>$setstatus,
		);
		$list=$Liang->where(["uid"=>$uid, "id"=>$liangid])->save( $data );
		$Users=M("users");
		
        $goodnum=$isexist['name'];
		$key='liang_'.$uid;
		if($setstatus==1){
			$Users->where(["id"=>$uid])->setField("goodnum",$goodnum);
			
			$isexist=M("liang")->where(["uid"=>$uid, "status"=>1, "state"=>1])->find();
			if($isexist){
				setcaches($key,$isexist);
			}
            
		}else{
			$Users->where(["id"=>$uid])->setField("goodnum",0);
			delcache($key);
		}
		
		echo json_encode($rs);
		exit;
	}
    
	/* 装备坐骑 */
	function setcar(){
		$uid=(int)I("uid");
		$token=I("token");
		$carid=(int)I("carid");
		$status=I("status");
		
		$rs=array('code'=>0,'info'=>array(),'msg'=>'更换成功');
		
		if( !$uid || !$token || checkToken($uid,$token)==700){
			$rs['code']=700;
			$rs['msg']='您的登陆状态失效，请重新登陆！';
			echo json_encode($rs);
			exit;
		} 
		$setstatus=$status?0:1;
		
		$Car_u=M("users_car");
        
        
        $isexist=$Car_u->where(["uid"=>$uid,"carid"=>$carid])->find();
        if(!$isexist){
            $rs['code']=1001;
			$rs['msg']='信息错误';
			echo json_encode($rs);
			exit;
        }

		$data1=array(
			'status'=>0,
		);
		$Car_u->where(["uid"=>$uid])->save($data1);

		
		$data=array(
			'status'=>$setstatus,
		);
		$result=$Car_u->where(["uid"=>$uid,"carid"=>$carid])->save($data);
		
		
		$key='car_'.$uid;
		if($setstatus){
			$isexist=M("users_car")->where(["uid"=>$uid,"status"=>1])->find();
			if($isexist){
				setcaches($key,$isexist);
			}
		}else{
			delcache($key);
        }
        
		echo json_encode($rs);
		exit;
		
	}

}