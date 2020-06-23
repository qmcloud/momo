<?php

class Domain_Music {

    public function classify_list() {
        $rs = array();

        $model = new Model_Music();
        $rs = $model->classify_list();

        return $rs;
    }

   	public function music_list($classify,$uid,$p){

        $rs = array();

        $model = new Model_Music();
        $rs = $model->music_list($classify,$uid,$p);

        return $rs;
    }

    public function searchMusic($keywords,$uid,$p){
        $rs = array();

        $model = new Model_Music();
        $rs = $model->searchMusic($keywords,$uid,$p);

        return $rs;
    }

    public function collectMusic($uid,$musicid){
        $rs = array();

        $model = new Model_Music();
        $rs = $model->collectMusic($uid,$musicid);

        return $rs;
    }

    public function getCollectMusicLists($uid,$p){
        $rs = array();

        $model = new Model_Music();
        $rs = $model->getCollectMusicLists($uid,$p);

        return $rs;
    }

    public function hotLists($uid){
        $rs = array();

        $model = new Model_Music();
        $rs = $model->hotLists($uid);

        return $rs;
    }

}
