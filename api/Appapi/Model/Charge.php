<?php

class Model_Charge extends PhalApi_Model_NotORM {
	/* 订单号 */
	public function getOrderId($changeid,$orderinfo) {
		
		$charge=DI()->notorm->charge_rules->select('*')->where('id=?',$changeid)->fetchOne();
		
		if(!$charge || $charge['money']!=$orderinfo['money'] || ($charge['coin']!=$orderinfo['coin']  && $charge['coin_ios']!=$orderinfo['coin'] )){
			return 1003;
		}
		
		$orderinfo['coin_give']=$charge['give'];
		

		$result= DI()->notorm->users_charge->insert($orderinfo);

		return $result;
	}			

}
