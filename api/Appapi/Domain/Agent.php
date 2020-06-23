<?php

class Domain_Agent {
	public function getCode($uid) {
		$rs = array();

		$model = new Model_Agent();
		$rs = $model->getCode($uid);

		return $rs;
	}
	
}
