<?php

class Domain_Backpack {
	public function getBackpack($uid) {

		$model = new Model_Backpack();
		$list = $model->getBackpack($uid);
        
        $domain = new Domain_Live();
        $giftlist=$domain->getGiftList();
        
        foreach($list as $k=>$v){
            foreach($giftlist as $k2=>$v2){
                if($v['giftid']==$v2['id']){
                    $v2['nums']=$v['nums'];
                    
                    $v=$v2;
                    break;
                }
            }
            $list[$k]=$v;
        }

		return $list;
	}

	public function addBackpack($uid,$giftid,$nums) {
		$rs = array();

		$model = new Model_Backpack();
		$rs = $model->addBackpack($uid,$giftid,$nums);

		return $rs;
	}

	public function reduceBackpack($uid,$giftid,$nums) {
		$rs = array();

		$model = new Model_Backpack();
		$rs = $model->reduceBackpack($uid,$giftid,$nums);

		return $rs;
	}
	
}
