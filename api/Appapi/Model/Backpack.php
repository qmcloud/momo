<?php

class Model_Backpack extends PhalApi_Model_NotORM {
	/* 背包礼物 */
	public function getBackpack($uid) {
		
        $list=DI()->notorm->backpack
            ->select('giftid,nums')
            ->where('uid=? and nums>0',$uid)
            ->fetchAll();

		return $list;
	}
    
    /* 添加背包礼物 */
	public function addBackpack($uid,$giftid,$nums) {

        $rs=DI()->notorm->backpack
                ->where('uid=? and giftid=?',$uid,$giftid)
                ->update(array('nums'=> new NotORM_Literal("nums + {$nums} ")));
        if(!$rs){
            $rs=DI()->notorm->backpack
                ->insert(array( 'uid'=>$uid, 'giftid'=>$giftid, 'nums'=>$nums ));
        }

		return $rs;
	}

    /* 减少背包礼物 */
	public function reduceBackpack($uid,$giftid,$nums) {

        $rs=DI()->notorm->backpack
                ->where('uid=? and giftid=? and nums>=?',$uid,$giftid,$nums)
                ->update(array('nums'=> new NotORM_Literal("nums - {$nums} ")));

		return $rs;
	}

}
