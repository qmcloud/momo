<?php
/**
 * 短视频
 */
class Api_Video extends PhalApi_Api {

	public function getRules() {
		return array(
            'getCon' => array(
				'uid' => array('name' => 'uid', 'type' => 'int','desc' => '用户ID'),
			),
            
			'setVideo' => array(
				'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户Token'),
				'title' => array('name' => 'title', 'type' => 'string',  'desc' => '标题'),
				'thumb' => array('name' => 'thumb', 'type' => 'string',  'require' => true, 'desc' => '封面图'),
				'href' => array('name' => 'href', 'type' => 'string',  'require' => true, 'desc' => '视频链接'),
				'lat' => array('name' => 'lat', 'type' => 'string',  'desc' => '维度'),
				'lng' => array('name' => 'lng', 'type' => 'string',  'desc' => '经度'),
				'city' => array('name' => 'city', 'type' => 'string',  'desc' => '城市'),
				'music_id' => array('name' => 'music_id', 'type' => 'int','default'=>0, 'desc' => '背景音乐id'),
                'goodsid' => array('name' => 'goodsid', 'type' => 'int','default'=>0, 'desc' => '商品ID'),
			),
            'setComment' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户Token'),
				'videoid' => array('name' => 'videoid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '视频ID'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'default'=>0, 'desc' => '回复的评论UID'),
                'commentid' => array('name' => 'commentid', 'type' => 'int',  'default'=>0,  'desc' => '回复的评论commentid'),
                'parentid' => array('name' => 'parentid', 'type' => 'int',  'default'=>0,  'desc' => '回复的评论ID'),
                'content' => array('name' => 'content', 'type' => 'string',  'default'=>'', 'desc' => '内容'),
                'at_info'=>array('name'=>'at_info','type'=>'string','desc'=>'被@的用户json信息'),
            ),
            'addView' => array(
            	'uid' => array('name' => 'uid', 'type' => 'int', 'require' => true, 'desc' => '用户ID'),
            	'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户Token'),
                'videoid' => array('name' => 'videoid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '视频ID'),
                'random_str'=>array('name' => 'random_str', 'type' => 'string', 'require' => true, 'desc' => '加密串'),

            ),
            'addLike' => array(
            	'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
            	'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户Token'),
                'videoid' => array('name' => 'videoid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '视频ID'),
            ),
			'addStep' => array(
            	'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'videoid' => array('name' => 'videoid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '视频ID'),
            ),
			
			'addShare' => array(
            	'uid' => array('name' => 'uid', 'type' => 'int',  'desc' => '用户ID'),
                'videoid' => array('name' => 'videoid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '视频ID'),
                'random_str'=>array('name' => 'random_str', 'type' => 'string', 'require' => true, 'desc' => '加密串'),
            ),
			
			'setBlack' => array(
            	'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'videoid' => array('name' => 'videoid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '视频ID'),
            ),
			
			'addCommentLike' => array(
            	'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
            	'token' => array('name' => 'token', 'type' => 'string', 'require' => false, 'desc' => '用户Token'),
                'commentid' => array('name' => 'commentid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '评论/回复 ID'),
            ),
            'getVideoList' => array(
            	'uid' => array('name' => 'uid', 'type' => 'int',  'desc' => '用户ID'),
            	'p' => array('name' => 'p', 'type' => 'int', 'min' => 1, 'default'=>1, 'desc' => '页数'),
            ),
            'getAttentionVideo' => array(
            	'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
            	'token' => array('name' => 'token', 'type' => 'string', 'require' => false, 'desc' => '用户Token'),
            	'p' => array('name' => 'p', 'type' => 'int', 'min' => 1, 'default'=>1, 'desc' => '页数'),
            ),
            'getVideo' => array(
            	'uid' => array('name' => 'uid', 'type' => 'int','desc' => '用户ID'),
                'videoid' => array('name' => 'videoid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '视频ID'),
            ),
            'getComments' => array(
                'uid' => array('name' => 'uid', 'type' => 'int','desc' => '用户ID'),
                'videoid' => array('name' => 'videoid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '视频ID'),
                'p' => array('name' => 'p', 'type' => 'int', 'min' => 1, 'default'=>1, 'desc' => '页数'),
            ),
			
			'getReplys' => array(
				'uid' => array('name' => 'uid', 'type' => 'int',  'require' => true, 'desc' => '用户ID'),
                'commentid' => array('name' => 'commentid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '评论ID'),
                'p' => array('name' => 'p', 'type' => 'int', 'min' => 1, 'default'=>1, 'desc' => '页数'),
            ),
			
			'getMyVideo' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
				'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户Token'),
                'p' => array('name' => 'p', 'type' => 'int', 'min' => 1, 'default'=>1, 'desc' => '页数'),
            ),
            'del' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => 'token'),
                'videoid' => array('name' => 'videoid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '视频ID'),
            ),
			
			'report' => array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string', 'require' => true, 'desc' => 'token'),
                'videoid' => array('name' => 'videoid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '视频ID'),
                'content' => array('name' => 'content', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '举报内容'),
            ),
			
			'getHomeVideo' => array(
                'uid' => array('name' => 'uid', 'type' => 'int',  'desc' => '用户ID'),
                'touid' => array('name' => 'touid', 'type' => 'int', 'require' => true, 'desc' => '对方ID'),
				'p' => array('name' => 'p', 'type' => 'int', 'min' => 1, 'default'=>1, 'desc' => '页数'),
            ),
			'getCreateNonreusableSignature' => array(
                'imgname' => array('name' => 'imgname', 'type' => 'string', 'desc' => '图片名称'),
                'videoname' => array('name' => 'videoname', 'type' => 'string', 'desc' => '视频名称'),
				'folderimg' => array('name' => 'folderimg', 'type' => 'string','desc' => '图片文件夹'),
				'foldervideo' => array('name' => 'foldervideo', 'type' => 'string', 'desc' => '视频文件夹'),
            ),


            'getRecommendVideos'=>array(
            	'uid' => array('name' => 'uid', 'type' => 'int',  'desc' => '用户ID'),
            	'p' => array('name' => 'p', 'type' => 'int', 'min' => 1, 'default'=>1, 'desc' => '页数'),
            	'isstart' => array('name' => 'isstart', 'type' => 'int', 'default'=>0, 'desc' => '是否启动App'),
            ),

            'getNearby'=>array(
            	'uid' => array('name' => 'uid', 'type' => 'int','desc' => '用户ID'),
                'lng' => array('name' => 'lng', 'type' => 'string', 'desc' => '经度值'),
                'lat' => array('name' => 'lat', 'type' => 'string','desc' => '纬度值'),
				'p' => array('name' => 'p', 'type' => 'int', 'default'=>'1' ,'desc' => '页数'),
            ),

            'setConversion'=>array(
            	'uid' => array('name' => 'uid', 'type' => 'int', 'require' => true, 'desc' => '用户ID'),
            	'token' => array('name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => 'token'),
                'videoid' => array('name' => 'videoid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '视频ID'),
                'random_str'=>array('name' => 'random_str', 'type' => 'string', 'require' => true, 'desc' => '加密串'),
            )

            
		);
	}
    
    /**
	 * 获取视频配置
	 * @desc 用于获取视频配置
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].isshop 是否有店铺，0否1是
	 * @return string msg 提示信息
	 */
	public function getCon() { 
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        $uid=checkNull($this->uid);
        
        /* 店铺信息 */
        
        
        $cdnset['isshop']=0;
        
		$rs['info'][0]=$cdnset;


		return $rs;
	}
	
	/**
	 * 发布短视频
	 * @desc 用于发布短视频
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].id 视频记录ID
	 * @return string msg 提示信息
	 */
	public function setVideo() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=$this->uid;
		$token=checkNull($this->token);
		$title=checkNull($this->title);
		$thumb=checkNull($this->thumb);
		$href=checkNull($this->href);
		$lat=checkNull($this->lat);
		$lng=checkNull($this->lng);
		$city=checkNull($this->city);
		$music_id=checkNull($this->music_id);
        $goodsid=checkNull($this->goodsid);
		
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		
		/* $qiniu_space_host=DI()->config->get('app.Qiniu.space_host');
		
		$thumb=$qiniu_space_host.'/'.$thumb;
		$thumb_s=$thumb.'?imageView2/2/w/200/h/200';
		$href=$qiniu_space_host.'/'.$href; */
        $thumb_s='';
        if($thumb){
            $thumb_s=$thumb.'?imageView2/2/w/200/h/200';
        }
		

		$data=array(
			"uid"=>$uid,
			"title"=>$title,
			"thumb"=>$thumb,
			"thumb_s"=>$thumb_s,
			"href"=>$href,
			"lat"=>$lat,
			"lng"=>$lng,
			"city"=>$city,
			"likes"=>0,
			"views"=>1, //因为涉及到推荐排序问题，所以初始值要为1
			"comments"=>0,
			"addtime"=>time(),
			"music_id"=>$music_id,

		);
        
        
		
		$domain = new Domain_Video();
		$info = $domain->setVideo($data,$music_id);
		if(!$info){
			$rs['code']=1001;
			$rs['msg']='发布失败';
		}

		$rs['info'][0]['id']=$info['id'];
		$rs['info'][0]['thumb_s']=get_upload_path($thumb_s);
		return $rs;
	}		
	
   	/**
     * 评论/回复
     * @desc 用于用户评论/回复 别人视频
     * @return int code 操作码，0表示成功
     * @return array info 
     * @return int info[0].isattent 对方是否关注我
     * @return int info[0].u2t 我是否拉黑对方
     * @return int info[0].t2u 对方是否拉黑我
     * @return int info[0].comments 评论总数
     * @return int info[0].replys 回复总数
     * @return string msg 提示信息
     */
	public function setComment() {
        $rs = array('code' => 0, 'msg' => '评论成功', 'info' => array());
		
		$uid=$this->uid;
		$token=checkNull($this->token);
		$touid=$this->touid;
		$videoid=$this->videoid;
		$commentid=$this->commentid;
		$parentid=$this->parentid;
		$content=checkNull($this->content);
		$at_info=$this->at_info;

		//$arr = json_decode($at_info,true);
		if(!$at_info){
			$at_info='';
		}
		
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		
		if($commentid==0 && $commentid!=$parentid){
			$commentid=$parentid;
		}
		
		$data=array(
			'uid'=>$uid,
			'touid'=>$touid,
			'videoid'=>$videoid,
			'commentid'=>$commentid,
			'parentid'=>$parentid,
			'content'=>$content,
			'addtime'=>time(),
			'at_info'=>$at_info
		);

		/*var_dump($data);
		die;*/

        $domain = new Domain_Video();
        $result = $domain->setComment($data);
		

		
		$info=array(
			'isattent'=>'0',
			'u2t'=>'0',
			't2u'=>'0',
			'comments'=>$result['comments'],
			'replys'=>$result['replys'],
		);
		if($touid>0){
			$isattent=isAttention($touid,$uid);
			$u2t = isBlack($uid,$touid);
			$t2u = isBlack($touid,$uid);
			
			$info['isattent']=(string)$isattent;
			$info['u2t']=(string)$u2t;
			$info['t2u']=(string)$t2u;
		}
		
		$rs['info'][0]=$info;
		
		if($parentid!=0){
			 $rs['msg']='回复成功';			
		}
        return $rs;
    }	
	
   	/**
     * 阅读
     * @desc 用于视频阅读数累计
     * @return int code 操作码，0表示成功
     * @return string msg 提示信息
     */
	public function addView() {
        $rs = array('code' => 0, 'msg' => '更新视频阅读次数成功', 'info' => array());

		$uid=$this->uid;
		$token=checkNull($this->token);
		$videoid=$this->videoid;
		$random_str=checkNull($this->random_str);

		//md5加密验证字符串
		$str=md5($uid.'-'.$videoid.'-'.'#2hgfk85cm23mk58vncsark');

		if($random_str!==$str){
			$rs['code'] = 1001;
			$rs['msg'] = '更新视频阅读次数失败';
			return $rs;
		}

		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}


        $domain = new Domain_Video();
        $res = $domain->addView($this->uid,$this->videoid);

        return $rs;
    }	
   	/**
     * 点赞
     * @desc 用于视频点赞数累计
     * @return int code 操作码，0表示成功
     * @return array info 
     * @return string info[0].islike 是否点赞 
     * @return string info[0].likes 点赞数量
     * @return string msg 提示信息
     */
	public function addLike() {
        $rs = array('code' => 0, 'msg' => '点赞成功', 'info' => array());
        $uid=$this->uid;
        $token=checkNull($this->token);
        $videoid=$this->videoid;
		$isBan=isBan($uid);
		 if($isBan=='0'){
			$rs['code'] = 700;
			$rs['msg'] = '该账号已被禁用';
			return $rs;
		}

		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		
        $domain = new Domain_Video();
        $result = $domain->addLike($uid,$videoid);
		if($result==1001){
			$rs['code'] = 1001;
			$rs['msg'] = "视频已删除";
			return $rs;
		}else if($result==1002){
			$rs['code'] = 1002;
			$rs['msg'] = "不能给自己点赞";
			return $rs;
		}
		$rs['info'][0]=$result;
        return $rs;
    }	

   	/**
     * 踩一下
     * @desc 用于视频踩数累计
     * @return int code 操作码，0表示成功
     * @return array info 
     * @return string info[0].isstep 是否踩
     * @return string info[0].steps 踩数量
     * @return string msg 提示信息
     */
	public function addStep() {
        $rs = array('code' => 0, 'msg' => '踩一踩成功', 'info' => array());
		$isBan=isBan($this->uid);
		 if($isBan=='0'){
			$rs['code'] = 700;
			$rs['msg'] = '该账号已被禁用';
			return $rs;
		}

        $domain = new Domain_Video();
        $rs['info'][0] = $domain->addStep($this->uid,$this->videoid);

        return $rs;
    }

   	/**
     * 视频分享
     * @desc 用于视频分享数累计
     * @return int code 操作码，0表示成功
     * @return array info 
     * @return string info[0].isshare 是否分享
     * @return string info[0].shares 分享数量
     * @return string msg 提示信息
     */
	public function addShare() {
        $rs = array('code' => 0, 'msg' => '分享成功', 'info' => array());

        $uid=$this->uid;
		$videoid=$this->videoid;
		$random_str=checkNull($this->random_str);


		//md5加密验证字符串
		$str=md5($uid.'-'.$videoid.'-'.'#2hgfk85cm23mk58vncsark');

		if($random_str!=$str){
			$rs['code'] = 1001;
			$rs['msg'] = '视频分享数修改失败';
			return $rs;
		}
		
        $domain = new Domain_Video();
        $rs['info'][0] = $domain->addShare($uid,$videoid);

        return $rs;
    }	

   	/**
     * 拉黑视频
     * @desc 用于拉黑视频
     * @return int code 操作码，0表示成功
     * @return array info 
     * @return string info[0].isblack 是否拉黑
     * @return string msg 提示信息
     */
	public function setBlack() {
        $rs = array('code' => 0, 'msg' => '操作成功', 'info' => array());
		$isBan=isBan($this->uid);
		 if($isBan=='0'){
			$rs['code'] = 700;
			$rs['msg'] = '该账号已被禁用';
			return $rs;
		}

        $domain = new Domain_Video();
        $rs['info'][0] = $domain->setBlack($this->uid,$this->videoid);

        return $rs;
    }	
	
   	/**
     * 评论/回复 点赞
     * @desc 用于评论/回复 点赞数累计
     * @return int code 操作码，0表示成功
     * @return array info 
     * @return string info[0].islike 是否点赞 
     * @return string info[0].likes 点赞数量
     * @return string msg 提示信息
     */
	public function addCommentLike() {
        $rs = array('code' => 0, 'msg' => '点赞成功', 'info' => array());

        $uid=$this->uid;
        $token=checkNull($this->token);
        $commentid=$this->commentid;

        $isBan=isBan($uid);
		 if($isBan=='0'){
			$rs['code'] = 700;
			$rs['msg'] = '该账号已被禁用';
			return $rs;
		}

		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}

        $domain = new Domain_Video();
         $res= $domain->addCommentLike($uid,$commentid);
         if($res==1001){
         	$rs['code']=1001;
         	$rs['msg']='评论信息不存在';
         	return $rs;
         }
         $rs['info'][0]=$res;

        return $rs;
    }	
	/**
     * 获取热门视频
     * @desc 用于获取热门视频
     * @return int code 操作码，0表示成功
     * @return array info 视频列表
     * @return object info[].userinfo 用户信息
     * @return string info[].datetime 格式后的发布时间
     * @return string info[].islike 是否点赞
     * @return string info[].isattent 是否关注
     * @return string info[].thumb_s 封面小图，分享用
     * @return string info[].comments 评论总数
     * @return string info[].likes 点赞数
     * @return string info[].goodsid 商品ID，0为无商品
     * @return object info[].goodsinfo 商品信息
     * @return string info[].goodsinfo.name 名称
     * @return string info[].goodsinfo.href 链接
     * @return string info[].goodsinfo.thumb 图片
     * @return string info[].goodsinfo.old_price 原价
     * @return string info[].goodsinfo.price 现价
     * @return string info[].goodsinfo.des 介绍
     * @return string msg 提示信息
     */
	public function getVideoList() {

        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $uid=$this->uid;
        $p=$this->p;
		$isBan=isBan($this->uid);
		 if($isBan=='0'){
			$rs['code'] = 700;
			$rs['msg'] = '该账号已被禁用';
			return $rs;
		}

		$key='videoHot_'.$p;

		$info=getcaches($key);

		if(!$info){
			$domain = new Domain_Video();
			$info= $domain->getVideoList($uid,$p);

			if($info==10010){
				$rs['code'] = 0;
				$rs['msg'] = "暂无视频列表";
				return $rs;
			}
			
			setcaches($key,$info,2);
		}

        
		$rs['info'] =$info;
        return $rs;
    }	
	/**
     * 获取关注视频
     * @desc 用于获取关注视频
     * @return int code 操作码，0表示成功
     * @return array info 视频列表
     * @return array info[].userinfo 用户信息
     * @return string info[].datetime 格式后的发布时间
	 * @return string info[].islike 是否点赞 
	 * @return string info[].comments 评论总数
     * @return string info[].likes 点赞数
     * @return string msg 提示信息
     */
	public function getAttentionVideo() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $uid=$this->uid;
		$token=checkNull($this->token);
		$p=$this->p;
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}

		$key='attention_vidseoLists_'.$uid.'_'.$p;
        $info=getcaches($key);

        if(!$info){
        	$domain = new Domain_Video();
        	$info=$domain->getAttentionVideo($uid,$p);
        	if($info==0){
        		 $rs['code']=0;
                $rs['msg']="暂无视频列表";
                return $rs;
        	}
        }
        
        $rs['info'] = $info;

        return $rs;
    }		
	/**
     * 视频详情
     * @desc 用于获取视频详情
     * @return int code 操作码，0表示成功，1000表示视频不存在
     * @return array info[0] 视频详情
     * @return object info[0].userinfo 用户信息
     * @return string info[0].datetime 格式后的时间差
     * @return string info[0].isattent 是否关注
     * @return string info[0].likes 点赞数
     * @return string info[0].comments 评论数
     * @return string info[0].views 阅读数
     * @return string info[0].steps 踩一踩数量
     * @return string info[0].shares 分享数量
     * @return string info[0].islike 是否点赞
     * @return string info[0].isstep 是否踩
     * @return string msg 提示信息
     */
	public function getVideo() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $domain = new Domain_Video();
        $result = $domain->getVideo($this->uid,$this->videoid);
		if($result==1000){
			$rs['code'] = 1000;
			$rs['msg'] = "视频已删除";
			return $rs;
			
		}
		$rs['info'][0]=$result;

        return $rs;
    }
	/**
     * 视频评论列表
     * @desc 用于获取视频评论列表
     * @return int code 操作码，0表示成功
     * @return array info 
     * @return string info[0].comments 评论总数
     * @return array info[0].commentlist 评论列表
     * @return object info[0].commentlist[].userinfo 用户信息
	 * @return string info[0].commentlist[].datetime 格式后的时间差
	 * @return string info[0].commentlist[].replys 回复总数
	 * @return string info[0].commentlist[].likes 点赞数
	 * @return string info[0].commentlist[].islike 是否点赞
	 * @return array info[0].commentlist[].replylist 回复列表
     * @return string msg 提示信息
     */
	public function getComments() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
		$isBan=isBan($this->uid);
		 if($isBan=='0'){
			$rs['code'] = 700;
			$rs['msg'] = '该账号已被禁用';
			return $rs;
		}

        $domain = new Domain_Video();
        $rs['info'][0] = $domain->getComments($this->uid,$this->videoid,$this->p);

        return $rs;
    }	
	
	/**
     * 回复列表
     * @desc 用于获取视频评论列表
     * @return int code 操作码，0表示成功
     * @return array info 评论列表
     * @return object info[].userinfo 用户信息
	 * @return string info[].datetime 格式后的时间差
	 * @return object info[].tocommentinfo 回复的评论的信息
	 * @return object info[].tocommentinfo.content 评论内容
	 * @return string info[].likes 点赞数
	 * @return string info[].islike 是否点赞
     * @return string msg 提示信息
     */
	public function getReplys() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
		$isBan=isBan($this->uid);
		 if($isBan=='0'){
			$rs['code'] = 700;
			$rs['msg'] = '该账号已被禁用';
			return $rs;
		}

        $domain = new Domain_Video();
        $rs['info'] = $domain->getReplys($this->uid,$this->commentid,$this->p);

        return $rs;
    }	
	
	
	/**
     * 我的视频
     * @desc 用于获取我发布的视频
     * @return int code 操作码，0表示成功
     * @return array info 视频列表
     * @return array info[].userinfo 用户信息
     * @return string info[].datetime 格式后的发布时间
     * @return string info[].islike 是否点赞
     * @return string msg 提示信息
     */
	public function getMyVideo() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=$this->uid;
		$token=checkNull($this->token);
		$p=$this->p;
		
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}

        $domain = new Domain_Video();
        $rs['info'] = $domain->getMyVideo($uid,$p);

        return $rs;
    }	
	
	/**
     * 删除视频
     * @desc 用于删除视频以及相关信息
     * @return int code 操作码，0表示成功
     * @return string msg 提示信息
     */
	public function del() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=$this->uid;
		$token=checkNull($this->token);
		$videoid=$this->videoid;

		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
		
        $domain = new Domain_Video();
        $info = $domain->del($uid,$videoid);

        return $rs;
    }	

	/**
     * 举报视频
     * @desc 用于删除视频以及相关信息
     * @return int code 操作码，0表示成功
     * @return string msg 提示信息
     */
	public function report() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=$this->uid;
		$token=checkNull($this->token);
		$videoid=$this->videoid;
		$content=checkNull($this->content);
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}
        
		$data=array(
			'uid'=>$uid,
			'videoid'=>$videoid,
			'content'=>$content,
			'addtime'=>time(),
		);
        $domain = new Domain_Video();
        $info = $domain->report($data);
		
		if($info==1000){
			$rs['code'] = 1000;
			$rs['msg'] = '视频不存在';
			return $rs;
		}

        return $rs;
    }	


	/**
     * 个人主页视频
     * @desc 用于获取个人主页视频
     * @return int code 操作码，0表示成功
     * @return string msg 提示信息
     */
	public function getHomeVideo() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
		$isBan=isBan($this->uid);
		 if($isBan=='0'){
			$rs['code'] = 700;
			$rs['msg'] = '该账号已被禁用';
			return $rs;
		}
		
		$uid=$this->uid;
		$touid=$this->touid;
		$p=$this->p;

        $domain = new Domain_Video();
        $info = $domain->getHomeVideo($uid,$touid,$p);
		
		
		$rs['info']=$info;

        return $rs;
    }	
	
	/* 检测文件后缀 */
	protected function checkExt($filename){
		$config=array("jpg","png","jpeg");
		$ext   =   pathinfo(strip_tags($filename), PATHINFO_EXTENSION);
		 
		return empty($config) ? true : in_array(strtolower($ext), $config);
	}	
	
	/**
     * 获取七牛上传Token
     * @desc 用于删除视频以及相关信息
     * @return int code 操作码，0表示成功
     * @return int code 操作码，0表示成功
     * @return string msg 提示信息
     */
	public function getQiniuToken(){
	
	   	$rs = array('code' => 0, 'msg' => '', 'info' =>array());

	   	//获取后台配置的腾讯云存储信息
		$configPri=getConfigPri();
		 
		$token = DI()->qiniu->getQiniuToken2($configPri['qiniu_accesskey'],$configPri['qiniu_secretkey'],$configPri['qiniu_bucket']);
		$rs['info'][0]['token']=$token ; 
		return $rs; 
		
	}
	/**
     * 获取腾讯云上传图片签名
     * @desc 用于删除视频以及相关信息
     * @return int code 操作码，0表示成功
     * @return int code 操作码，0表示成功
     * @return string msg 提示信息
     */
	public function getCreateReusableSignatureImg(){
	
	   	$rs = array('code' => 0, 'msg' => '', 'info' =>array());
	
		require(API_ROOT.'/public/txcloud/include.php');
		
		//获取后台配置的腾讯云存储信息
		$configPri=getConfigPri();


		$bucket = $configPri['txcloud_bucket'];
        
		//$config=DI()->config->get('app.TxCloud.config');
		

		$filepath = '/test1/9261c8066f05a46903f3d2341e8203cd.jpg';
		$expiration = time() + 3600; 
		$auth = new \QCloud\Cos\Auth($configPri['txcloud_appid'], $configPri['txcloud_secret_id'], $configPri['txcloud_secret_key']);

		$signature = $auth->createReusableSignature($expiration, $bucket, $filepath);
		 
		$rs['info'][0]['signature']=$signature;

		return $signature;
		
	}
	/**
     *有效签名
     * @return string 
     */
    public function getCreateNonreusableSignature(){
    	
		$rs = array('code' => 0, 'msg' => '', 'info' =>array());
		$imgname=$this->imgname;
		$videoname=$this->videoname;
		$folderimg=$this->folderimg;
		$foldervideo=$this->foldervideo;
		
		require(API_ROOT.'/public/txcloud/include.php');
	
		//无用$config=DI()->config->get('app.TxCloud.config');

		$configPri=getConfigPri();

		$bucketname=$configPri['txcloud_bucket'];
	
		$auth = new \QCloud\Cos\Auth($configPri['txcloud_appid'], $configPri['txcloud_secret_id'], $configPri['txcloud_secret_key']);


		if($imgname){
			$filepathimg="/".$folderimg."/".$imgname;
			
			$signatureimg = $auth->createNonreusableSignature($bucketname, $filepathimg);
		}
		if($videoname){
			$filepathvideo="/".$foldervideo."/".$videoname;
			$signaturevideo = $auth->createNonreusableSignature($bucketname, $filepathvideo);
		} 
		$data=array(
			"imgsign"=>$signatureimg,
			"videosign"=>$signaturevideo,
			"appid"=>$configPri['txcloud_appid'],  //腾讯云appid
			"region"=>$configPri['txcloud_region'], //腾讯云存储buctet所属地域
			"bucketname"=>$configPri['txcloud_bucket'], //腾讯云存储桶
		);


		$rs['info'][0]=$data; 
        return $rs;	
    }

    /**
     * 获取推荐视频
     * @desc 用户获取推荐视频
     * @return int code 状态码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     * @return string info[0].id 视频id
     * @return string info[0].uid 视频发布者id
     * @return string info[0].title 视频标题
     * @return string info[0].thumbs 视频封面
     * @return string info[0].thumbs 视频小封面
     * @return string info[0].href 视频链接
     * @return string info[0].likes 视频被喜欢总数
     * @return string info[0].views 视频被观看总数
     * @return string info[0].comments 视频评论总数
     * @return string info[0].steps 视频被踩总数
     * @return string info[0].shares 视频分享总数
     * @return string info[0].addtime 视频发布时间
     * @return string info[0].lat 纬度
     * @return string info[0].lng 经度
     * @return string info[0].city 城市
     * @return string info[0].isdel 是否删除
     * @return string info[0].datetime 视频发布时间格式化
     * @return string info[0].islike 是否喜欢了该视频
     * @return string info[0].isattent 是否关注
     * @return string info[0].isstep 是否踩了该视频
     * @return string info[0].isdialect 是否方言秀
     * @return object info[0].userinfo 视频发布者信息
     * @return string info[0].userinfo.id 视频发布者id
     * @return string info[0].userinfo.user_nicename 视频发布者昵称
     * @return string info[0].userinfo.avatar 视频发布者头像
     * @return string info[0].userinfo.coin 视频发布者钻石
     * @return string info[0].userinfo.avatar_thumb 视频发布者小头像
     * @return string info[0].userinfo.sex 视频发布者性别
     * @return string info[0].userinfo.signature 视频发布者签名
     * @return string info[0].userinfo.privince 视频发布者省份
     * @return string info[0].userinfo.city 视频发布者市
     * @return string info[0].userinfo.birthday 视频发布者生日
     * @return string info[0].userinfo.age 视频发布者年龄
     * @return string info[0].userinfo.praise 视频发布者被赞总数
     * @return string info[0].userinfo.fans 视频发布者粉丝数
     * @return string info[0].userinfo.follows 视频发布者关注数
     * @return object info[0].musicinfo 背景音乐信息
     * @return string info[0].musicinfo.id 背景音乐id
     * @return string info[0].musicinfo.title 背景音乐标题
     * @return string info[0].musicinfo.author 背景音乐作者
     * @return string info[0].musicinfo.img_url 背景音乐封面地址
     * @return string info[0].musicinfo.length 背景音乐长度
     * @return string info[0].musicinfo.file_url 背景音乐地址
     * @return string info[0].musicinfo.use_nums 背景音乐使用次数
     */
    public function getRecommendVideos(){
    	$rs = array('code' => 0, 'msg' => '', 'info' => array());

    	$uid=$this->uid;
    	
    	if($uid>0){ //非游客

    		$isBan=isBan($this->uid);
			if($isBan=='0'){
				$rs['code'] = 700;
				$rs['msg'] = '该账号已被禁用';
				return $rs;
			}
    	}
		

		$p=$this->p;
		$isstart=$this->isstart;


		$key='videoRecommend_'.$p;

		$info=getcaches($key);

		if(!$info){

			$domain=new Domain_Video();
			$info=$domain->getRecommendVideos($uid,$p,$isstart);

			if($info==1001){
				$rs['code']=1001;
				$rs['msg']="暂无视频列表";
				return $rs;
			}

			setcaches($key,$info,2);

		}

		



		$rs['info']=$info;

		return $rs;
    }

	/**
	 * 获取附近的视频列表
	 * @desc 用于获取附近的视频列表
	 * @return int code 状态码，0表示成功
	 * @return string msg 提示信息
	 * @return array info 返回信息
	 */
	public function getNearby(){
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=$this->uid;
		$lng=checkNull($this->lng);
		$lat=checkNull($this->lat);
		$p=checkNull($this->p);

		if($lng==''){
			return $rs;
		}
		
		if($lat==''){
			return $rs;
		}
		
		if(!$p){
			$p=1;
		}

		$key='videoNearby_'.$lng.'_'.$lat.'_'.$p;

		$info=getcaches($key);

		if(!$info){
			$domain = new Domain_Video();
			$info = $domain->getNearby($uid,$lng,$lat,$p);

			if($info==1001){
				return $rs;
			}
			
			setcaches($key,$info,2);
		}

		$rs['info'] = $info;
        return $rs;
	}

	/**
     * 获取视频举报分类列表
     * @desc 获取视频举报分类列表
     * @return int code 操作码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     */
	public function getReportContentlist() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $domain = new Domain_Video();
        $res = $domain->getReportContentlist();

        if($res==1001){
        	$rs['code']=1001;
        	$rs['msg']='暂无举报分类列表';
        	return $rs;
        }
        $rs['info']=$res;
        return $rs;
    }

    /**
     * 更新视频看完次数
     * @desc 更新视频看完次数
     * @return int code 操作码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     */
    public function setConversion(){

    	$rs = array('code' => 0, 'msg' => '视频完整观看次数更新成功', 'info' => array());
    	$uid=$this->uid;
		$videoid=$this->videoid;
		$random_str=checkNull($this->random_str);

		//md5加密验证字符串
		$str=md5($uid.'-'.$videoid.'-'.'#2hgfk85cm23mk58vncsark');

		if($random_str!==$str){
			$rs['code'] = 1001;
			$rs['msg'] = '视频完整观看次数更新失败';
			return $rs;
		}

		$token=checkNull($this->token);
		$checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = '您的登陆状态失效，请重新登陆！';
			return $rs;
		}

		

		$domain = new Domain_Video();
        $res = $domain->setConversion($videoid);
        

        return $rs;

    }


}
