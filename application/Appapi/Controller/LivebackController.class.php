<?php
/**
 * 直播回放
 */
namespace Appapi\Controller;
use Common\Controller\HomebaseController;
class livebackController extends HomebaseController {
	
	/* 
		回调数据格式
		{
				"channel_id": "2121_15919131751",
				"end_time": 1473125627,
				"event_type": 100,
				"file_format": "flv",
				"file_id": "9192487266581821586",
				"file_size": 9749353,
				"sign": "fef79a097458ed80b5f5574cbc13e1fd",
				"start_time": 1473135647,
				"stream_id": "2121_15919131751",
				"t": 1473126233,
				"video_id": "200025724_ac92b781a22c4a3e937c9e61c2624af7",
				"video_url": "http://200025724.vod.myqcloud.com/200025724_ac92b781a22c4a3e937c9e61c2624af7.f0.flv"
		}
	*/
	function index(){
		$request = file_get_contents("php://input");
		file_put_contents(SITE_PATH.'/data/liveback_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 callback request:'.json_encode($request)."\r\n",FILE_APPEND);
		$result = array( 'code' => 0 );    
		$data = json_decode($request, true);

		if(!$data){
			$this->callbacklog("request para json format error");
			$result['code']=4001;
			echo json_encode($result);	
			exit;
		}
		
		if(array_key_exists("t",$data)
				&& array_key_exists("sign",$data)
				&& array_key_exists("event_type",$data) 
				&& array_key_exists("stream_id",$data))
		{
			$check_t = $data['t'];
			$check_sign = $data['sign'];
			$event_type = $data['event_type'];
			$stream_id = $data['stream_id'];
		}else {
			$this->callbacklog("request para error");
			$result['code']=4002;
			echo json_encode($result);	
			exit;
		}
		/* $md5_sign = $this-> GetCallBackSign($check_t);
		if( !($check_sign == $md5_sign) ){
			$this->callbacklog("check_sign error:" . $check_sign . ":" . $md5_sign);
			$result['code']=4003;
			echo json_encode($result);	
			exit;
		}      */   
		
		if($event_type == 100){
			/* 回放回调 */
			if(array_key_exists("video_id",$data) && 
					array_key_exists("video_url",$data) &&
					array_key_exists("start_time",$data) &&
					array_key_exists("end_time",$data) ){
						
				$video_id = $data['video_id'];
				$video_url = $data['video_url'];
				$start_time = $data['start_time'];
				$end_time = $data['end_time'];
			}else{
				$this->callbacklog("request para error:回放信息参数缺少" );
				$result['code']=4002;
				echo json_encode($result);	
				exit;
			}
		}     
		
		if($event_type == 0){        	
			/* 状态回调 关播 */
			$ret=$this->stopRoom('',$stream_id);
		}elseif ($event_type == 1){
			//$ret = $this->dao_live->callBackLiveStatus($stream_id,1);
		}elseif ($event_type == 100){
			//$duration = $end_time - $start_time;
			//if ( $duration > 60 ){ 	
				$data=array(
					"video_url"=>$video_url,
					//"duration"=>$duration,
					//"file_id"=>$video_id,
				);								
				$ret=M("users_liverecord")->where(["stream"=>$stream_id])->save($data);
				$ret=0;
			//}else {
			//	$ret = 0;
			//	$this->callbacklog("tape duration too short:" . strval($duration) ."|" . $stream_id . "|" . $video_id);
			//}
			
		}	
		$result['code']=$ret; 
		echo json_encode($result);	
		exit;

	}
	
	public function GetCallBackSign($txTime){
		$config=M("config")->where("id='1'")->find();
		$md5_val = md5($config['live_push_key'] . strval($txTime));
		return $md5_val;
	}
	
	public function callbacklog($msg){
		//file_put_contents('./callbacklog.txt',date('Y-m-d H:i:s').' 提交参数信息 :'.$msg."\r\n",FILE_APPEND);
	}
	
	
	public function stopRoom($uid='',$stream=''){
        $where['islive']=1;
		if($uid){
            $where['uid']=$uid;
		}else{
            $where['stream']=$stream;
		}
		$users_live=M("users_live");
			
		$info=$users_live->field('uid,showid,starttime,title,province,city,stream,lng,lat,type,type_val,liveclassid')->where($where)->find();

		if($info){
			$users_live->where(['stream'=>$info['stream']])->delete();
            
            $uid=$info['uid'];
            $stream=$info['stream'];
            
			$nowtime=time();
			$info['endtime']=$nowtime;
			$info['time']=date("Y-m-d",$info['showid']);
            $where2['uid']=['neq',$uid];
            $where2['touid']=$uid;
            $where2['showid']=$info['showid'];
            
			$votes=M('users_coinrecord')
				->where($where2)
				->sum('totalcoin');
			$info['votes']=0;
			if($votes){
				$info['votes']=$votes;
			}
            $redis = connectionRedis();
			$nums=$redis->zSize('user_'.$stream);			
			$redis->hDel("livelist",$uid);
			$redis->delete($uid.'_zombie');
			$redis->delete($uid.'_zombie_uid');
			$redis->delete('attention_'.$uid);
			$redis->delete('user_'.$stream);
			$info['nums']=$nums;			
			$result=M('users_liverecord')->add($info);	
		}		
		return 0;
	}

}