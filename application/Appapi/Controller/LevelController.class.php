<?php
/**
 * 会员等级
 */
namespace Appapi\Controller;
use Common\Controller\HomebaseController;
class LevelController extends HomebaseController {
	
	function index(){       
		$uid=(int)I("uid");
		$token=I("token");
		
		if(checkToken($uid,$token)==700){
			$this->assign("reason",'您的登陆状态失效，请重新登陆！');
			$this->display(':error');
			exit;
		} 
		
		$User=M("users");
		
		$userinfo=$User->field("avatar,consumption,votestotal")->where(["id"=>$uid])->find();
        
        $userinfo['avatar']=get_upload_path($userinfo['avatar']);
        
		$this->assign("userinfo",$userinfo);
		/* 用户等级 */
		$Level=M("experlevel");  
		
		$levelinfo=$Level->where("level_up>='{$userinfo['consumption']}'")->order("levelid asc")->find();
		if(!$levelinfo){
			$levelinfo=$Level->order("levelid desc")->find();
		}
		$cha=$levelinfo['level_up']+1-$userinfo['consumption'];
		if($cha>0)
		{
			$baifen=floor($userinfo['consumption']/$levelinfo['level_up']*100);
			$type="1";
		}else{
			$baifen=100;
			$type="0";
		}
		


		$this->assign("baifen",$baifen);
		$this->assign("levelinfo",$levelinfo);
		$this->assign("cha",$cha);
		$this->assign("type",$type);
		
		/* 主播等价 */
		$Level_a=M("experlevel_anchor");  
		
		$levelinfo_a=$Level_a->where("level_up>='{$userinfo['votestotal']}'")->order("levelid asc")->find();
		if(!$levelinfo_a){
			$levelinfo_a=$Level_a->order("levelid desc")->find();
		}
		$cha_a=$levelinfo_a['level_up']+1-$userinfo['votestotal'];
		if($cha_a>0)
		{
			$baifen_a=floor($userinfo['votestotal']/$levelinfo_a['level_up']*100);
			$type_a="1";
		}else{
			$baifen_a=100;
			$type_a="0";
		}

		$this->assign("cha_a",$cha_a);
		$this->assign("type_a",$type_a);
		$this->assign("baifen_a",$baifen_a);
		$this->assign("levelinfo_a",$levelinfo_a);
		
		$this->display();
	    
	}
	
	function level(){
		$list=M("experlevel")->order("levelid asc")->select();
		foreach($list as $k=>$v){
			$list[$k]['level_up']=number_format($v['level_up']);
			$list[$k]['thumb']=get_upload_path($v['thumb']);
		}
		$this->assign("list",$list);
		$this->display();
	}

	function level_a(){
		$list=M("experlevel_anchor")->order("levelid asc")->select();
		foreach($list as $k=>$v){
			$list[$k]['level_up']=number_format($v['level_up']);
			$list[$k]['thumb']=get_upload_path($v['thumb']);
		}
		$this->assign("list",$list);
		$this->display();
	}
}