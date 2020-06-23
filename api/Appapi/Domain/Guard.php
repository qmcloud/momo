<?php

class Domain_Guard {
	public function getGuardList($data) {
		$rs = array();

		$model = new Model_Guard();
		$rs = $model->getGuardList($data);

		return $rs;
	}

	public function getList() {
		$rs = array();

		$model = new Model_Guard();
		$rs = $model->getList();

		return $rs;
	}

	public function buyGuard($data) {
		$rs = array();

		$model = new Model_Guard();
		$rs = $model->buyGuard($data);

		return $rs;
	}

	public function getUserGuard($uid,$liveuid) {
		$rs = array();

		$model = new Model_Guard();
		$rs = $model->getUserGuard($uid,$liveuid);

		return $rs;
	}

	public function getGuardNums($liveuid) {
		$rs = array();

		$model = new Model_Guard();
		$rs = $model->getGuardNums($liveuid);

		return $rs;
	}
	
}
