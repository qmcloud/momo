<?php
/**
 * 贡献榜
 */
namespace Appapi\Controller;
use Common\Controller\HomebaseController;
class ContributeController extends HomebaseController {
	
	function index(){
		$uid=(int)I("uid");
        
        $nowtime=time();
        //当天0点
        $today=date("Ymd",$nowtime);
        $today_start=strtotime($today);
        //当天 23:59:59
        $today_end=strtotime("{$today} + 1 day");

        $w=date('w',$nowtime); 
        //获取本周开始日期，如果$w是0，则表示周日，减去 6 天 
        $first=1;
        //周一
        $week=date('Y-m-d H:i:s',strtotime( date("Ymd")."-".($w ? $w - $first : 6).' days')); 
        $week_start=strtotime( date("Ymd")."-".($w ? $w - $first : 6).' days'); 

        //本周结束日期 
        //周天
        $week_end=strtotime("{$week} +1 week");

        
        //本月第一天
        $month=date("Y-m",$nowtime).'-01';
        $month_start=strtotime($month);

        //本月最后一天
        $month_end=strtotime("{$month} +1 month");
        
            
            
		$p=1;
		$page_nums=20;
		$start=($p-1)*$page_nums;
        
        /* 日榜 */
		$list_day=M("users_coinrecord")->field("uid,sum(totalcoin) as total")->where(" action in ('sendgift','sendbarrage') and touid='{$uid}' and addtime >={$today_start} and addtime < {$today_end} ")->group("uid")->order("total desc")->limit($page_nums)->select();
		foreach($list_day as $k=>$v){
			$list_day[$k]['userinfo']=getUserInfo($v['uid']);
		}
		$this->assign("list_day",$list_day);
        
        $list_day_total=M("users_coinrecord")->where(" action in ('sendgift','sendbarrage') and touid='{$uid}' and addtime >={$today_start} and addtime < {$today_end} ")->sum('totalcoin');
        if(!$list_day_total){
            $list_day_total=0;
        }
        
        $this->assign("list_day_total",$list_day_total);
        
        /* 周榜 */
        $list_week=M("users_coinrecord")->field("uid,sum(totalcoin) as total")->where(" action in ('sendgift','sendbarrage') and touid='{$uid}' and addtime >={$week_start} and addtime < {$week_end} ")->group("uid")->order("total desc")->limit($page_nums)->select();
		foreach($list_week as $k=>$v){
			$list_week[$k]['userinfo']=getUserInfo($v['uid']);
		}
		$this->assign("list_week",$list_week);
        
        $list_week_total=M("users_coinrecord")->where(" action in ('sendgift','sendbarrage') and touid='{$uid}' and addtime >={$week_start} and addtime < {$week_end} ")->sum('totalcoin');
        if(!$list_week_total){
            $list_week_total=0;
        }
        $this->assign("list_week_total",$list_week_total);
        
        /* 月榜 */
        $list_month=M("users_coinrecord")->field("uid,sum(totalcoin) as total")->where(" action in ('sendgift','sendbarrage') and touid='{$uid}' and addtime >={$month_start} and addtime < {$month_end} ")->group("uid")->order("total desc")->limit($page_nums)->select();
		foreach($list_month as $k=>$v){
			$list_month[$k]['userinfo']=getUserInfo($v['uid']);
		}
		$this->assign("list_month",$list_month);
        
        $list_month_total=M("users_coinrecord")->where(" action in ('sendgift','sendbarrage') and touid='{$uid}' and addtime >={$month_start} and addtime < {$month_end} ")->sum('totalcoin');
        if(!$list_month_total){
            $list_month_total=0;
        }
        $this->assign("list_month_total",$list_month_total);
        
        /* 总榜 */
        $list_all=M("users_coinrecord")->field("uid,sum(totalcoin) as total")->where(" action in ('sendgift','sendbarrage') and touid='{$uid}' ")->group("uid")->order("total desc")->limit($page_nums)->select();
		foreach($list_all as $k=>$v){
			$list_all[$k]['userinfo']=getUserInfo($v['uid']);
		}
		$this->assign("list_all",$list_all);
        
        $list_all_total=M("users_coinrecord")->where(" action in ('sendgift','sendbarrage') and touid='{$uid}' ")->sum('totalcoin');
        if(!$list_all_total){
            $list_all_total=0;
        }
        $this->assign("list_all_total",$list_all_total);
        
		$this->assign("uid",$uid);
		


		$this->display();		
	}
	
	function getmore(){
		$uid=(int)I("uid");
		$p=(int)I("page");
		$page_nums=20;
		$start=($p-1)*$page_nums;
		
		$list=M("users_coinrecord")->field("uid,sum(totalcoin) as total")->where(" action in ('sendgift','sendbarrage') and touid='{$uid}'")->group("uid")->order("total desc")->limit($start,$page_nums)->select();
		foreach($list as $k=>$v){
			$list[$k]['userinfo']=getUserInfo($v['uid']);
		}
		
		
		$nums=count($list);
		if($nums<$page_nums){
			$isscroll=0;
		}else{
			$isscroll=1;
		}

		$result=array(
			'data'=>$list,
			'nums'=>$nums,
			'start'=>$start,
			'isscroll'=>$isscroll,
		);
	 
		echo json_encode($result);
		exit;		
	}
	
	public function order(){
		$uid=(int)I("uid");
		$type=I("type");
		
		if($type=='week'){
			
			$nowtime=time();
			//当天0点
			//$today=date("Ymd",$nowtime);
			//$today_start=strtotime($today);
			//当天 23:59:59
			//$today_end=strtotime("{$today} + 1 day")-1;

			$w=date('w',$nowtime); 
			//获取本周开始日期，如果$w是0，则表示周日，减去 6 天 
			$first=1;
			//周一
			$week=date('Y-m-d H:i:s',strtotime( date("Ymd")."-".($w ? $w - $first : 6).' days')); 
			$week_start=strtotime( date("Ymd")."-".($w ? $w - $first : 6).' days'); 

			//本周结束日期 
			//周天
			$week_end=strtotime("{$week} +1 week")-1;
			
			
			$list=M("users_coinrecord")->field("uid,sum(totalcoin) as total")->where(" action in ('sendgift','sendbarrage') and touid='{$uid}' and addtime>{$week_start} and addtime<{$week_end}")->group("uid")->order("total desc")->limit(0,20)->select();
			
			foreach($list as $k=>$v){
				$list[$k]['userinfo']=getUserInfo($v['uid']);
			}
		}else{
			$list=M("users_coinrecord")->field("uid,sum(totalcoin) as total")->where(" action in ('sendgift','sendbarrage') and touid='{$uid}'")->group("uid")->order("total desc")->limit(0,20)->select();
			foreach($list as $k=>$v){
				$list[$k]['userinfo']=getUserInfo($v['uid']);
			}
		}

		$this->assign("list",$list);
		
		
		
		
		


		$this->display();			
		
		
	}

}