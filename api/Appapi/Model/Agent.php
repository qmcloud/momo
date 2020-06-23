<?php

class Model_Agent extends PhalApi_Model_NotORM {
	/* 引导页 */
	public function getCode($uid) {
		
        $agentinfo=DI()->notorm->users_agent_code
            ->select('code')
            ->where('uid=?',$uid)
            ->fetchOne();
            
		return $agentinfo;
	}			

}
