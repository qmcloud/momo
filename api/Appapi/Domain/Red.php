<?php

class Domain_Red {
	public function sendRed($data) {
		$rs = array();

		$model = new Model_Red();
		$rs = $model->sendRed($data);

		return $rs;
	}

	public function getRedList($liveuid,$showid) {
		$rs = array();

		$model = new Model_Red();
		$rs = $model->getRedList($liveuid,$showid);

		return $rs;
	}

	public function robRed($data) {
		$rs = array();

		$model = new Model_Red();
		$rs = $model->robRed($data);

		return $rs;
	}

	public function getRedInfo($redid) {
		$rs = array();

		$model = new Model_Red();
		$rs = $model->getRedInfo($redid);

		return $rs;
	}

	public function getRedRobList($redid) {
		$rs = array();

		$model = new Model_Red();
		$rs = $model->getRedRobList($redid);

		return $rs;
	}
	
}
