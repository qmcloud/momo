<?php

/**
 * 背景音乐管理
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
use QCloud\Cos\Api;
use QCloud\Cos\Auth;

class MusicController extends AdminbaseController {

	/*分类添加*/
	function classify_add(){

		$this->display();
	}

	/*分类添加提交*/
	function classify_add_post(){

		if(IS_POST){

			$classify=M("users_music_classify");
			$classify->create();
			$classify->addtime=time();
			

			$url=$_POST['img_url'];
			$title=trim($_POST['title']);
			$orderno=$_POST['orderno'];

			if($title==""){
				$this->error("请填写分类名称");
			}

			if($url==""){
				$this->error("请上传分类图标");
			}

			if(!is_numeric($orderno)){
				$this->error("排序号请填写数字");
			}

			if($orderno<0){
				$this->error("排序号必须大于0");
			}

			$classify->orderno=$orderno;



			$classify->img_url=$url;
			
			$isexit=$classify->where(["title"=>$title])->find();	
			if($isexit){
				$this->error('该分类已存在');
			}

			$result=$classify->add();

			if($result){
				$this->success('添加成功','Admin/Music/classify',3);
			}else{
				$this->error('添加失败');
			}
		}

	}


	//分类排序
    function classify_listorders() { 
		
        $ids = $_POST['listorders'];
        foreach ($ids as $key => $r) {
            $data['orderno'] = $r;
            M("users_music_classify")->where(array('id' => $key))->save($data);
        }
				
        $status = true;
        if ($status) {
            $this->success("排序更新成功！");
        } else {
            $this->error("排序更新失败！");
        }
    }

    /*分类列表*/
	function classify(){


		if($_REQUEST['keyword']!=''){
			$map['title']=array("like","%".$_REQUEST['keyword']."%");  
			$_GET['keyword']=$_REQUEST['keyword'];
		}
			
			
    	$classify=M("users_music_classify");
    	$count=$classify->where($map)->count();
    	$page = $this->page($count, 20);
    	$lists = $classify
			->where($map)
			->order("orderno,addtime DESC")
			->limit($page->firstRow . ',' . $page->listRows)
			->select();
            
        foreach($lists as $k=>$v){
            $v['img_url']=get_upload_path($v['img_url']);
            
            $lists[$k]=$v;
        }
		
    	$this->assign('lists', $lists);
		$this->assign('formget', $_GET);
    	$this->assign("page", $page->show('Admin'));
		$this->display();
	}

	/*分类删除*/
	function classify_del(){

		$id=intval($_GET['id']);
		if($id){
			$result=M("users_music_classify")->where(["id"=>$id])->save(array("isdel"=>1));				
			if($result){
				$this->success('删除成功');
			}else{
				$this->error('删除失败');
			}			
		}else{				
			$this->error('数据传入失败！');
		}
	}

	/*分类取消删除*/
	function classify_canceldel(){

		$id=intval($_GET['id']);
		if($id){
			$result=M("users_music_classify")->where(["id"=>$id])->save(array("isdel"=>0));				
			if($result){
				$this->success('取消成功');
			}else{
				$this->error('取消失败');
			}			
		}else{				
			$this->error('数据传入失败！');
		}
	}

	/*分类编辑*/
	function classify_edit(){
		$id=intval($_GET['id']);
		if($id){
			$info=M("users_music_classify")->where(["id"=>$id])->find();

			$this->assign("classify_info",$info);
		}else{
			$this->error('数据传入失败！');
		}
		
		$this->display();
	}

	/*分类编辑提交*/
	function classify_edit_post(){

		if(IS_POST){			
			$classify=M("users_music_classify");
			
			$id=(int)I("id");
			$title=I("title");
			$orderno=I("orderno");
			$url=I("img_url");

			if(!trim($title)){
				$this->error('分类标题不能为空');
			}

			if($url==""){
				$this->error("请上传分类图标");
			}

			if(!is_numeric($orderno)){
				$this->error("排序号请填写数字");
			}

			if($orderno<0){
				$this->error("排序号必须大于0");
			}
            
            $where['id']=array('neq',$id);
            $where['title']=$title;
			$isexit=$classify->where($where)->find();	
			if($isexit){
				$this->error('该分类已存在');
			}
			
			$classify->create();
			$result=$classify->save();
			if($result!==false){
				  $this->success('修改成功');
			 }else{
				  $this->error('修改失败');
			 }
		}

	}


	/*背景音乐*/
    function index(){

		$map=array();
			
		
	 	if($_REQUEST['classify_id']!=''){
			$map['classify_id']=$_REQUEST['classify_id']; 
			$_GET['classify_id']=$_REQUEST['classify_id'];
		}

		if($_REQUEST['upload_type']!=''){
			$map['upload_type']=$_REQUEST['upload_type']; 
			$_GET['upload_type']=$_REQUEST['upload_type'];
		}

		if($_REQUEST['keyword']!=''){
			$map['title']=array("like","%".$_REQUEST['keyword']."%");
			$_GET['keyword']=$_REQUEST['keyword'];
		}
		
    	$music=M("users_music");
    	$classifyObj=M("users_music_classify");
    	$count=$music->where($map)->count();
    	$page = $this->page($count, 20);
    	$lists = $music
			->where($map)
			->order("use_nums desc")
			->limit($page->firstRow . ',' . $page->listRows)
			->select();

		//var_dump($music->getLastSql());

		foreach ($lists as $k => $v) {
			$lists[$k]['classify_title']=$classifyObj->where(["id"=>$v['classify_id']])->getField("title");
			$lists[$k]['uploader_nicename']=M("users")->where(["id"=>$v['uploader']])->getField("user_nicename");
            
			$lists[$k]['img_url']=get_upload_path($v['img_url']);
			$lists[$k]['file_url']=get_upload_path($v['file_url']);
		}
		
		$classify=$classifyObj->order("orderno")->select();

    	$this->assign('lists', $lists);
    	$this->assign('classify', $classify);
    	$this->assign('formget', $_GET);
    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display();
    }

    /*背景音乐添加*/
    function music_add(){
    	$classify=M("users_music_classify")->order("orderno")->select();
    	$this->assign('classify', $classify);
    	$this->display();
    }

    /*背景音乐添加保存*/
    function music_add_post(){



    	if(IS_POST){

    		$music=M("users_music");
			$music->create();
			$music->addtime=time();
			$music->upload_type=1;
			$music->uploader=get_current_admin_id(); //当前管理员id
			

			$img_url=$_POST['img_url'];
			$title=$_POST['title'];
			$author=$_POST['author'];
			$length=$_POST['length'];
			$use_nums=$_POST['use_nums'];

			if($title==""){
				$this->error("请填写音乐名称");
			}

			//判断该音乐是否存在
			$isexist=$music->where(["title"=>$title])->find();

			if($isexist){
				$this->error("该音乐已经存在");
			}

			if($author==""){
				$this->error("请填写演唱者");
			}

			if($img_url==""){
				$this->error("请上传音乐封面");
			}

			if($length==""){
				$this->error("请填写音乐时长");
			}

			if(!strpos($length,":")){
				$this->error("请按照格式填写音乐时长");
			}

			if(!is_numeric($use_nums)||$use_nums<0){
				$this->error("使用次数请写正整数");
			}

			//获取后台上传配置
			$configpri=getConfigPri();

			//var_dump($configpri);
			if($configpri['cloudtype']==1){  //七牛云存储

				$savepath=date('Ymd').'/';
				//上传处理类
	            $config=array(
	            		'rootPath' => './'.C("UPLOADPATH"),
	            		'savePath' => $savepath,
	            		'maxSize' => 100*1048576, //100M
	            		'saveName'   =>    array('uniqid',''),
	            		'exts'       =>    array('mp3','MP3'),
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


			$music->file_url=$url;
			

				


			$result=$music->add();

			if($result){
				$this->success('添加成功','Admin/Music/music_add',3);
			}else{
				$this->error('添加失败');
			}

    	}

    }



    /*音乐试听*/

    function music_listen(){

    	$id=(int)I("id");
    	if(!$id||$id==""||!is_numeric($id)){
    		$this->error("加载失败");
    	}else{
    		//获取音乐信息
    		$info=M("users_music")->where(["id"=>$id])->find();
    		$this->assign("info",$info);
    	}

    	$this->display();
    }

    /*音乐删除*/
    function music_del(){
    	$id=(int)I("id");
    	if(!$id||$id==""||!is_numeric($id)){
    		$this->error("操作失败");
    	}else{
    		$count=M("users_video")->where(["music_id"=>$id])->count();
    		if($count>0){
    			$result=M("users_music")->where(["id"=>$id])->save(array("isdel"=>1));
    		}else{
    			$result=M("users_music")->where(["id"=>$id])->delete();
    		}				
			if($result){
				$this->success('删除成功');
			}else{
				$this->error('删除失败');
			}
    	}
    }


    /*取消删除*/
    function music_canceldel(){
    	$id=(int)I("id");
    	if(!$id||$id==""||!is_numeric($id)){
    		$this->error("操作失败");
    	}else{
    		$result=M("users_music")->where(["id"=>$id])->save(array("isdel"=>0));				
			if($result){
				$this->success('取消成功');
			}else{
				$this->error('取消失败');
			}
    	}
    }

    /*音乐编辑*/
    function music_edit(){

    	$id=(int)I("id");
    	if($id==""){
    		$this->error("操作失败");
    	}else{

    		$music=M("users_music");
    		$info=$music->where(["id"=>$id])->find();
    		$this->assign("info",$info);

    		$classify=M("users_music_classify")->order("orderno")->select();
    		$this->assign("classify",$classify);
    	}
    	$this->display();
    }


    function music_edit_post(){

    	if(IS_POST){

    		$music=M("users_music");
			$music->create();

			$music->updatetime=time();

			$id=(int)$_POST['id'];
			$img_url=$_POST['img_url'];
			$title=$_POST['title'];
			$author=$_POST['author'];
			$length=$_POST['length'];
			$use_nums=$_POST['use_nums'];
	

			if($title==""){
				$this->error("请填写音乐名称");
			}

			//判断该音乐是否存在
            $where['id']=array('neq',$id);
            $where['title']=$title;
			$isexist=$music->where($where)->find();

			if($isexist){
				$this->error("该音乐已经存在");
			}

			if($author==""){
				$this->error("请填写演唱者");
			}

			if($img_url==""){
				$this->error("请上传音乐封面");
			}

			if($length==""){
				$this->error("请填写音乐时长");
			}

			if(!strpos($length,":")){
				$this->error("请按照格式填写音乐时长");
			}

			if(!is_numeric($use_nums)||$use_nums<0){
				$this->error("使用次数请写正整数");
			}


			if($_FILES){


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
		            		'exts'       =>    array('mp3','MP3'),
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
		                $this->error('音频文件上传失败');
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
						$this->error('音频文件上传失败');
					}

					$url = $ret['data']['source_url'];
					
					
				}


				$music->file_url=$url;
			}


			$result=$music->save();

			if($result!==false){
				  $this->success('修改成功');
			 }else{
				  $this->error('修改失败');
			 }

    	}

    }


	

}
