<?php

class Model_Linkmic extends PhalApi_Model_NotORM {
	/* 设置连麦开关 */
	public function setMic($uid,$ismic) {

        $result=DI()->notorm->users_live
                ->where('uid=?',$uid)
                ->update( ['ismic'=>$ismic] );
        
		return $result;
	}		


	/* 判断主播连麦开关 */
	public function isMic($liveuid) {
		
        $isexist=DI()->notorm->users_live
                ->select('ismic')
                ->where('uid=?',$liveuid)
                ->fetchOne();
        if($isexist && $isexist['ismic']){
            return 1;
        }
        
		return 0;
	}	

	

}
