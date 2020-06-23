<?php

class Domain_Livepk {
	public function getLiveList($uid,$where,$p) {
		$rs = array();

		$model = new Model_Livepk();
		$rs = $model->getLiveList($uid,$where,$p);

		return $rs;
	}

	public function checkLive($stream) {
		$rs = array();

		$model = new Model_Livepk();
		$rs = $model->checkLive($stream);

		return $rs;
	}

	public function changeLive($uid,$pkuid,$type) {
		$rs = array();

		$model = new Model_Livepk();
		$rs = $model->changeLive($uid,$pkuid,$type);

		return $rs;
	}

	
}
