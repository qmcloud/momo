<?php

class Model_Turntable extends PhalApi_Model_NotORM {
    
    /* 配置 */
    public function getConfig(){
        $list=DI()->notorm->turntable_con
                ->select('id,times,coin')
                ->order('orderno asc,id asc')
                ->fetchAll();
        return $list;
    }
    
    /* 奖品信息 */
    public function getTurntables(){
        $list=DI()->notorm->turntable
                ->select('id,type,type_val,thumb,rate')
                ->order('id asc')
                ->fetchAll();
        return $list;
    }
    
	/* 转盘纪录 */
	public function setlog($data) {
		$rs=DI()->notorm->turntable_log->insert($data);

		return $rs;
	}

	/* 转盘纪录 */
	public function uplogwin($id,$iswin) {
		$rs=DI()->notorm->turntable_log
                ->where('id=?',$id)
                ->update(['iswin'=>$iswin]);

		return $rs;
	}
    
    /* 添加中奖纪录 */
	public function setWin($data) {
        
		$rs=DI()->notorm->turntable_win->insert($data);

		return $rs;
	}

	/* 中奖纪录 */
	public function getWin($uid,$p) {
        
        if($p<1){
            $p=1;
        }
        
		$pnum=50;
		$start=($p-1)*$pnum;
        
		$list=DI()->notorm->turntable_win
                    ->select('type,type_val,nums,thumb,addtime')
                    ->where('uid=?',$uid)
                    ->order('id desc')
                    ->limit($start,$pnum)
                    ->fetchAll();

		return $list;
	}
    


}
