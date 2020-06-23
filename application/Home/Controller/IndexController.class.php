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
/**
 * 首页
 */
class IndexController extends HomebaseController {
	public function pc_index() {
		
			$this->assign("pc_index",$pc_index);
	
		$this->display();
	}	
    //首页
	public function index() {


		$prefix= C("DB_PREFIX");
		$this->assign("current",'index');	
		$uid=session("uid");
		$firstLive="";

		/*获取推荐播放列表(正在直播，推荐，按粉丝数排序)*/

		$indexLive=M("users_live")->where("islive='1' and isrecommend='1' and type='0'")->select();

		foreach ($indexLive as $k => $v){
            $userinfo=getUserInfo($v['uid']);
            
            $v['avatar']=$userinfo['avatar'];
			$v['avatar_thumb']=$userinfo['avatar_thumb'];
			$v['user_nicename']=$userinfo['user_nicename'];
            
			if($v['thumb']==""){
				$v['thumb']=$v['avatar'];
			}
			if($v['isvideo']==0){
                if($this->configpri['cdn_switch']!=5){
                    $v['pull']=PrivateKeyA('rtmp',$v['stream'],0);
                }
            }
			$v['fans_nums']=M("users_attention")->where("touid={$v['uid']}")->count();
            $indexLive[$k]=$v;
		}

		$sort=array_column($indexLive,"fans_nums");
		array_multisort($sort, SORT_DESC, $indexLive);
		$indexLive1=array_slice($indexLive,0,4);
		$firstLive=$indexLive[0]['pull'];
		$firstUid=$indexLive[0]['uid'];
		$this->assign("indexLive",$indexLive1);
		$this->assign("firstUid",$firstUid);
		$this->assign("firstLive",$firstLive);
		//var_dump($indexLive1);
		/* 轮播 */
		$slide=M("slide")->where("slide_status='1' and slide_cid='1'")->order("listorder asc")->select();
		$this->assign("slide",$slide);	

		$redis =connectionRedis();

		/* 推荐（正在直播 在线人数） */
		$recommend=M("users_live ")
					->field("uid,thumb,uid,stream,type,islive")
					->where("islive='1'")
					->limit(12)
					->select();
		foreach($recommend as $k=>$v){
            $userinfo=getUserInfo($v['uid']);
            
            $v['avatar']=$userinfo['avatar'];
			$v['avatar_thumb']=$userinfo['avatar_thumb'];
			$v['user_nicename']=$userinfo['user_nicename'];
	 		if($v['thumb']=="")
			{
				$v['thumb']=$v['avatar'];
			} 
			$nums=$redis->zSize('user_'.$v['stream']);
			$v['nums']=$nums;
            $recommend[$k]=$v;
		}

		$sort=array_column($recommend,"nums");
		$sort1=array_column($recommend,"uid");
		array_multisort($sort, SORT_DESC,$sort1,SORT_DESC, $recommend);

		
		$this->assign("recommend",$recommend);			 
			 
		/* 热门（在直播，推荐为热门） */
		$hot=M("users_live l")
					->field("uid,thumb,stream,title,city,islive,type")
					->where("islive='1' and ishot='1'")
					->order("isrecommend desc,starttime desc")
					->limit(10)
					->select();
        foreach($hot as $k=>$v){
            $userinfo=getUserInfo($v['uid']);

            $v['avatar']=$userinfo['avatar'];
            $v['avatar_thumb']=$userinfo['avatar_thumb'];
            $v['user_nicename']=$userinfo['user_nicename'];
            $v['signature']=$userinfo['signature'];
            $nums=$redis->zSize('user_'.$v['stream']);

            $v['nums']=(string)$nums;
            if($v['thumb']=="")
            {
                $v['thumb']=$v['avatar'];
            }
            $hot[$k]=$v;
		} 
		$this->assign("hot",$hot);

		/* 最新直播（在直播，按开播时间倒序） */ 
		$live=M("users_live")->field("uid,thumb,stream,title,city,islive,type")->where("islive='1'")->order("starttime desc")->limit(10)->select();
		foreach($live as $k=>$v){
            $userinfo=getUserInfo($v['uid']);
            
            $v['avatar']=$userinfo['avatar'];
			$v['avatar_thumb']=$userinfo['avatar_thumb'];
			$v['user_nicename']=$userinfo['user_nicename'];
			$v['signature']=$userinfo['signature'];
            
			$nums=$redis->zSize('user_'.$v['stream']);
			$v['nums']=(string)$nums;

			if($v['thumb']==""){
				$v['thumb']=$v['avatar'];
			}
            $live[$k]=$v;
		} 

		$this->assign("live",$live);


		/* 主播排行榜 */
	  /*$anchorlist=M("users_liverecord")->field("uid,sum(nums) as light")->order("light desc")->group("uid")->limit(10)->select();
		foreach($anchorlist as $k=>$v){
			$anchorlist[$k]['userinfo']=getUserInfo($v['uid']);
			// 判断 当前用户是否关注
			if($uid>0){
				$isAttention=isAttention($uid,$v['uid']);
				$anchorlist[$k]['isAttention']=$isAttention;
			}else{
				$anchorlist[$k]['isAttention']=0;
			}
			
		}
		$this->assign("anchorlist",$anchorlist);*/

		

		$redis->close();
    	$this->display();
    }	
	
	public function translate()
	{
		$prefix= C("DB_PREFIX");	
		$keyword=I('keyword');
		if($keyword!='')
		{
			$where['user_type']='2';
            $whereor['id']  = $keyword;
            $whereor['user_nicename']  = array('like',"%{$keyword}%");
            $whereor['_logic'] = 'or';
            $where['_complex'] = $whereor;
			$_GET['keyword']=$keyword;
		}
		else
		{
			$where="u.user_type='2' and l.islive='1' ";
		}
		$auth=M("users");
		$pagesize = 18; 
		if($keyword=="")
		{
			$count= M("users_live l")
					->field("u.user_nicename,u.avatar,l.uid,l.stream,l.title,l.city,l.islive")
					->join("left join {$prefix}users u on u.id=l.uid")
					->where($where)
					->order("l.starttime desc")
					->count();
			$Page= new \Page2($count,$pagesize);
			$show= $Page->show();
			$lists=M("users_live l")
					->field("u.user_nicename,u.avatar,l.uid,l.stream,l.title,l.city,l.islive")
					->join("left join {$prefix}users u on u.id=l.uid")
					->where($where)
					->order("l.starttime desc")
					->limit($Page->firstRow.','.$Page->listRows)
					->select();
			$msg["info"]='抱歉,没有找到关于"';
			$msg["name"]='';
			$msg["result"]='"的搜索结果';
			$msg["type"]='0';
		}else{
			$count= $auth->where($where)->count();
			$Page= new \Page2($count,$pagesize);
			$show= $Page->show();
			$lists=$auth->where($where)->order("consumption desc")->limit($Page->firstRow.','.$Page->listRows)->select();
			$msg["info"]='共找到'.$count.'个关于"';
			$msg["name"]=$keyword;
			$msg["result"]='"的搜索结果';
			$msg["type"]='1';
		}
		$this->assign('lists',$lists);
		$this->assign('msg',$msg);
		$this->assign('page',$show);
		$this->assign('formget', $_GET);
		$this->display();
	}	
    
    /* 图片裁剪 */
    function cutImg(){
        $filepath=I('filepath');
        $new_width=I('width');
        $new_height=I('height');
        $source_info   = getimagesize($filepath);
        $source_width  = $source_info[0];
        $source_height = $source_info[1];
        $source_mime   = $source_info['mime'];
        $source_ratio  = $source_height / $source_width;
        $target_ratio  = $new_height / $new_width;
        // 源图过高
        if ($source_ratio > $target_ratio){

            $cropped_width  = $source_width;
            $cropped_height = $source_width * $target_ratio;
            $source_x = 0;
            $source_y = ($source_height - $cropped_height) / 2;
        }
        // 源图过宽
        elseif ($source_ratio < $target_ratio){
        	
            $cropped_width  = $source_height / $target_ratio;
            $cropped_height = $source_height;
            $source_x = ($source_width - $cropped_width) / 2;
            $source_y = 0;
        }
        // 源图适中
        else{

            $cropped_width  = $source_width;
            $cropped_height = $source_height;
            $source_x = 0;
            $source_y = 0;
        }

        switch ($source_mime){
            case 'image/gif':
                $source_image = imagecreatefromgif($filepath);
                break;
            case 'image/jpeg':
                $source_image = imagecreatefromjpeg($filepath);
                break;
            case 'image/png':
                $source_image = imagecreatefrompng($filepath);
                break;
            default:
                return false;
            break;
        }

        $target_image  = imagecreatetruecolor($new_width, $new_height);
        $cropped_image = imagecreatetruecolor($cropped_width, $cropped_height);
        // 裁剪
        imagecopy($cropped_image, $source_image, 0, 0, $source_x, $source_y, $cropped_width, $cropped_height);
        // 缩放
        imagecopyresampled($target_image, $cropped_image, 0, 0, 0, 0, $new_width, $new_height, $cropped_width, $cropped_height);
        header('Content-Type: image/jpeg');
        imagejpeg($target_image);
        imagedestroy($source_image);
        imagedestroy($target_image);
        imagedestroy($cropped_image);
    }

}


