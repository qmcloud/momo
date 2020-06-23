<?php

class Domain_Guide {
	public function getGuide() {
		$rs = array();

		$model = new Model_Guide();
		$rs = $model->getGuide();

		return $rs;
	}
	
}
