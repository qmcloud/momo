<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $this->show('Hello World');
    }
	function test(){
		$url1=PrivateKeyA("rtmp","123",1);
		$url2=PrivateKeyA("rtmp","123",0);
		$url3=PrivateKeyA("http","123.m3u8",0);
		$rs=array(
			'push'=>$url1,
			'pull'=>$url2,
			'pull2'=>$url3,
		);
		echo json_encode($rs);
	}
}