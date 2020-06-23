<?php

class Model_Livepk extends PhalApi_Model_NotORM {
	/* 直播中用户列表 */
	public function getLiveList($uid,$where,$p) {
		if($p<1){
            $p=1;
        }
        $pnum=50;
		$start=($p-1)*$pnum;
        
        $list=DI()->notorm->users_live
                ->select('uid,stream,pkuid')
                ->where('islive=1 and isvideo=0')
                ->where($where)
                ->order('starttime desc')
                ->limit($start,$pnum)
                ->fetchAll();
        foreach($list as $k=>$v){
            $userinfo=getUserInfo($v['uid']);
            $v['avatar']=$userinfo['avatar'];
			$v['avatar_thumb']=$userinfo['avatar_thumb'];
			$v['user_nicename']=$userinfo['user_nicename'];
            
            $list[$k]=$v;
        }
        
		return $list;
	}		


	/* 直播中用户列表 */
	public function checkLive($stream) {
		
        $isexist=DI()->notorm->users_live
                ->select('uid')
                ->where('islive=1 and isvideo=0 and stream=?',$stream)
                ->fetchOne();
        if($isexist){
            return true;
        }
        
		return false;
	}	


	/* 更新连麦用户信息 */
	public function changeLive($uid,$pkuid,$type) {

        if($type == 1){
            /* 连麦 */
            $uid_live=DI()->notorm->users_live
                ->select('uid,stream,pkuid')
                ->where('islive=1 and isvideo=0 and uid=?',$uid)
                ->fetchOne();
            
            $pkuid_live=DI()->notorm->users_live
                ->select('uid,stream,pkuid')
                ->where('islive=1 and isvideo=0 and uid=?',$pkuid)
                ->fetchOne();

            if($uid_live && $pkuid_live && $uid_live['pkuid']==0 && $pkuid_live['pkuid']==0){
                DI()->notorm->users_live
                ->where(" uid={$uid} ")
                ->update( array('pkuid'=>$pkuid_live['uid'],'pkstream'=>$pkuid_live['stream']) );
                
                DI()->notorm->users_live
                ->where(" uid={$pkuid} ")
                ->update( array('pkuid'=>$uid_live['uid'],'pkstream'=>$uid_live['stream']) );
                
            }    
            
        }else{
            /* 断麦 */
            DI()->notorm->users_live
                ->where(" uid={$uid}  or pkuid={$uid}")
                ->update( array('pkuid'=>0,'pkstream'=>'') );
        }


		return $rs;
	}			
	

}
