<?php

class Model_Music extends PhalApi_Model_NotORM {
	

	/*获取分类列表*/
	public function classify_list(){
		$list=DI()->notorm->users_music_classify->select("id,title,img_url")->where("isdel=0")->order("orderno")->fetchAll();
		if(!$list){
			return 1001;
		}

		foreach ($list as $k => $v) {
			$list[$k]['img_url']=get_upload_path($v['img_url']);
		}

		return $list;
	}

	/*获取音乐列表*/
	public function music_list($classify,$uid,$p){
        if($p<1){
            $p=1;
        }
		$nums=20;
		$start=($p-1)*$nums;

		$where=" isdel=0";

		if($classify>0){
			$where.=" and classify_id={$classify}";
		}



		$list=DI()->notorm->users_music->select("id,title,author,img_url,length,file_url,use_nums")->where($where)->order("use_nums desc")->limit($start,$nums)->fetchAll();

		if(!$list){
			return 1001;
		}

		
		if($uid<1){ //游客

			foreach ($list as $k => $v) {
				$list[$k]['iscollect']=0;
				$list[$k]['img_url']=get_upload_path($v['img_url']);
				$list[$k]['file_url']=get_upload_path($v['file_url']);
			}
		}else{

			
			//获取本人收藏列表
			$collectLists=DI()->notorm->users_music_collection->select("music_id")->where("uid=? and status=1",$uid)->fetchAll();
			$collects=array();
			foreach ($collectLists as $val) {
				$collects[]=$val['music_id'];
			}


			foreach ($list as $k => $v) {
				if(in_array($v['id'],$collects)){
					$list[$k]['iscollect']="1";
				}else{
					$list[$k]['iscollect']="0";
				}

				$list[$k]['img_url']=get_upload_path($v['img_url']);
				$list[$k]['file_url']=get_upload_path($v['file_url']);
			}

		}

		
		return $list;

	}

	/*搜索音乐(模糊查询本地，按照使用量排序)*/
	public function searchMusic($keywords,$uid,$p){

		if($p<1){
            $p=1;
        }

		$nums=50;
		$start=($p-1)*$nums;

		$where="1=1";
		if($keywords!=""){
			$where .=" and (title like '%".$keywords."%') or author like '%".$keywords."%'";
		}

		$list=DI()->notorm->users_music->select("id,title,author,img_url,length,file_url,use_nums")->where($where)->order("use_nums desc")->limit($start,$nums)->fetchAll();

		if(!$list){
			return 1001;
		}

		if($uid<1){ //游客

			foreach ($list as $k => $v) {
				$list[$k]['iscollect']=0;
				$list[$k]['img_url']=get_upload_path($v['img_url']);
				$list[$k]['file_url']=get_upload_path($v['file_url']);
			}
		}else{

			//获取本人收藏列表
			$collectLists=DI()->notorm->users_music_collection->select("music_id")->where("uid=? and status=1",$uid)->fetchAll();
			$collects=array();

			foreach ($collectLists as $val) {
				$collects[]=$val['music_id'];
			}

			foreach ($list as $k => $v) {
				if(in_array($v['id'],$collects)){
					$list[$k]['iscollect']="1";

				}else{
					$list[$k]['iscollect']="0";
				}

				$list[$k]['img_url']=get_upload_path($v['img_url']);
				$list[$k]['file_url']=get_upload_path($v['file_url']);
			}

		}

		return $list;
	}

	/*收藏/取消收藏音乐*/
	public function collectMusic($uid,$musicid){

		//判断音乐是否存在
		$info=DI()->notorm->users_music->select("title,addtime")->where("id=?",$musicid)->fetchOne();


		if(!$info){
			return 1001;
		}

		//判断用户是否收藏过该视频
		$isexist=DI()->notorm->users_music_collection->select("*")->where("uid='{$uid}' and music_id='{$musicid}'")->fetchOne();


		//已经收藏过
		if($isexist){

			if($isexist['status']==1){ //已收藏
				//将状态改为取消收藏
				$result=DI()->notorm->users_music_collection->where("uid=? and music_id=?",$uid,$musicid)->update(array("status"=>0,"updatetime"=>time()));
				if($result!==false){
					return 200;
				}else{
					return 201;
				}
			}else{ //改为收藏

				//将状态改为收藏
				$result=DI()->notorm->users_music_collection->where("uid=? and music_id=?",$uid,$musicid)->update(array("status"=>1,"updatetime"=>time()));
				if($result!==false){
					return 300;
				}else{
					return 301;
				}
			}
			
		}else{

			//向收藏表中写入记录
			$data=array("uid"=>$uid,"music_id"=>$musicid,'addtime'=>time(),'status'=>1);
			$result=DI()->notorm->users_music_collection->insert($data);
			if($result!==false){
				return 300;
			}else{
				return 301;
			}
		}

	}

	/*获取用户收藏背景音乐列表*/
	public function getCollectMusicLists($uid,$p){
        if($p<1){
            $p=1;
        }
		$nums=20;
		$start=($p-1)*$nums;

		$where="uid='{$uid}' and status=1";

		$list=DI()->notorm->users_music_collection->select("music_id,addtime")->where($where)->order("addtime desc")->limit($start,$nums)->fetchAll();


		if(!$list){
			return 0;
		}

		foreach ($list as $k => $v) {
			$musicinfo=DI()->notorm->users_music->select("title,author,img_url,length,file_url,use_nums")->where("id=?",$v['music_id'])->fetchOne();
			unset($list[$k]['addtime']);
			$list[$k]['collecttime']=datetime($v['addtime']); //收藏时间
			$list[$k]['title']=$musicinfo['title'];
			$list[$k]['author']=$musicinfo['author'];
			$list[$k]['img_url']=get_upload_path($musicinfo['img_url']);
			$list[$k]['length']=$musicinfo['length'];
			$list[$k]['file_url']=get_upload_path($musicinfo['file_url']);
			$list[$k]['use_nums']=$musicinfo['use_nums'];
			$list[$k]['iscollect']='1';
			$list[$k]['id']=$v['music_id'];  //为了app端模板解析统一，所以重新添加了一个id字段


		}

		return $list;
	}


	/*获取热门音乐列表*/
	public function hotLists($uid){

		$start=0;
		$nums=10;
		$where=" isdel=0";

		$list=DI()->notorm->users_music->select("id,title,author,img_url,length,file_url,use_nums")->where($where)->order("use_nums desc")->limit($start,$nums)->fetchAll();

		if(!$list){
			return 1001;
		}

		
		if($uid<1){ //游客

			foreach ($list as $k => $v) {
				$list[$k]['iscollect']=0;
				$list[$k]['img_url']=get_upload_path($v['img_url']);
				$list[$k]['file_url']=get_upload_path($v['file_url']);
			}
		}else{

			
			//获取本人收藏列表
			$collectLists=DI()->notorm->users_music_collection->select("music_id")->where("uid=? and status=1",$uid)->fetchAll();
			$collects=array();
			foreach ($collectLists as $val) {
				$collects[]=$val['music_id'];
			}


			foreach ($list as $k => $v) {
				if(in_array($v['id'],$collects)){
					$list[$k]['iscollect']=1;
				}else{
					$list[$k]['iscollect']=0;
				}

				$list[$k]['img_url']=get_upload_path($v['img_url']);
				$list[$k]['file_url']=get_upload_path($v['file_url']);
			}

		}

		
		return $list;

	}
	
}
