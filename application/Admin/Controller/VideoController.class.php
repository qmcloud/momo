<?php

/**
 * 短视频
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
use QCloud\Cos\Api;
use QCloud\Cos\Auth;



class VideoController extends AdminbaseController {

	/*待审核视频列表*/
    function index(){
		
		if($_REQUEST['ordertype']!=''){
			$ordertype=$_REQUEST['ordertype'];
			$_GET['ordertype']=$_REQUEST['ordertype'];
		}
		 $map['isdel']=0;
		 $map['status']=0; 
		 $map['is_ad']=0; 
		
		if($_REQUEST['keyword']!=''){
			$map['uid|id']=array("eq",$_REQUEST['keyword']); 
			$_GET['keyword']=$_REQUEST['keyword'];
		}		
		if($_REQUEST['keyword1']!=''){
			$map['title']=array("like","%".$_REQUEST['keyword1']."%");  
			$_GET['keyword1']=$_REQUEST['keyword1'];
		}
		//用户名称
		if($_REQUEST['keyword2']!=''){
			/* $map['title']=array("like","%".$_REQUEST['keyword2']."%");   */
			$_GET['keyword2']=$_REQUEST['keyword2'];
			$username=$_REQUEST['keyword2'];
            $where['user_nicename']=array('like',"%".$username."%");
			$userlist =M("users")->field("id")->where($where)->select();
			$strids="";
			foreach($userlist as $ku=>$vu){
				if($strids==""){
					$strids=$vu['id'];
				}else{
					$strids.=",".$vu['id'];
				}
			}
			$map['uid']=array("in",$strids);
		}
		
		$p=I("p");
		if(!$p){
			$p=1;
		}

    	$video_model=M("users_video");
    	$count=$video_model->where($map)->count();
    	$page = $this->page($count, 20);
		$orderstr="";
		if($ordertype==1){//评论数排序
			$orderstr="comments DESC";
		}else if($ordertype==2){//票房数量排序（点赞）
			$orderstr="likes DESC";
		}else if($ordertype==3){//分享数量排序
			$orderstr="shares DESC";
		}else{
			$orderstr="addtime DESC";
		}
		
    	$lists = $video_model
			->where($map)
			->order($orderstr)
			->limit($page->firstRow . ',' . $page->listRows)
			->select();
		foreach($lists as $k=>$v){
			if($v['uid']==0){
				$userinfo=array(
					'user_nicename'=>'系统管理员'
				);
			}else{
				$userinfo=getUserInfo($v['uid']);
				if(!$userinfo){
					$userinfo=array(
						'user_nicename'=>'已删除'
					);
				}
				
			}

			
			$lists[$k]['userinfo']=$userinfo;
			
			$hasurgemoney=($v['big_urgenums']-$v['urge_nums'])*$v['urge_money'];
			$lists[$k]['hasurgemoney']=$hasurgemoney;
		}
    	$this->assign('lists', $lists);
		$this->assign('formget', $_GET);
    	$this->assign("page", $page->show('Admin'));
    	$this->assign("p",$p);
    	$this->display();
    }


     /*未通过视频列表*/
	
    function nopassindex(){
		
		if($_REQUEST['ordertype']!=''){
			$ordertype=$_REQUEST['ordertype'];
			$_GET['ordertype']=$_REQUEST['ordertype'];
		}
		 $map['isdel']=0; 
		 $map['status']=2; 
		 $map['is_ad']=0; 
		
		if($_REQUEST['keyword']!=''){
			$map['uid|id']=array("eq",$_REQUEST['keyword']); 
			$_GET['keyword']=$_REQUEST['keyword'];
		}		
		if($_REQUEST['keyword1']!=''){
			$map['title']=array("like","%".$_REQUEST['keyword1']."%");  
			$_GET['keyword1']=$_REQUEST['keyword1'];
		}
		//用户名称
		if($_REQUEST['keyword2']!=''){
			/* $map['title']=array("like","%".$_REQUEST['keyword2']."%");   */
			$_GET['keyword2']=$_REQUEST['keyword2'];
			$username=$_REQUEST['keyword2'];
            $where['user_nicename']=array('like',"%".$username."%");
            
			$userlist =M("users")->field("id")->where($where)->select();
			$strids="";
			foreach($userlist as $ku=>$vu){
				if($strids==""){
					$strids=$vu['id'];
				}else{
					$strids.=",".$vu['id'];
				}
			}
			$map['uid']=array("in",$strids);
		}
		
		$p=I("p");
		if(!$p){
			$p=1;
		}

    	$video_model=M("users_video");
    	$count=$video_model->where($map)->count();
    	$page = $this->page($count, 20);
		$orderstr="";
		if($ordertype==1){//评论数排序
			$orderstr="comments DESC";
		}else if($ordertype==2){//票房数量排序（点赞）
			$orderstr="likes DESC";
		}else if($ordertype==3){//分享数量排序
			$orderstr="shares DESC";
		}else{
			$orderstr="addtime DESC";
		}
		
    	$lists = $video_model
			->where($map)
			->order($orderstr)
			->limit($page->firstRow . ',' . $page->listRows)
			->select();
		foreach($lists as $k=>$v){
			if($v['uid']==0){
				$userinfo=array(
					'user_nicename'=>'系统管理员'
				);
			}else{
				$userinfo=getUserInfo($v['uid']);
				if(!$userinfo){
					$userinfo=array(
						'user_nicename'=>'已删除'
					);
				}
				
			}

			
			$lists[$k]['userinfo']=$userinfo;
			
			$hasurgemoney=($v['big_urgenums']-$v['urge_nums'])*$v['urge_money'];
			$lists[$k]['hasurgemoney']=$hasurgemoney;
		}
    	$this->assign('lists', $lists);
		$this->assign('formget', $_GET);
    	$this->assign("page", $page->show('Admin'));
    	$this->assign("p",$p);
    	$this->display();
    }


    /*审核通过视频列表*/
	
    function passindex(){
		
		if($_REQUEST['ordertype']!=''){
			$ordertype=$_REQUEST['ordertype'];
			$_GET['ordertype']=$_REQUEST['ordertype'];
		}
		 $map['isdel']=0; 
		 $map['status']=1; 
		 $map['is_ad']=0; 
		
		if($_REQUEST['keyword']!=''){
			$map['uid|id']=array("eq",$_REQUEST['keyword']); 
			$_GET['keyword']=$_REQUEST['keyword'];
		}		
		if($_REQUEST['keyword1']!=''){
			$map['title']=array("like","%".$_REQUEST['keyword1']."%");  
			$_GET['keyword1']=$_REQUEST['keyword1'];
		}
		//用户名称
		if($_REQUEST['keyword2']!=''){
			/* $map['title']=array("like","%".$_REQUEST['keyword2']."%");   */
			$_GET['keyword2']=$_REQUEST['keyword2'];
			$username=$_REQUEST['keyword2'];
            $where['user_nicename']=array('like',"%".$username."%");
            
			$userlist =M("users")->field("id")->where($where)->select();
			$strids="";
			foreach($userlist as $ku=>$vu){
				if($strids==""){
					$strids=$vu['id'];
				}else{
					$strids.=",".$vu['id'];
				}
			}
			$map['uid']=array("in",$strids);
		}
		
		$p=I("p");
		if(!$p){
			$p=1;
		}

    	$video_model=M("users_video");
    	$count=$video_model->where($map)->count();
    	$page = $this->page($count, 20);
		$orderstr="";
		if($ordertype==1){//评论数排序
			$orderstr="comments DESC";
		}else if($ordertype==2){//票房数量排序（点赞）
			$orderstr="likes DESC";
		}else if($ordertype==3){//分享数量排序
			$orderstr="shares DESC";
		}else{
			$orderstr="addtime DESC";
		}
		
    	$lists = $video_model
			->where($map)
			->order($orderstr)
			->limit($page->firstRow . ',' . $page->listRows)
			->select();
		foreach($lists as $k=>$v){
			if($v['uid']==0){
				$userinfo=array(
					'user_nicename'=>'系统管理员'
				);
			}else{
				$userinfo=getUserInfo($v['uid']);
				if(!$userinfo){
					$userinfo=array(
						'user_nicename'=>'已删除'
					);
				}
				
			}

			
			$lists[$k]['userinfo']=$userinfo;
			
			$hasurgemoney=($v['big_urgenums']-$v['urge_nums'])*$v['urge_money'];
			$lists[$k]['hasurgemoney']=$hasurgemoney;
		}
    	$this->assign('lists', $lists);
		$this->assign('formget', $_GET);
    	$this->assign("page", $page->show('Admin'));
    	$this->assign("p",$p);
    	$this->display();
    }

		
	function del(){

		$res=array("code"=>0,"msg"=>"删除成功","info"=>array());
		$id=(int)I("id");
		$reason=I("reason");
		if(!$id){

			$res['code']=1001;
			$res['msg']='视频信息加载失败';
			echo json_encode($res);
			exit;
		}	

		//获取视频信息
		$videoInfo=M("users_video")->where("id={$id}")->find();

		$result=M("users_video")->where("id={$id}")->delete();
		
		//$result=M("users_video")->where("id={$id}")->setField("isdel","1");

		if($result!==false){

			M("users_video_black")->where("videoid={$id}")->delete();	 //删除视频拉黑
			M("users_video_comments")->where("videoid={$id}")->delete();	 //删除视频评论
			M("users_video_like")->where("videoid={$id}")->delete();	 //删除视频喜欢
			M("users_video_report")->where("videoid={$id}")->delete();	 //删除视频举报
			
			/*//获取该视频的评论id
			$commentlists=M("users_video_comments")->field("id")->where("videoid={$id}")->select();
			$commentids="";
			foreach($commentlists as $k=>$v){
				if($commentids==""){
					$commentids=$v['id'];
				}else{
					$commentids.=",".$v['id'];
				}
			}

			//删除视频评论喜欢
			$map['commentid']=array("in",$commentids);*/


			M("users_video_comments_like")->where("videoid={$id}")->delete(); //删除视频评论喜欢

			

			if($videoInfo['isdel']==0){ //视频上架情况下被删除发送通知
				//极光IM
				$id=$_SESSION['ADMIN_ID'];
				$user=M("Users")->where("id='{$id}'")->find();

	    		//向系统通知表中写入数据
	    		/* $sysInfo=array(
	    			'title'=>'视频删除提醒',
	    			'addtime'=>time(),
	    			'admin'=>$user['user_login'],
	    			'ip'=>get_client_ip(0,true),
	    			'uid'=>$videoInfo['uid']

	    		);

	    		if($videoInfo['title']!=''){
	    			$videoTitle='上传的《'.$videoInfo['title'].'》';
	    		}else{
	    			$videoTitle='上传的';
	    		}

	    		$baseMsg='您于'.date("Y-m-d H:i:s",$videoInfo['addtime']).$videoTitle.'视频被管理员于'.date("Y-m-d H:i:s",time()).'删除';

	    		if(!$reason){
	    			$sysInfo['content']=$baseMsg;
	    		}else{
	    			$sysInfo['content']=$baseMsg.',删除原因为：'.$reason;
	    		}

	    		$result1=M("system_push")->add($sysInfo);

	    		if($result1!==false){

	    			$text="视频删除提醒";
	    			$uid=$videoInfo['uid'];
	    			jMessageIM($text,$uid);

	    		} */
			}
			



			$res['msg']='视频删除成功';
			echo json_encode($res);
			exit;
		}else{
			$res['code']=1002;
			$res['msg']='视频删除失败';
			echo json_encode($res);
			exit;
		}			
										  			
	}		
    //排序
    public function listorders() { 
		
        $ids = $_POST['listorders'];
        foreach ($ids as $key => $r) {
            $data['orderno'] = $r;
            M("users_video")->where(array('id' => $key))->save($data);
        }
				
        $status = true;
        if ($status) {
            $this->success("排序更新成功！");
        } else {
            $this->error("排序更新失败！");
        }
    }	
    

	function add(){

		$this->display();				
	}

	function add_post(){
		if(IS_POST){			
			$video=M("users_video");
			$video->create();
			$video->addtime=time();
			$video->uid=0;
			
			$owner=$_POST['owner'];
			$owner_uid=(int)$_POST['owner_uid'];

			if($owner==1){

				if($owner_uid==""||!is_numeric($owner_uid)){
					$this->error("请填写视频所有者id");
					return;
				}

				//判断用户是否存在
				$ownerInfo=M("users")->where("user_type=2 and id={$owner_uid}")->find();
				if(!$ownerInfo){
					$this->error("视频所有者不存在");
					return;
				}

				$video->uid=$owner_uid;

			}



			$url=$_POST['href'];
			
			$title=$_POST['title'];
			$thumb=$_POST['thumb'];

			if($title==""){
				$this->error("请填写视频标题");
			}

			if($thumb==""){
				$this->error("请上传视频封面");
			}

			$video->thumb_s=$_POST['thumb'];

			if($url!=""){

				//判断链接地址的正确性
				if(strpos($url,'http')!==false||strpos($url,'https')!==false){

					$video_type=substr(strrchr($url, '.'), 1);
					if(strtolower($video_type)!='mp4'){
						$this->error("文件名后缀必须为mp4格式");
					}

					$video->href=$url;

				}else{

					$this->error("请填写正确的视频地址");

				}

				
				

			}else{



				//获取后台上传配置
				$configpri=getConfigPri();

				$show_val=$configpri['show_val'];

				$video->show_val=$show_val;

				//var_dump($configpri);
				if($configpri['cloudtype']==1){  //七牛云存储

					$savepath=date('Ymd').'/';
					//上传处理类
		            $config=array(
		            		'rootPath' => './'.C("UPLOADPATH"),
		            		'savePath' => $savepath,
		            		'maxSize' => 100*1048576, //100M
		            		'saveName'   =>    array('uniqid',''),
		            		'exts'       =>    array('mp4'),
		            		'autoSub'    =>    false,
		            );

					$config_qiniu = array(
 
						'accessKey' => $configpri['qiniu_accesskey'], //这里填七牛AK
						'secretKey' => $configpri['qiniu_secretkey'], //这里填七牛SK
						'domain' => $configpri['qiniu_domain'],//这里是域名
						'bucket' => $configpri['qiniu_bucket']//这里是七牛中的“空间”
					);


		            $upload = new \Think\Upload($config,'Qiniu',$config_qiniu);
		            $info = $upload->upload();
					
		            if ($info) {
		                //上传成功
		                //写入附件数据库信息
		                $first=array_shift($info);
		                if(!empty($first['url'])){
		                	$url=$first['url'];
		                	
		                }else{
		                	$url=C("TMPL_PARSE_STRING.__UPLOAD__").$savepath.$first['savename'];
		                	
		                }
		                
						/*echo "1," . $url.",".'1,'.$first['name'];
						exit;*/


		            } else {
		                //上传失败，返回错误
		                //exit("0," . $upload->getError());
		                $this->error('添加失败');
		            }



				}else if($configpri['cloudtype']==2){ //腾讯云存储

					/* 腾讯云 */
					require(SITE_PATH.'api/public/txcloud/include.php');
					//bucketname
					$bucket = $configpri['txcloud_bucket'];

					$src = $_FILES["file"]["tmp_name"];
					
					//var_dump("src".$src);

					//cosfolderpath
					$folder = '/'.$configpri['txvideofolder'];
					//cospath
					$dst = $folder.'/'.$_FILES["file"]["name"];
					//config your information
					$config = array(
						'app_id' => $configpri['txcloud_appid'],
						'secret_id' => $configpri['txcloud_secret_id'],
						'secret_key' => $configpri['txcloud_secret_key'],
						'region' => $configpri['txcloud_region'],   // bucket所属地域：华北 'tj' 华东 'sh' 华南 'gz'
						'timeout' => 60
					);

					date_default_timezone_set('PRC');
					$cosApi = new 	Api($config);

					$ret = $cosApi->upload($bucket, $src, $dst);

					

					if($ret['code']!=0){
						//上传失败，返回错误
						//exit("0," . $ret['message']);
						$this->error('添加失败');
					}

					$url = $ret['data']['source_url'];
					
					
				}


				$video->href=$url;
			}

				


			$result=$video->add();

			if($result){
				$this->success('添加成功','Admin/Video/passindex',3);
			}else{
				$this->error('添加失败');
			}
		}			
	}

	function edit(){
		$id=intval($_GET['id']);
		$from=I("from");
		if($id){
			$video=M("users_video")->where("id={$id}")->find();
			if($video['uid']==0){
				$userinfo=array(
					'user_nicename'=>'系统管理员'
				);
			}else{
				$userinfo=getUserInfo($video['uid']);
				if(!$userinfo){
					$userinfo=array(
						'user_nicename'=>'已删除'
					);
				}
			}
			
			$video['userinfo']=$userinfo;
			$this->assign('video', $video);						
		}else{				
			$this->error('数据传入失败！');
		}
		$this->assign("from",$from);							  
		$this->display();				
	}
	
	function edit_post(){
		if(IS_POST){

			$video=M("users_video");
			$video->create();

			$id=(int)$_POST['id'];
			$title=$_POST['title'];
			$thumb=$_POST['thumb'];
			$type=$_POST['video_upload_type'];
			$url=$_POST['href'];
			$status=$_POST['status'];
			$isdel=$_POST['isdel'];
			$nopasstime=$_POST['nopasstime'];
			


			/*if($title==""){
				$this->error("请填写视频标题");
			}*/

			if($thumb==""){
				$this->error("请上传视频封面");
			}

			$video->thumb_s=$_POST['thumb'];

			if($type!=''){

				if($type==0){ //视频链接型式
					if($url==''){
						$this->error("请填写视频链接地址");
					}

					//判断链接地址的正确性
					if(strpos($url,'http')!==false||strpos($url,'https')!==false){

						$video_type=substr(strrchr($url, '.'), 1);

						if(strtolower($video_type)!='mp4'){
							$this->error("文件名后缀必须为mp4格式");
						}

						$video->href=$url;

					}else{

						$this->error("请填写正确的视频地址");

					}


				}else if($type==1){ //文件上传型式

					$savepath=date('Ymd').'/';

					//获取后台上传配置
					$configpri=getConfigPri();

					//var_dump($configpri);
					if($configpri['cloudtype']==1){  //七牛云存储


						//上传处理类
			            $config=array(
			            		'rootPath' => './'.C("UPLOADPATH"),
			            		'savePath' => $savepath,
			            		'maxSize' => 100*1048576, //100M
			            		'saveName'   =>    array('uniqid',''),
			            		'exts'       =>    array('mp4'),
			            		'autoSub'    =>    false,
			            );

						$config_qiniu = array(
	 
							'accessKey' => $configpri['qiniu_accesskey'], //这里填七牛AK
							'secretKey' => $configpri['qiniu_secretkey'], //这里填七牛SK
							'domain' => $configpri['qiniu_domain'],//这里是域名
							'bucket' => $configpri['qiniu_bucket']//这里是七牛中的“空间”
						);


			            $upload = new \Think\Upload($config,'Qiniu',$config_qiniu);


			            $info = $upload->upload();

			            if ($info) {
			                //上传成功
			                //写入附件数据库信息
			                $first=array_shift($info);
			                if(!empty($first['url'])){
			                	$url=$first['url'];
			                	
			                }else{
			                	$url=C("TMPL_PARSE_STRING.__UPLOAD__").$savepath.$first['savename'];
			                	
			                }
			                
							/*echo "1," . $url.",".'1,'.$first['name'];
							exit;*/


			            } else {
			                //上传失败，返回错误
			                //exit("0," . $upload->getError());
			                $this->error('视频文件上传失败');
			            }



					}else if($configpri['cloudtype']==2){ //腾讯云存储

						/* 腾讯云 */
						require(SITE_PATH.'api/public/txcloud/include.php');
						//bucketname
						$bucket = $configpri['txcloud_bucket'];

						$src = $_FILES["file"]["tmp_name"];
						
						//var_dump("src".$src);

						//cosfolderpath
						$folder = '/'.$configpri['txvideofolder'];
						//cospath
						$dst = $folder.'/'.$_FILES["file"]["name"];
						//config your information
						$config = array(
							'app_id' => $configpri['txcloud_appid'],
							'secret_id' => $configpri['txcloud_secret_id'],
							'secret_key' => $configpri['txcloud_secret_key'],
							'region' => $configpri['txcloud_region'],   // bucket所属地域：华北 'tj' 华东 'sh' 华南 'gz'
							'timeout' => 60
						);

						date_default_timezone_set('PRC');
						$cosApi = new 	Api($config);

						$ret = $cosApi->upload($bucket, $src, $dst);

						

						if($ret['code']!=0){
							//上传失败，返回错误
							//exit("0," . $ret['message']);
							$this->error('视频文件上传失败');
						}

						$url = $ret['data']['source_url'];
						
						
					}


				}


				$video->href=$url;
			}else{

				//获取该视频的href
				$url=$video->where("id={$id}")->getField("href");

				$video->href=$url;
			}

			if($status==2){
				$video->nopass_time=time();
			}

			//审核通过给该视频添加曝光值（改为接口添加视频时直接添加曝光值）
			// if($status==1){
			// 	$video->show_val=100;
			// }

			$result=$video->save();

			if($result!==false){

				if($status==2||$isdel==1){  //如果该视频下架或视频状态改为不通过，需要将视频喜欢列表的状态更改
					M("users_video_like")->where("videoid={$id}")->setField('status',0);
				}

				if($status==2&&$nopasstime==0){  //视频状态为审核不通过且为第一次审核为不通过，发送极光IM

					$videoInfo=M("users_video")->where("id={$id}")->find();

					$id=$_SESSION['ADMIN_ID'];
					$user=M("Users")->where("id='{$id}'")->find();

		    		//向系统通知表中写入数据
		    		/* $sysInfo=array(
		    			'title'=>'视频未审核通过提醒',
		    			'addtime'=>time(),
		    			'admin'=>$user['user_login'],
		    			'ip'=>get_client_ip(0,true),
		    			'uid'=>$videoInfo['uid']

		    		);

		    		$baseMsg='您于'.date("Y-m-d H:i:s",$videoInfo['addtime']).'上传的《'.$videoInfo['title'].'》视频被管理员于'.date("Y-m-d H:i:s",time()).'审核为不通过';;

		    		
		    		$sysInfo['content']=$baseMsg;
		    		

		    		$result1=M("system_push")->add($sysInfo);

		    		if($result1!==false){

		    			$text="视频未审核通过提醒";
		    			$uid=$videoInfo['uid'];
		    			jMessageIM($text,$uid);

		    		} */

				}

				$this->success('修改成功');
			 }else{
				$this->error('修改失败');
			 }
		}			
	}
	
    function reportlist(){

		if($_REQUEST['status']!=''){
			$map['status']=$_REQUEST['status'];
			$_GET['status']=$_REQUEST['status'];
		}
		if($_REQUEST['start_time']!=''){
			$map['addtime']=array("gt",strtotime($_REQUEST['start_time']));
			$_GET['start_time']=$_REQUEST['start_time'];
		}

		if($_REQUEST['end_time']!=''){
		 
			$map['addtime']=array("lt",strtotime($_REQUEST['end_time']));
			$_GET['end_time']=$_REQUEST['end_time'];
		}
		if($_REQUEST['start_time']!='' && $_REQUEST['end_time']!='' ){
		 
			$map['addtime']=array("between",array(strtotime($_REQUEST['start_time']),strtotime($_REQUEST['end_time'])));
			$_GET['start_time']=$_REQUEST['start_time'];
			$_GET['end_time']=$_REQUEST['end_time'];
		}

		if($_REQUEST['keyword']!=''){
			$map['uid']=array("like","%".$_REQUEST['keyword']."%"); 
			$_GET['keyword']=$_REQUEST['keyword'];
		}

		$p=I("p");
		if(!$p){
			$p=1;
		}		
			
    	$Report=M("users_video_report");
    	$Users=M("users");
    	$count=$Report->where($map)->count();
    	$page = $this->page($count, 20);
    	$lists = $Report
			->where($map)
			->order("addtime DESC")
			->limit($page->firstRow . ',' . $page->listRows)
			->select();
			
		foreach($lists as $k=>$v){
			$userinfo=$Users->field("user_nicename")->where("id='{$v[uid]}'")->find();
			if(!$userinfo){
				$userinfo=array(
					'user_nicename'=>'已删除'
				);
			}
			$lists[$k]['userinfo']= $userinfo;
			$touserinfo=$Users->field("user_nicename")->where("id='{$v[touid]}'")->find();
			if(!$touserinfo){
				$touserinfo=array(
					'user_nicename'=>'已删除'
				);
			}
			$lists[$k]['touserinfo']= $touserinfo;
		}			
			
    	$this->assign('lists', $lists);
    	$this->assign('formget', $_GET);
    	$this->assign("page", $page->show('Admin'));
    	$this->assign("p",$p);
    	$this->display();
    }
		
	function setstatus(){
		$id=intval($_GET['id']);
		if($id){
			$data['status']=1;
			$data['uptime']=time();
			$result=M("users_video_report")->where("id='{$id}'")->save($data);				
			if($result!==false){
				$this->success('标记成功');
			}else{
				$this->error('标记失败');
			}			
		}else{				
			$this->error('数据传入失败！');
		}								  		
	}		
	//删除用户举报列表
	function report_del(){
		$id=intval($_GET['id']);
		if($id){
			$result=M("users_video_report")->delete($id);				
			if($result){
				$this->success('删除成功');
			}else{
				$this->error('删除失败');
			}			
		}else{				
			$this->error('数据传入失败！');
		}								  
	}	
	//举报内容设置**************start******************
	
	//举报类型列表
	
	function reportset(){
		$report=M("users_video_report_classify");
		$lists = $report
			->order("orderno ASC")
			->select();
			
		$this->assign('lists', $lists);
		$this->display();
	}
	//添加举报理由
	function add_report(){
		
		$this->display();
	}
	function add_reportpost(){
		
		if(IS_POST){			
			$report=M("users_video_report_classify");
			
			$name=I("name");//举报类型名称
			if(!trim($name)){
				$this->error('举报类型名称不能为空');
			}
			$isexit=M("users_video_report_classify")->where(['name'=>$name])->find();	
			if($isexit){
				$this->error('该举报类型名称已存在');
			}
			
			$report->create();
			$report->addtime=time();
			$result=$report->add(); 
			if($result){
				$this->success('添加成功');
			}else{
				$this->error('添加失败');
			}
		}	
	}
	//编辑举报类型名称
	function edit_report(){
		$id=intval($_GET['id']);
		if($id){
			$reportinfo=M("users_video_report_classify")->where("id={$id}")->find();
			
			$this->assign('reportinfo', $reportinfo);						
		}else{				
			$this->error('数据传入失败！');
		}								  
		$this->display();				
	}
	
	function edit_reportpost(){
		if(IS_POST){			
			$report=M("users_video_report_classify");
			
			$id=(int)I("id");
			$name=I("name");//举报类型名称
			if(!trim($name)){
				$this->error('举报类型名称不能为空');
			}
            $where['id']=array('neq',$id);
            $where['name']=$name;
            
			$isexit=M("users_video_report_classify")->where($where)->find();	
			if($isexit){
				$this->error('该举报类型名称已存在');
			}
			
			$report->create();
			$result=$report->save(); 
			if($result!==false){
				  $this->success('修改成功');
			 }else{
				  $this->error('修改失败');
			 }
		}			
	}
	//删除举报类型名称
	function del_report(){
		$id=intval($_GET['id']);
		if($id){
			$result=M("users_video_report_classify")->where("id={$id}")->delete();				
			if($result!==false){
				$this->success('删除成功');
			}else{
				$this->error('删除失败');
			}			
		}else{				
			$this->error('数据传入失败！');
		}								  
		$this->display();		
	}
	  //举报内容排序
    public function listordersset() { 
		
        $ids = $_POST['listorders'];
        foreach ($ids as $key => $r) {
            $data['orderno'] = $r;
            M("users_video_report_classify")->where(array('id' => $key))->save($data);
        }
				
        $status = true;
        if ($status) {
            $this->success("排序更新成功！");
        } else {
            $this->error("排序更新失败！");
        }
    }	
//举报内容设置**************end******************	
//
    //设置下架
    public function setXiajia(){
    	$res=array("code"=>0,"msg"=>"下架成功","info"=>array());
    	$id=(int)I("id");
    	$reason=I("reason");
    	if(!$id){
    		$res['code']=1001;
    		$res['msg']="请确认视频信息";
    		echo json_encode($res);
    		exit;
    	}

    	//判断此视频是否存在
    	$videoInfo=M("users_video")->where("id={$id}")->find();
    	if(!$videoInfo){
    		$res['code']=1001;
    		$res['msg']="请确认视频信息";
    		echo json_encode($res);
    		exit;
    	}

    	//更新视频状态
    	$data=array("isdel"=>1,"xiajia_reason"=>$reason);

    	$result=M("users_video")->where("id={$id}")->save($data);

    	if($result!==false){

    		//将视频喜欢列表的状态更改
    		M("users_video_like")->where("videoid={$id}")->setField('status',0);

    		//更新此视频的举报信息
    		$data1=array(
    			'status'=>1,
    			'uptime'=>time()
    		);

    		M("users_video_report")->where("videoid={$id}")->save($data1);

    		//$uid=(int)$_SESSION['ADMIN_ID'];
			//$user=M("Users")->where("id='{$uid}'")->find();

    		//向系统通知表中写入数据
    		/* $sysInfo=array(
    			'title'=>'视频下架提醒',
    			'addtime'=>time(),
    			'admin'=>$user['user_login'],
    			'ip'=>get_client_ip(0,true),
    			'uid'=>$videoInfo['uid']

    		);

    		$baseMsg='您于'.date("Y-m-d H:i:s",$videoInfo['addtime']).'上传的《'.$videoInfo['title'].'》视频被管理员于'.date("Y-m-d H:i:s",time()).'下架';;

    		if(!$reason){
    			$sysInfo['content']=$baseMsg;
    		}else{
    			$sysInfo['content']=$baseMsg.',下架原因为：'.$reason;
    		}

    		$result1=M("system_push")->add($sysInfo);


    		if($result1!==false){

    			$text="视频下架提醒";
    			$uid=$videoInfo['uid'];
    			jMessageIM($text,$uid);

    		} */

    		


    		echo json_encode($res);
    		exit;
    	}else{
    		$res['code']=1002;
    		$res['msg']="下架失败";
    		echo json_encode($res);
    		exit;
    	}
    	
    }

    /*下架视频列表*/
    public  function lowervideo(){

    	if($_REQUEST['ordertype']!=''){
			$ordertype=$_REQUEST['ordertype'];
			$_GET['ordertype']=$_REQUEST['ordertype'];
		}
		 $map['isdel']=1;
		 $map['is_ad']=0;  
		
		if($_REQUEST['keyword']!=''){
			$map['uid|id']=array("eq",$_REQUEST['keyword']); 
			$_GET['keyword']=$_REQUEST['keyword'];
		}		
		if($_REQUEST['keyword1']!=''){
			$map['title']=array("like","%".$_REQUEST['keyword1']."%");  
			$_GET['keyword1']=$_REQUEST['keyword1'];
		}
		//用户名称
		if($_REQUEST['keyword2']!=''){
			/* $map['title']=array("like","%".$_REQUEST['keyword2']."%");   */
			$_GET['keyword2']=$_REQUEST['keyword2'];
			$username=$_REQUEST['keyword2'];
            
            $where['user_nicename']=array('like',"%".$username."%");
            
			$userlist =M("users")->field("id")->where($where)->select();
			$strids="";
			foreach($userlist as $ku=>$vu){
				if($strids==""){
					$strids=$vu['id'];
				}else{
					$strids.=",".$vu['id'];
				}
			}
			$map['uid']=array("in",$strids);
		}

		$p=I("p");
		if(!$p){
			$p=1;
		}
		
		
    	$video_model=M("users_video");
    	$count=$video_model->where($map)->count();
    	$page = $this->page($count, 20);
		$orderstr="";
		if($ordertype==1){//评论数排序
			$orderstr="comments DESC";
		}else if($ordertype==2){//点赞数量排序
			$orderstr="likes DESC";
		}else if($ordertype==3){//分享数量排序
			$orderstr="shares DESC";
		}else{
			$orderstr="addtime DESC";
		}
		
    	$lists = $video_model
			->where($map)
			->order($orderstr)
			->limit($page->firstRow . ',' . $page->listRows)
			->select();
		foreach($lists as $k=>$v){
			if($v['uid']==0){
				$userinfo=array(
					'user_nicename'=>'系统管理员'
				);
			}else{
				$userinfo=getUserInfo($v['uid']);
				if(!$userinfo){
					$userinfo=array(
						'user_nicename'=>'已删除'
					);
				}
				
			}

			
			$lists[$k]['userinfo']=$userinfo;
			
			$hasurgemoney=($v['big_urgenums']-$v['urge_nums'])*$v['urge_money'];
			$lists[$k]['hasurgemoney']=$hasurgemoney;
		}
    	$this->assign('lists', $lists);
		$this->assign('formget', $_GET);
    	$this->assign("page", $page->show('Admin'));
    	$this->assign("p",$p);
    	$this->display();
    }


    public function  video_listen(){
    	$id=(int)I("id");
    	if(!$id||$id==""||!is_numeric($id)){
    		$this->error("加载失败");
    	}else{
    		//获取音乐信息
    		$info=M("users_video")->where("id={$id}")->find();
    		$this->assign("info",$info);
    	}

    	$this->display();
    }


    /*视频上架*/
    public function set_shangjia(){
    	$id=(int)I("id");
    	if(!$id){
    		$this->error("视频信息加载失败");
    	}

    	//获取视频信息
    	$info=M("users_video")->where("id={$id}")->find();
    	if(!$info){
    		$this->error("视频信息加载失败");
    	}

    	$data=array(
    		'xiajia_reason'=>'',
    		'isdel'=>0
    	);
    	$result=M("users_video")->where("id={$id}")->save($data);
    	if($result!==false){

    		//将视频喜欢列表的状态更改
    		M("users_video_like")->where("videoid={$id}")->setField('status',1);

    		$this->success("上架成功");
    	}
    	$this->display();
    }

    public function commentlists(){
    	
    	$videoid=I("videoid");
    	$video_comment=M("users_video_comments");
    	$map=array();
    	//$map['parentid']=0;
    	$map['videoid']=$videoid;
    	$count=$video_comment->where($map)->count();
    	$page = $this->page($count, 20);
    	//获取一级评论列表
    	$lists=$video_comment->where($map)->order("addtime desc")->limit($page->firstRow . ',' . $page->listRows)->select();

    	//var_dump($video_comment->getLastSql());
    	foreach ($lists as $k => $v) {
    		$lists[$k]['user_nicename']=M("users")->where("id={$v['uid']}")->getField("user_nicename");
    		/*$secondComments=$video_comment->where("videoid={$videoid} and commentid={$v['id']}")->select();
    		foreach ($secondComments as $k1 => $v1) {
    			$secondComments[$k1]['user_nicename']=M("users")->where("id={$v1['uid']}")->getField("user_nicename");
    			$lists[$k]['secondComments']=$secondComments;
    		}*/
    	}

    	//var_dump($lists);

    	$this->assign("lists",$lists);
    	$this->assign("page", $page->show('Admin'));
    	$this->display();

    }
}
