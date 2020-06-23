<?php
/**
 * 短视频
 */
namespace Appapi\Controller;
use Common\Controller\HomebaseController;
class VideoController extends HomebaseController {

	function index1(){       
		$videoid=(int)I("videoid");
		if( !$videoid ){
			$this->assign("reason",'信息错误');
			$this->display(':error');
			exit;
		} 
		$Video=M("users_video");
		$videoinfo=$Video->where(["id"=>$videoid])->find();
		
		if(!$videoinfo){
			$this->assign("reason",'视频不存在');
			$this->display(':error');
			exit;
		}
		
		$liveinfo=getUserInfo($videoinfo['uid']);
		
		$this->assign("hls",$videoinfo['href']);
		$this->assign("videoinfo",$videoinfo);
		$this->assign("liveinfo",$liveinfo);

		$this->display();
	}
	function index(){       
		$videoid=(int)I("videoid");

		if( !$videoid ){
			$this->assign("reason",'信息错误');
			$this->display(':error');
			exit;
		} 

        $Video=M("users_video");

		
		$videoinfo=$Video->where(["id"=>$videoid])->find();
		
		if(!$videoinfo){
			$this->assign("reason",'视频不存在');
			$this->display(':error');
			exit;
		}
		
		$liveinfo=getUserInfo($videoinfo['uid']);
		
		$this->assign("hls",$videoinfo['href']);
		$this->assign("videoinfo",$videoinfo);
		$this->assign("liveinfo",$liveinfo);

		$this->display();
	}


	/*更新曝光值（一小时请求一次）*/

	function updateshowval(){
		$lastid=(int)I("lastid");
		if(!$lastid){
			$lastid=0;
		}

		$limit=1000;

		$now=time();

		$effective_time=$now-1*60*60;  //当前时间往前推一小时
		$Video=M("users_video");
		//获取视频列表中可被扣除曝光值的视频列表
        $where['isdel']=0;
        $where['status']=1;
        $where['show_val']=['egt',1];
        $where['id']=['gt',$lastid];
        $where['addtime']=['elt',$effective_time];

		$video_list=$Video->where($where)->order("id asc")->limit($limit)->select();

		//获取后台配置的每小时减去的曝光值
		$configPri=getConfigPri();

		$list_nums=count($video_list);

		foreach ($video_list as $k => $v) {
			$Video->where("id={$v['id']}")->setDec('show_val',$configPri['hour_minus_val']);//曝光值减一
			$lastid=$v['id'];
		}

		if($list_nums<$limit){
			echo "NO";
            exit;  
		}

		echo 'OK-'.$lastid;
        exit;
		
		
	}
}