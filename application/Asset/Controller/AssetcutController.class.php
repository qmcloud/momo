<?php

/**
 * 附件上传
 */
namespace Asset\Controller;
use Common\Controller\AdminbaseController;
class AssetcutController extends AdminbaseController {


    function _initialize() {

    }

    /**
     * swfupload 上传 
     */
    public function swfupload() {
        if (IS_POST) {
			$savepath=date('Ymd').'/';
            //上传处理类
            $config=array(
            		'rootPath' => './'.C("UPLOADPATH"),
            		'savePath' => $savepath,
            		'maxSize' => 11048576,
            		'saveName'   =>    array('uniqid',''),
            		'exts'       =>    array('jpg', 'gif', 'png', 'jpeg',"txt",'zip','swf'),
            		'autoSub'    =>    false,
            );
						$upload = new \Think\Upload($config);// 
						$info=$upload->upload();
            //开始上传
            if ($info) {
                //上传成功
                //写入附件数据库信息
                $first=array_shift($info);
                if(!empty($first['url'])){
                	$url=$first['url'];
                }else{
                	$url=C("TMPL_PARSE_STRING.__UPLOAD__").$savepath.$first['savename'];
									
					require_once("./simplewind/Lib/Extend/Image/Lite.php");
					require_once("./simplewind/Lib/Extend/Image/Driver/Gd.php");
					require_once("./simplewind/Lib/Extend/Image/Driver/GIF.php");
					require_once("./simplewind/Lib/Extend/Image/Driver/Imagick.php");
					
					$files='.'.$url;
					//初始化
					$PhalApi_Image = new \Image_Lite();
					//打开图片
					$PhalApi_Image->open($files);

					$PhalApi_Image->thumb(660, 660, IMAGE_THUMB_SCALING);
					$PhalApi_Image->save($files);
					
					$newfiles=str_replace(".png","_thumb.png",$files);
					$newfiles=str_replace(".jpg","_thumb.jpg",$newfiles);
					$newfiles=str_replace(".gif","_thumb.gif",$newfiles); 
					
					$PhalApi_Image->thumb(200, 200, IMAGE_THUMB_SCALING);
					$PhalApi_Image->save($newfiles);

                }
                
				echo "1," . $url.",".'1,'.$first['name'];
				exit;
            } else {
                //上传失败，返回错误
                exit("0," . $upload->getError());
            }
        } else {
            $this->display(':swfuploadcut');
        }
    }

}
