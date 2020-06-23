<?php
namespace Asset\Controller;
use Think\Controller;
class UeditorController extends Controller {
	
	
	private $stateMap = array( //上传状态映射表，国际化用户需考虑此处数据的国际化
        "SUCCESS", //上传成功标记，在UEditor中内不可改变，否则flash判断会出错
        "文件大小超出 upload_max_filesize 限制",
        "文件大小超出 MAX_FILE_SIZE 限制",
        "文件未被完整上传",
        "没有文件被上传",
        "上传文件为空",
        "ERROR_TMP_FILE" => "临时文件错误",
        "ERROR_TMP_FILE_NOT_FOUND" => "找不到临时文件",
        "ERROR_SIZE_EXCEED" => "文件大小超出网站限制",
        "ERROR_TYPE_NOT_ALLOWED" => "文件类型不允许",
        "ERROR_CREATE_DIR" => "目录创建失败",
        "ERROR_DIR_NOT_WRITEABLE" => "目录没有写权限",
        "ERROR_FILE_MOVE" => "文件保存时出错",
        "ERROR_FILE_NOT_FOUND" => "找不到上传文件",
        "ERROR_WRITE_CONTENT" => "写入文件内容错误",
        "ERROR_UNKNOWN" => "未知错误",
        "ERROR_DEAD_LINK" => "链接不可用",
        "ERROR_HTTP_LINK" => "链接不是http链接",
        "ERROR_HTTP_CONTENTTYPE" => "链接contentType不正确"
    );
	
	function _initialize() {
		$adminid=sp_get_current_admin_id();
		$userid=sp_get_current_userid();
		if(empty($adminid) && empty($userid)){
			exit("非法上传！");
		}
	}
	
	/**
	 * ueditor 1.3.6 upload img
	 */
	public function uploadimg(){
		
		
		
		//上传处理类
		$config=array(
				'rootPath' => './'. C("UPLOADPATH"),
				'savePath' => "ueditor/",
				'maxSize' => 11048576,
				'saveName'   =>    array('uniqid',''),
				'exts'       =>    array('jpg', 'gif', 'png', 'jpeg'),
				'autoSub'    =>    false,
		);
		$upload = new \Think\Upload($config);// 
		
		$file = $title = $oriName = $state ='0';

		$info=$upload->upload();
		
		//开始上传
		if ($info) {
			
			
			//上传成功
			$title = $oriName = $info['upfile']['name'];
			
			$state = 'SUCCESS';
			$file = C("TMPL_PARSE_STRING.__UPLOAD__")."ueditor/".$info['upfile']['savename'];
			if(strpos($file, "https")===0 || strpos($file, "http")===0){
				
			}else{//local
				$host=(is_ssl() ? 'https' : 'http')."://".$_SERVER['HTTP_HOST'];
				$file=$host.$file;
			}
		} else {
			$state = $upload->getError();
		}
		$response= "{'url':'" .$file . "','title':'" . $title . "','original':'" . $oriName . "','state':'" . $state . "'}";
		exit($response);
	}
	
	public function imageManager(){
		error_reporting(E_ERROR|E_WARNING);
		$path = 'upload'; //最好使用缩略图地址，否则当网速慢时可能会造成严重的延时
		$action = htmlspecialchars($_POST["action"]);
		if($action=="get"){
			$files = $this->getfiles($path);
			if(!$files)return;
			$str = "";
			foreach ($files as $file) {
				$str .= $file."ue_separate_ue";
			}
			echo $str;
		}
	}
	
	//imageManager()用的到
	private function getfiles(){
		if (!is_dir($path)) return;
		
		$handle = opendir($path);
		while (false !== ($file = readdir($handle))) {
			if ($file != '.' && $file != '..') {
				$path2 = $path . '/' . $file;
				if (is_dir($path2)) {
					getfiles($path2, $files);
				} else {
					if (preg_match("/\.(gif|jpeg|jpg|png|bmp)$/i", $file)) {
						$files[] = $path2;
					}
				}
			}
		}return $files;
	}
	
	/**
	 * ueditor 1.3.6 get remote image
	 */
	public function get_remote_image(){
		$uri = htmlspecialchars( $_POST[ 'upfile' ] );
		$uri = str_replace( "&amp;" , "&" , $uri );
		//远程抓取图片配置
		$config = array(
				"savePath" => './'. C("UPLOADPATH")."ueditor/",            //保存路径
				"allowFiles" => array( ".gif" , ".png" , ".jpg" , ".jpeg" , ".bmp" ) , //文件允许格式
				"maxSize" => 3000                    //文件大小限制，单位KB
		);
		//忽略抓取时间限制
		set_time_limit( 0 );
		//ue_separate_ue  ue用于传递数据分割符号
		$imgUrls = explode( "ue_separate_ue" , $uri );
		$tmpNames = array();
		foreach ( $imgUrls as $imgUrl ) {
			//http开头验证
			if(strpos($imgUrl,"http")!==0){
				array_push( $tmpNames , "error" );
				continue;
			}
			//获取请求头
			if(!sp_is_sae()){//SAE下无效
				$heads = get_headers( $imgUrl );
				//死链检测
				if ( !( stristr( $heads[ 0 ] , "200" ) && stristr( $heads[ 0 ] , "OK" ) ) ) {
					array_push( $tmpNames , "error" );
					continue;
				}
			}
			
		
			//格式验证(扩展名验证和Content-Type验证)
			$fileType = strtolower( strrchr( $imgUrl , '.' ) );
			if ( !in_array( $fileType , $config[ 'allowFiles' ] ) || stristr( $heads[ 'Content-Type' ] , "image" ) ) {
				array_push( $tmpNames , "error" );
				continue;
			}
		
			//打开输出缓冲区并获取远程图片
			ob_start();
			$context = stream_context_create(
					array (
							'http' => array (
									'follow_location' => false // don't follow redirects
							)
					)
			);
			//请确保php.ini中的fopen wrappers已经激活
			readfile( $imgUrl,false,$context);
			$img = ob_get_contents();
			ob_end_clean();
		
			//大小验证
			$uriSize = strlen( $img ); //得到图片大小
			$allowSize = 1024 * $config[ 'maxSize' ];
			if ( $uriSize > $allowSize ) {
				array_push( $tmpNames , "error" );
				continue;
			}
			//创建保存位置
			$savePath = $config[ 'savePath' ];
			if ( !file_exists( $savePath ) ) {
				mkdir( "$savePath" , 0777 );
			}
			$file=date("Ymdhis").uniqid() . strrchr( $imgUrl , '.' );
			//写入文件
			$tmpName = $savePath .$file ;
			$file = C("TMPL_PARSE_STRING.__UPLOAD__")."ueditor/".$file;
			if(strpos($file, "https")===0 || strpos($file, "http")===0){
			
			}else{//local
				$host=(is_ssl() ? 'https' : 'http')."://".$_SERVER['HTTP_HOST'];
				$file=$host.$file;
			}
			if(sp_file_write($tmpName,$img)){
				array_push( $tmpNames ,  $file );
			}else{
				array_push( $tmpNames , "error" );
			}
		}
		/**
		 * 返回数据格式
		 * {
		 *   'url'   : '新地址一ue_separate_ue新地址二ue_separate_ue新地址三',
		 *   'srcUrl': '原始地址一ue_separate_ue原始地址二ue_separate_ue原始地址三'，
		 *   'tip'   : '状态提示'
		 * }
		 */
		$response= "{'url':'" . implode( "ue_separate_ue" , $tmpNames ) . "','tip':'远程图片抓取成功！','srcUrl':'" . $uri . "'}";
		exit($response);
	}
	
	
	
	
	function upload(){
		date_default_timezone_set("Asia/chongqing");
		error_reporting(E_ERROR);
		header("Content-Type: text/html; charset=utf-8");
		
		$CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents("./public/js/ueditor/config.json")), true);
		$action = $_GET['action'];
		
		switch ($action) {
			case 'config':
				$result =  json_encode($CONFIG);
				break;
		
				/* 上传图片 */
			case 'uploadimage':
				/* 上传涂鸦 */
			case 'uploadscrawl':
				$result = $this->_ueditor_upload();
				break;
				/* 上传视频 */
			case 'uploadvideo':
				$result = $this->_ueditor_upload(array('maxSize' => 1073741824,/*1G*/'exts'  =>    array('mp4', 'avi', 'wmv','rm','rmvb','mkv')));
				break;
				/* 上传文件 */
			case 'uploadfile':
				$result = $this->_ueditor_upload(array('exts'  =>    array('jpg', 'gif', 'png', 'jpeg','txt','pdf','doc','docx','xls','xlsx','zip','rar','ppt','pptx',)));
				break;
		
				/* 列出图片 */
			case 'listimage':
				$result = "";
				break;
				/* 列出文件 */
			case 'listfile':
				$result = "";
				break;
		
				/* 抓取远程文件 */
			case 'catchimage':
				$result=$this->_get_remote_image();
				break;
		
			default:
				$result = json_encode(array('state'=> '请求地址出错'));
				break;
		}
		
		/* 输出结果 */
		if (isset($_GET["callback"]) && false ) {//TODO 跨域上传
			if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
				echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
			} else {
				echo json_encode(array(
						'state'=> 'callback参数不合法'
				));
			}
		} else {
			exit($result) ;
		}
	}
	
	
	
	private function _get_remote_image(){
		$source=array();
		if (isset($_POST['source'])) {
			$source = $_POST['source'];
		} else {
			$source = $_GET['source'];
		}
		
		$item=array(
				"state" => "",
				"url" => "",
				"size" => "",
				"title" => "",
				"original" => "",
				"source" =>""
		);
		$date=date("Ymd");
		//远程抓取图片配置
		$config = array(
				"savePath" => './'. C("UPLOADPATH")."ueditor/$date/",            //保存路径
				"allowFiles" => array( ".gif" , ".png" , ".jpg" , ".jpeg" , ".bmp" ) , //文件允许格式
				"maxSize" => 3000                    //文件大小限制，单位KB
		);
		
		$list = array();
		foreach ( $source as $imgUrl ) {
			$return_img=$item;
			$return_img['source']=$imgUrl;
			$imgUrl = htmlspecialchars($imgUrl);
			$imgUrl = str_replace("&amp;", "&", $imgUrl);
			//http开头验证
			if(strpos($imgUrl,"http")!==0){
				$return_img['state']=$this->stateMap['ERROR_HTTP_LINK'];
				array_push( $list , $return_img );
				continue;
			}
			//获取请求头
			if(sp_is_sae()){//SAE下无效
				$heads = get_headers( $imgUrl );
				//死链检测
				if ( !( stristr( $heads[ 0 ] , "200" ) && stristr( $heads[ 0 ] , "OK" ) ) ) {
					$return_img['state']=$this->stateMap['ERROR_DEAD_LINK'];
					array_push( $list , $return_img );
					continue;
				}
			}
				
	
			//格式验证(扩展名验证和Content-Type验证)
			$fileType = strtolower( strrchr( $imgUrl , '.' ) );
			if ( !in_array( $fileType , $config[ 'allowFiles' ] ) || stristr( $heads[ 'Content-Type' ] , "image" ) ) {
				$return_img['state']=$this->stateMap['ERROR_HTTP_CONTENTTYPE'];
				array_push( $list , $return_img );
				continue;
			}
	
			//打开输出缓冲区并获取远程图片
			ob_start();
			$context = stream_context_create(
					array (
							'http' => array (
									'follow_location' => false // don't follow redirects
							)
					)
			);
			//请确保php.ini中的fopen wrappers已经激活
			readfile( $imgUrl,false,$context);
			$img = ob_get_contents();
			ob_end_clean();

			//大小验证
			$uriSize = strlen( $img ); //得到图片大小
			$allowSize = 1024 * $config[ 'maxSize' ];
			if ( $uriSize > $allowSize ) {
				$return_img['state']=$this->stateMap['ERROR_SIZE_EXCEED'];
				array_push( $list , $return_img );
				continue;
			}

			//创建保存位置
			$savePath = $config[ 'savePath' ];
			if ( !file_exists( $savePath ) ) {
				mkdir( "$savePath" , 0777 );
			}
			$file=uniqid() . strrchr( $imgUrl , '.' );
			//写入文件
			$tmpName = $savePath .$file ;
			$file = C("TMPL_PARSE_STRING.__UPLOAD__")."ueditor/$date/".$file;
			if(strpos($file, "https")===0 || strpos($file, "http")===0){
					
			}else{//local
				$host=(is_ssl() ? 'https' : 'http')."://".$_SERVER['HTTP_HOST'];
				$file=$host.$file;
			}

			if(sp_file_write($tmpName,$img)){
				$return_img['state']='SUCCESS';
				$return_img['url']=$file;
				array_push( $list ,  $return_img );
			}else{
				$return_img['state']=$this->stateMap['ERROR_WRITE_CONTENT'];
				array_push( $list , $return_img );
			}
			
		}
		
		return json_encode(array(
				'state'=> count($list) ? 'SUCCESS':'ERROR',
				'list'=> $list
		));
	}
	
	private function _ueditor_upload($config=array()){
		
		$date=date("Ymd");
		//上传处理类
		$mconfig=array(
				'rootPath' => './'. C("UPLOADPATH"),
				'savePath' => "ueditor/$date/",
				'maxSize' => 10485760,//10M
				'saveName'   =>    array('uniqid',''),
				'exts'       =>    array('jpg', 'gif', 'png', 'jpeg'),
				'autoSub'    =>    false,
		);
		
		if(is_array($config)){
			$config=array_merge($mconfig,$config);
		}else{
			$config=$mconfig;
		}
		$upload = new \Think\Upload($config);//
		
		$file = $title = $oriName = $state ='0';
		
		$info=$upload->upload();
		//开始上传
		if ($info) {
			//上传成功
			$title = $oriName = $_FILES['upfile']['name'];
			$size=$info['upfile']['size'];
		
			$state = 'SUCCESS';
			
			if(!empty($info['upfile']['url'])){
				$url=$info['upfile']['url'];
			}else{
				$url = C("TMPL_PARSE_STRING.__UPLOAD__")."ueditor/$date/".$info['upfile']['savename'];
			}
			if(strpos($url, "https")===0 || strpos($url, "http")===0){
		
			}else{//local
				$host=(is_ssl() ? 'https' : 'http')."://".$_SERVER['HTTP_HOST'];
				$url=$host.$url;
			}
		} else {
			$state = $upload->getError();
		}
		
		$response=array(
				"state" => $state,
				"url" => $url,
				"title" => $title,
				"original" =>$oriName,
		);
		
		return json_encode($response);
	}
}