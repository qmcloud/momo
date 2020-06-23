<?php

class Model_Livemanage extends PhalApi_Model_NotORM {
	/* 我的管理员 */
	public function getManageList($uid) {
        
        $rs=[
            'nums'=>'0',
            'total'=>'5',
            'list'=>[],
        ];
		
        $nums=DI()->notorm->users_livemanager
            ->where('liveuid=?',$uid)
            ->count();
            
        $list=DI()->notorm->users_livemanager
            ->select('uid')
            ->where('liveuid=?',$uid)
            ->fetchAll();
            
        foreach($list as $k=>$v){
            
            $userinfo=getUserInfo($v['uid']);
            
            $v['user_nicename']=$userinfo['user_nicename'];
            $v['avatar']=$userinfo['avatar'];
            $v['avatar_thumb']=$userinfo['avatar_thumb'];
            $v['sex']=$userinfo['sex'];
            $v['level']=$userinfo['level'];

            $list[$k]=$v;
        }

        $rs['nums']=(string)$nums;
        $rs['list']=$list;
        
		return $rs;
	}
    
    /* 解除管理 */
	public function cancelManage($uid,$touid) {
        
        $rs=DI()->notorm->users_livemanager
            ->where('liveuid=? and uid=?',$uid,$touid)
            ->delete();
            
		return $rs;
	}

	/* 我的房间 */
	public function getRoomList($uid) {

            
        $list=DI()->notorm->users_livemanager
            ->select('liveuid')
            ->where('uid=?',$uid)
            ->fetchAll();
            
        foreach($list as $k=>$v){
            
            $userinfo=getUserInfo($v['liveuid']);
            
            $v['user_nicename']=$userinfo['user_nicename'];
            $v['avatar']=$userinfo['avatar'];
            $v['avatar_thumb']=$userinfo['avatar_thumb'];
            $v['sex']=$userinfo['sex'];
            $v['level']=$userinfo['level'];

            $list[$k]=$v;
        }

        
		return $list;
	}

	/* 禁言用户 */
	public function getShutList($liveuid) {
        

            
        $list=DI()->notorm->live_shut
            ->select('uid')
            ->where('liveuid=?',$liveuid)
            ->order('id desc')
            ->fetchAll();
            
        foreach($list as $k=>$v){
            
            $userinfo=getUserInfo($v['uid']);
            
            $v['user_nicename']=$userinfo['user_nicename'];
            $v['avatar']=$userinfo['avatar'];
            $v['avatar_thumb']=$userinfo['avatar_thumb'];
            $v['sex']=$userinfo['sex'];
            $v['level']=$userinfo['level'];

            $list[$k]=$v;
        }

        
		return $list;
	}

	/* 解除禁言 */
	public function cancelShut($liveuid,$touid) {
        
        $rs=DI()->notorm->live_shut
            ->where('liveuid=? and uid=?',$liveuid,$touid)
            ->delete();
            
        DI()->redis -> hDel($liveuid . 'shutup',$touid);
        
		return $rs;
	}

	/* 踢人用户 */
	public function getKickList($liveuid) {
        

            
        $list=DI()->notorm->live_kick
            ->select('uid')
            ->where('liveuid=?',$liveuid)
            ->order('id desc')
            ->fetchAll();
            
        foreach($list as $k=>$v){
            
            $userinfo=getUserInfo($v['uid']);
            
            $v['user_nicename']=$userinfo['user_nicename'];
            $v['avatar']=$userinfo['avatar'];
            $v['avatar_thumb']=$userinfo['avatar_thumb'];
            $v['sex']=$userinfo['sex'];
            $v['level']=$userinfo['level'];

            $list[$k]=$v;
        }

        
		return $list;
	}
    
	/* 解除踢人 */
	public function cancelKick($liveuid,$touid) {
        
        $rs=DI()->notorm->live_kick
            ->where('liveuid=? and uid=?',$liveuid,$touid)
            ->delete();
            
		return $rs;
	}

}
