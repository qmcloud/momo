<?php

class Domain_Live {
	
	public function checkBan($uid) {
		$rs = array();

		$model = new Model_Live();
		$rs = $model->checkBan($uid);
		return $rs;
	}

	public function createRoom($uid,$data) {
		$rs = array();

		$model = new Model_Live();
		$rs = $model->createRoom($uid,$data);
		return $rs;
	}
	
	public function getFansIds($touid) {
		$rs = array();

		$model = new Model_Live();
		$rs = $model->getFansIds($touid);
		return $rs;
	}
	
	public function changeLive($uid,$stream,$status) {
		$rs = array();

		$model = new Model_Live();
		$rs = $model->changeLive($uid,$stream,$status);
		return $rs;
	}
	
	public function changeLiveType($uid,$stream,$data) {
		$rs = array();

		$model = new Model_Live();
		$rs = $model->changeLiveType($uid,$stream,$data);
		return $rs;
	}

	public function stopRoom($uid,$stream) {
		$rs = array();

		$model = new Model_Live();
		$rs = $model->stopRoom($uid,$stream);
		return $rs;
	}
	
	public function stopInfo($stream) {
		$rs = array();

		$model = new Model_Live();
		$rs = $model->stopInfo($stream);
		return $rs;
	}
	
	public function checkLive($uid,$liveuid,$stream) {
		$rs = array();

		$model = new Model_Live();
		$rs = $model->checkLive($uid,$liveuid,$stream);
		return $rs;
	}
	
	public function roomCharge($uid,$token,$liveuid,$stream) {
		$rs = array();

		$model = new Model_Live();
		$rs = $model->roomCharge($uid,$token,$liveuid,$stream);
		return $rs;
	}
	
	public function getUserCoin($uid) {
		$rs = array();

		$model = new Model_Live();
		$rs = $model->getUserCoin($uid);
		return $rs;
	}
	
	public function isZombie($uid) {
		$rs = array();

		$model = new Model_Live();
		$rs = $model->isZombie($uid);
		return $rs;
	}
	
	public function getZombie($stream,$where) {
        $rs = array();
				
        $model = new Model_Live();
        $rs = $model->getZombie($stream,$where);

        return $rs;
    }	

	public function getPop($touid) {
		$rs = array();

		$model = new Model_Live();
		$rs = $model->getPop($touid);
		return $rs;
	}

	public function getGiftList() {

        $key='getGiftList';
		$list=getcaches($key);

		if(!$list){
			$model = new Model_Live();
            $list = $model->getGiftList();
            if($list){
                setcaches($key,$list);
            }
		}
        
        foreach($list as $k=>$v){
			$list[$k]['gifticon']=get_upload_path($v['gifticon']);
		}	
        
		return $list;
	}
	
	public function sendGift($uid,$liveuid,$stream,$giftid,$giftcount,$ispack) {
		$rs = array();

		$model = new Model_Live();
		$rs = $model->sendGift($uid,$liveuid,$stream,$giftid,$giftcount,$ispack);
		return $rs;
	}

	public function sendBarrage($uid,$liveuid,$stream,$giftid,$giftcount,$content) {
		$rs = array();

		$model = new Model_Live();
		$rs = $model->sendBarrage($uid,$liveuid,$stream,$giftid,$giftcount,$content);
		return $rs;
	}
	
	public function setAdmin($liveuid,$touid) {
		$rs = array();

		$model = new Model_Live();
		$rs = $model->setAdmin($liveuid,$touid);
		return $rs;
	}
	
	public function getAdminList($liveuid) {
		$rs = array();

		$model = new Model_Live();
		$rs = $model->getAdminList($liveuid);
		return $rs;
	}
	
	public function getUserHome($uid,$touid) {
		$rs = array();

		$model = new Model_Live();
		$rs = $model->getUserHome($uid,$touid);
		return $rs;
	}
    
	public function getReportClass() {
		$rs = array();

		$model = new Model_Live();
		$rs = $model->getReportClass();
		return $rs;
	}

	public function setReport($uid,$touid,$content) {
		$rs = array();

		$model = new Model_Live();
		$rs = $model->setReport($uid,$touid,$content);
		return $rs;
	}

	public function getVotes($liveuid) {
		$rs = array();

		$model = new Model_Live();
		$rs = $model->getVotes($liveuid);
		return $rs;
	}
    
	public function checkShut($uid,$liveuid) {
		$rs = array();
		$model = new Model_Live();
		$rs = $model->checkShut($uid,$liveuid);
		return $rs;
	}
    
    public function setShutUp($uid,$liveuid,$touid,$showid) {
		$rs = array();
		$model = new Model_Live();
		$rs = $model->setShutUp($uid,$liveuid,$touid,$showid);
		return $rs;
	}

	public function kicking($uid,$liveuid,$touid) {
		$rs = array();
		$model = new Model_Live();
		$rs = $model->kicking($uid,$liveuid,$touid);
		return $rs;
	}
    
    public function superStopRoom($uid,$token,$liveuid,$type) {
		$rs = array();
		$model = new Model_Live();
		$rs = $model->superStopRoom($uid,$token,$liveuid,$type);
		return $rs;
	}

	public function getContribut($uid,$liveuid,$showid) {
		$rs = array();
		$model = new Model_Live();
		$rs = $model->getContribut($uid,$liveuid,$showid);
		return $rs;
	}	
    
	public function checkLiveing($uid,$stream) {
		$rs = array();
		$model = new Model_Live();
		$rs = $model->checkLiveing($uid,$stream);
		return $rs;
	}	
    
    public function getLiveInfo($liveuid) {
		$rs = array();
		$model = new Model_Live();
		$rs = $model->getLiveInfo($liveuid);
		return $rs;
	}	
}
