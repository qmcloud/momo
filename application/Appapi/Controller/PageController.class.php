<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace Appapi\Controller;
use Common\Controller\HomebaseController;
class PageController extends HomebaseController{

	public function lists() {

		$page=M("page")->field("id,title")->where("type='1'")->order("listorder asc")->select();

		$this->assign("page",$page);
		 
		$this->display();
	}	
	
	public function pagecon() {
		$id=(int)$_GET['id'];
		$page=M("page")->where(["id"=>$id])->find();

		$this->assign('page',$page);

		
		$this->display();
	}
	
	public function newslist() {
		
		$team=M("news_class")->field("id,title")->order("listorder asc")->select();
		
		foreach($team as $k=>$v){
			
			 $list=M("news")
					->field("id,title")
					->where(" classid='{$v['id']}'")
					->order("listorder asc")
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
		
		 $name=M("news_class")->where(["id"=>$term_id])->getField("title");
			
			$list=M("news")
					->field("id,title")
					->where(["classid"=>$term_id])
					->order("listorder asc")
					->select();
		
		$this->assign("name",$name);
		$this->assign("list",$list);
		 
		$this->display();
	}		

	
	public function news() {
     $id=(int)$_GET['id'];
		$news=M("news")->field("title,body")->where(["id"=>$id])->find();

		$this->assign("news",$news);
		 
		$this->display();
	}		
}