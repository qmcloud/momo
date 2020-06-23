<?php

class Domain_Linkmic {

    public function setMic($uid,$ismic) {
        $rs = array();

        $model = new Model_Linkmic();
        $rs = $model->setMic($uid,$ismic);

        return $rs;
    }

   	public function isMic($liveuid){

        $rs = array();

        $model = new Model_Linkmic();
        $rs = $model->isMic($liveuid);

        return $rs;
    }

}
