<?php
/**
 * 个人主页
 */
namespace Appapi\Controller;
use Common\Controller\HomebaseController;
class HomeController extends HomebaseController {
	
	function index(){       
		$touid=(int)I("touid");
        
        if(!$touid){
            $this->assign("reason",'信息错误');
			$this->display(':error');
			exit;
        }

		$info=getUserInfo($touid);	

        if(!$info){
            $this->assign("reason",'信息错误');
			$this->display(':error');
			exit;
        }        

		$info['follows']=NumberFormat(getFollownums($touid));
		$info['fans']=NumberFormat(getFansnums($touid));
        
        $this->assign('info',$info);

		/* 贡献榜前三 */

		$contribute=M("users_coinrecord")
				->field("uid,sum(totalcoin) as total")
				->where(["action"=>'sendgift' , "touid"=>$touid])
				->group("uid")
				->order("total desc")
				->limit(0,3)
				->select();
		foreach($contribute as $k=>$v){
			$userinfo=getUserInfo($v['uid']);
			$contribute[$k]['avatar']=$userinfo['avatar'];
		}		

        $this->assign('contribute',$contribute);
		
        /* 视频数 */
        $info['videonums']='0';
        /* 直播数 */
        $livenums=M("users_liverecord")
					->where(["uid"=>$touid])
					->count();

        $this->assign('livenums',$livenums);
		/* 直播记录 */
		$record=array();
		$record=M("users_liverecord")
					->field("id,uid,nums,starttime,endtime,title,city")
					->where(["uid"=>$touid])
					->order("id desc")
					->limit(0,20)
					->select();
		foreach($record as $k=>$v){
            if($v['title']==''){
                $record[$k]['title']='无标题';
            }
			$record[$k]['datestarttime']=date("Y.m.d",$v['starttime']);
			$record[$k]['dateendtime']=date("Y.m.d",$v['endtime']);
            $cha=$v['endtime']-$v['starttime'];
            $record[$k]['length']=getSeconds($cha);
		}			

        $this->assign('liverecord',$record);
        
        
        /* 标签 */

        $label=getMyLabel($touid);
        
        $labels=array_slice($label,0,3);
        
		$this->assign('labels',$labels);

		
		$this->display();
	    
	}

}