<?php

class Domain_Home {

    public function getSlide() {
        $rs = array();
        $model = new Model_Home();
        $rs = $model->getSlide();
        return $rs;
    }
		
	public function getHot($p) {
        $rs = array();

        $model = new Model_Home();
        $rs = $model->getHot($p);
				
        return $rs;
    }
		
	public function getFollow($uid,$p) {
        $rs = array();
				
        $model = new Model_Home();
        $rs = $model->getFollow($uid,$p);
				
        return $rs;
    }
		
	public function getNew($lng,$lat,$p) {
        $rs = array();

        $model = new Model_Home();
        $rs = $model->getNew($lng,$lat,$p);
				
        return $rs;
    }
		
	public function search($uid,$key,$p) {
        $rs = array();

        $model = new Model_Home();
        $rs = $model->search($uid,$key,$p);
				
        return $rs;
    }
	
	public function getNearby($lng,$lat,$p) {
        $rs = array();

        $model = new Model_Home();
        $rs = $model->getNearby($lng,$lat,$p);
				
        return $rs;
    }
	
	public function getRecommend() {
        $rs = array();

        $model = new Model_Home();
        $rs = $model->getRecommend();
				
        return $rs;
    }
	
	public function attentRecommend($uid,$touid) {
        $rs = array();

        $model = new Model_Home();
        $rs = $model->attentRecommend($uid,$touid);
				
        return $rs;
    }

    public function profitList($uid,$type,$p){
        $rs = array();

        $model = new Model_Home();
        $rs = $model->profitList($uid,$type,$p);
                
        return $rs;
    }

    public function consumeList($uid,$type,$p){
        $rs = array();

        $model = new Model_Home();
        $rs = $model->consumeList($uid,$type,$p);
                
        return $rs;
    }

    public function getClassLive($liveclassid,$p){
        $rs = array();

        $model = new Model_Home();
        $rs = $model->getClassLive($liveclassid,$p);
                
        return $rs;
    }

}
