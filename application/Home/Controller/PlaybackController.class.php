<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace Home\Controller;
use Common\Controller\HomebaseController; 
class PlaybackController extends HomebaseController {
	public function index() {
		$touid=I('touid');
        if(!$touid){
            $this->error('参数错误');
        }
        $where['uid']=$touid;
		$liverecord=M("users_liverecord")->where($where)->select();
		foreach($liverecord as $k=>$v)
		{
			$time=$liverecord['endtime']-$liverecord['starttime'];
			$liverecord[$k]['time']=getSeconds($time,1);
		}
		$this->assign("liverecord",$liverecord);
		$this->display();
	}
}