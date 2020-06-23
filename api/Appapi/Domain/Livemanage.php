<?php

class Domain_Livemanage {
	public function getManageList($uid) {
		$rs = array();

		$model = new Model_Livemanage();
		$rs = $model->getManageList($uid);

		return $rs;
	}

	public function cancelManage($uid,$touid) {
		$rs = array();

		$model = new Model_Livemanage();
		$rs = $model->cancelManage($uid,$touid);

		return $rs;
	}

	public function getRoomList($uid) {
		$rs = array();

		$model = new Model_Livemanage();
		$rs = $model->getRoomList($uid);

		return $rs;
	}

	public function getShutList($liveuid) {
		$rs = array();

		$model = new Model_Livemanage();
		$rs = $model->getShutList($liveuid);

		return $rs;
	}

	public function cancelShut($liveuid,$touid) {
		$rs = array();

		$model = new Model_Livemanage();
		$rs = $model->cancelShut($liveuid,$touid);

		return $rs;
	}

	public function getKickList($liveuid) {
		$rs = array();

		$model = new Model_Livemanage();
		$rs = $model->getKickList($liveuid);

		return $rs;
	}

	public function cancelKick($liveuid,$touid) {
		$rs = array();

		$model = new Model_Livemanage();
		$rs = $model->cancelKick($liveuid,$touid);

		return $rs;
	}
	
}
