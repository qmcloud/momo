<?php

class Domain_User {

	public function getBaseInfo($userId) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->getBaseInfo($userId);

			return $rs;
	}
	
	public function checkName($uid,$name) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->checkName($uid,$name);

			return $rs;
	}
	
	public function userUpdate($uid,$fields) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->userUpdate($uid,$fields);

			return $rs;
	}
	
	public function updatePass($uid,$oldpass,$pass) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->updatePass($uid,$oldpass,$pass);

			return $rs;
	}

	public function getBalance($uid) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->getBalance($uid);

			return $rs;
	}
	
	public function getChargeRules() {
			$rs = array();

			$model = new Model_User();
			$rs = $model->getChargeRules();

			return $rs;
	}
	
	public function getProfit($uid) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->getProfit($uid);

			return $rs;
	}

	public function setCash($data) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->setCash($data);

			return $rs;
	}
	
	public function setAttent($uid,$touid) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->setAttent($uid,$touid);

			return $rs;
	}
	
	public function setBlack($uid,$touid) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->setBlack($uid,$touid);

			return $rs;
	}
	
	public function getFollowsList($uid,$touid,$p) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->getFollowsList($uid,$touid,$p);

			return $rs;
	}
	
	public function getFansList($uid,$touid,$p) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->getFansList($uid,$touid,$p);

			return $rs;
	}

	public function getBlackList($uid,$touid,$p) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->getBlackList($uid,$touid,$p);

			return $rs;
	}

	public function getLiverecord($touid,$p) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->getLiverecord($touid,$p);

			return $rs;
	}
	
	public function getUserHome($uid,$touid) {
		$rs = array();

		$model = new Model_User();
		$rs = $model->getUserHome($uid,$touid);
		return $rs;
	}	
	
	public function getContributeList($touid,$p) {
		$rs = array();

		$model = new Model_User();
		$rs = $model->getContributeList($touid,$p);
		return $rs;
	}	
	
	public function setDistribut($uid,$code) {
		$rs = array();

		$model = new Model_User();
		$rs = $model->setDistribut($uid,$code);
		return $rs;
	}

	public function getImpressionLabel() {
        $rs = array();
                
        $model = new Model_User();
        $rs = $model->getImpressionLabel();

        return $rs;
    }	

	public function getUserLabel($uid,$touid) {
        $rs = array();
                
        $model = new Model_User();
        $rs = $model->getUserLabel($uid,$touid);

        return $rs;
    }	

	public function setUserLabel($uid,$touid,$labels) {
        $rs = array();
                
        $model = new Model_User();
        $rs = $model->setUserLabel($uid,$touid,$labels);

        return $rs;
    }	

	public function getMyLabel($uid) {
        $rs = array();
                
        $model = new Model_User();
        $rs = $model->getMyLabel($uid);

        return $rs;
    }	

	public function getPerSetting() {
        $rs = array();
                
        $model = new Model_User();
        $rs = $model->getPerSetting();

        return $rs;
    }	

	public function getUserAccountList($uid) {
        $rs = array();
                
        $model = new Model_User();
        $rs = $model->getUserAccountList($uid);

        return $rs;
    }	

	public function setUserAccount($data) {
        $rs = array();
                
        $model = new Model_User();
        $rs = $model->setUserAccount($data);

        return $rs;
    }

	public function delUserAccount($data) {
        $rs = array();
                
        $model = new Model_User();
        $rs = $model->delUserAccount($data);

        return $rs;
    }	
	
	public function LoginBonus($uid){
		$rs = array();
		$model = new Model_User();
		$rs = $model->LoginBonus($uid);
		return $rs;

	}

	public function getLoginBonus($uid){
		$rs = array();
		$model = new Model_User();
		$rs = $model->getLoginBonus($uid);
		return $rs;

	}

	public function checkIsAgent($uid){
		$rs = array();
		$model = new Model_User();
		$rs = $model->checkIsAgent($uid);
		return $rs;
	}
    
    
}
