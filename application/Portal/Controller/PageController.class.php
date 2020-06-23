<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace Portal\Controller;
use Common\Controller\HomebaseController;
class PageController extends HomebaseController{
	public function index() {
		$id=$_GET['id'];
		$content=sp_sql_page($id);
		
		if(empty($content)){
		    header('HTTP/1.1 404 Not Found');
		    header('Status:404 Not Found');
		    if(sp_template_file_exists(MODULE_NAME."/404")){
		        $this->display(":404");
		    }
		     
		    return ;
		}
		
		$this->assign($content);
		$smeta=json_decode($content['smeta'],true);
		$tplname=isset($smeta['template'])?$smeta['template']:"";
		
		$tplname=sp_get_apphome_tpl($tplname, "page");
		
		$this->display(":$tplname");
	}
	
	public function nav_index(){
		$navcatname="页面";
		$datas=sp_sql_pages("field:id,post_title;");
		$navrule=array(
				"action"=>"Page/index",
				"param"=>array(
						"id"=>"id"
				),
				"label"=>"post_title");
		exit( sp_get_nav4admin($navcatname,$datas,$navrule) );
	}
	
	public function lists() {

		$page=M("posts")->field("id,post_title")->where("type='2'")->order("orderno asc")->select();

		$this->assign("page",$page);
		 
		$this->display();
	}	
	
	public function newslist() {
		
		$team=M("terms")->field("term_id,name")->order("listorder asc")->select();
		
		foreach($team as $k=>$v){
			
			 $list=M("term_relationships")
						->alias("a")
						->field("b.id,b.post_title")
						->join(C ( 'DB_PREFIX' )."posts b ON a.object_id = b.id")
						->where("b.recommended='1' and a.term_id='{$v['term_id']}'")
						->order("b.orderno asc")
						->select();
			$team[$k]['list']=$list;
			
		}
		
		$this->assign("team",$team);
		
		$uid=I('uid');
		$version=I('version');
		$model=I('model');
		$this->assign("uid",$uid);
		$this->assign("version",$version);
		$this->assign("model",$model);
		 
		$this->display();
	}		
	
	public function newslists() {
		
		  $term_id=(int)$_GET['id'];
		
		 $name=M("terms")->where(["term_id"=>$term_id])->getField("name");
			
			 $list=M("term_relationships")
						->alias("a")
						->field("b.id,b.post_title")
						->join(C ( 'DB_PREFIX' )."posts b ON a.object_id = b.id")
						->where(["a.term_id"=>$term_id])
						->order("b.orderno asc")
						->select();
						
		$this->assign("name",$name);
		$this->assign("list",$list);
		 
		$this->display();
	}		

	
	public function news() {
     $id=(int)$_GET['id'];
		$news=M("posts")->field("post_title,post_content")->where(["id"=>$id])->find();

		$this->assign("news",$news);
		 
		$this->display();
	}
}