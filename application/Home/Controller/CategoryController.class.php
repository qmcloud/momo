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
 * 分类页
 */
class CategoryController extends HomebaseController {
	
    //分类页
	public function index() {

		/* 右侧广告 */
		$ads=M("ads")->where("sid='1'")->order("orderno asc")->limit(7)->select();
		$this->assign('ads',$ads);
		/* 主播列表 */
		$cat=(int)I("cat");
		$prefix= C("DB_PREFIX");	
			switch($cat){
				case "1":
						$where="u.sex='1' and l.islive='1' ";
						$site_seo_title='国民男神';
						$current=1;	
						break;
				case "2":
						$where="u.sex='2' and l.islive='1' ";
						$site_seo_title='女神驾到';
						$current=2;
						break;
				default :
						$where="l.islive='1'";
						$site_seo_title='';
						break;			
				
				
			}
			
			$this->assign("current",$current);	
			
			$pagesize = 20;
	    
			$User = M('users'); // 实例化User对象
			$Live = M('users_live'); // 
			$count = M("users_live l")
					->field("u.user_nicename,u.avatar,l.uid,l.stream,l.thumb,l.title,l.city,l.islive")
					->join("left join {$prefix}users u on u.id=l.uid")
					->where($where)
					->count();// 查询满足要求的总记录数
			
			$Page       = new \Page2($count,$pagesize);// 实例化分页类 传入总记录数和每页显示的记录数(25)
			$show       = $Page->show();// 分页显示输出

			// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
			$lists=M("users_live l")
					->field("u.user_nicename,u.avatar,l.thumb,l.uid,l.stream,l.title,l.city,l.islive")
					->join("left join {$prefix}users u on u.id=l.uid")
					->where($where)
					->order("l.showid desc")
					->limit($Page->firstRow.','.$Page->listRows)
					->select();
			foreach($lists as $k=>$vo){
				if($vo['thumb']=="")
				{
					$lists[$k]['thumb']=$vo['avatar'];
				}
			} 
			$this->assign('lists',$lists);// 赋值数据集
			$this->assign('page',$show);// 赋值分页输出		
			$this->assign('site_seo_title',$site_seo_title);	
     	$this->display();
    }	


}


