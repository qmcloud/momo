<?php
namespace Asset\Controller;
use Think\Controller;
class DownloadController extends Controller {
	//文件下载
    function index(){
    	header("Content-type:text/html;charset=utf-8");
    	$unique_id = trim($_GET['key']); //获取唯一码
    	$asset = M('Asset');
    	$line = $asset->where(array('unique'=>$unique_id))->find();
    	//print_r($line); die;
    	$rel_name = $line['filename'];
    	
    	if(!$rel_name){
    		$this->error('未知错误！');
    	}
    	$file = $line['filepath'].$line['filename'];
    	//用以解决中文不能显示出来的问题
    	$file=iconv("utf-8","gb2312",$file);
    	//首先要判断给定的文件存在与否
    	if(!file_exists($file)){
    		$this->error("没有该文件文件");
    	}
    	$fp=fopen($file,"r");
    	$file_size=filesize($file);
    	//下载文件需要用到的头
    	Header("Content-type: application/octet-stream");
    	Header("Accept-Ranges: bytes");
    	Header("Accept-Length:".$file_size);
    	Header("Content-Disposition: attachment; filename=".$rel_name);
    	$buffer=1024;
    	$file_count=0;
    	//向浏览器返回数据
    	while(!feof($fp) && $file_count<$file_size){
    		$file_con=fread($fp,$buffer);
    		$file_count+=$buffer;
    		echo $file_con;
    	}
    	//写入数据库
    	$asset->where(["_unique"=>$unique_id])->setInc('download_times',1);
    	fclose($fp);
    }
}
