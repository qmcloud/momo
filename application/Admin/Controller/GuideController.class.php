<?php

/**
 * 引导图
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class GuideController extends AdminbaseController {

    function set(){

        $config=M("options")->where("option_name='guide'")->getField("option_value");

		$this->assign('config',json_decode($config,true) );
    	
    	$this->display();
    }
    
    function set_post(){

        if(IS_POST){
			
			$config=I("post.post");

            if ( M("options")->where("option_name='guide'")->save(['option_value'=>json_encode($config)] )!==false) {
                /* $key='getConfigPub';
                setcaches($key,$config); */
                $this->success("保存成功！");
            } else {
                $this->error("保存失败！");
            }
		}
    }
    
    function index(){

        $config=M("options")->where("option_name='guide'")->getField("option_value");
        
        $config = json_decode($config,true);
        
        $type=$config['type'];
        
        $map['type']=$type;
        
    	$guide=M("guide");
    	$count=$guide->where($map)->count();
    	$page = $this->page($count, 20);
    	$lists = $guide
            ->where($map)
            ->order("orderno asc, id desc")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
            
    	$this->assign('lists', $lists);
    	$this->assign('type', $type);

    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display();
    }
		
    function del(){
        $id=intval($_GET['id']);
        if($id){
            $result=M("guide")->delete($id);				
                if($result){
                    $this->success('删除成功');
                 }else{
                    $this->error('删除失败');
                 }			
        }else{				
            $this->error('数据传入失败！');
        }								  
        $this->display();				
    }		
    //排序
    public function listorders() { 
		
        $ids = $_POST['listorders'];
        foreach ($ids as $key => $r) {
            $data['orderno'] = $r;
            M("guide")->where(array('id' => $key))->save($data);
        }
				
        $status = true;
        if ($status) {
            $this->success("排序更新成功！");
        } else {
            $this->error("排序更新失败！");
        }
    }	
    

    function add(){
        $config=M("options")->where("option_name='guide'")->getField("option_value");
        
        $config = json_decode($config,true);
        
        $type=$config['type'];
        if($type==1){
            $map['type']=$type;
        
            $guide=M("guide");
            $count=$guide->where($map)->count();
            if($count>1){
                $this->error("引导页视频只能存在一个");
            }
        }
        
        $this->assign('type', $type);
        
        $this->display();				
    }
    
    function add_post(){
        if(IS_POST){
             $guide=M("guide");
             $guide->create();

             $type=I('type');
             $href=I('href');
             $guide->href=html_entity_decode($href);
             if($type==1){
                 if($_FILES){
                    $savepath=date('Ymd').'/';
                    //上传处理类
                    $config=array(
                            'rootPath' => './'.C("UPLOADPATH"),
                            'savePath' => $savepath,
                            'maxSize' => 1024*1024*10,
                            'saveName'   =>    array('uniqid',''),
                            'exts'       =>    array('mp4'),
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
                        }
                        
                        $guide->thumb=$url;
                    } else {
                        //上传失败，返回错误 
                        $this->error($upload->getError());
                    }  
                     
                 }
             }
             $guide->addtime=time();
             $guide->uptime=time();
             
             $result=$guide->add(); 
             if($result){
                $this->success('添加成功');
             }else{
                $this->error('添加失败');
             }
        }			
    }		
    function edit(){
        $id=intval($_GET['id']);
        if($id){
            $data=M("guide")->find($id);
            $this->assign('data', $data);						
        }else{				
            $this->error('数据传入失败！');
        }								  
        $this->display();				
    }
    
    function edit_post(){
        if(IS_POST){			
             $guide=M("guide");
             $guide->create();
             
             $type=I('type');
             $href=I('href');
             $guide->href=html_entity_decode($href);
             if($type==1 ){
                 if($_FILES){
                    $savepath=date('Ymd').'/';
                    //上传处理类
                    $config=array(
                            'rootPath' => './'.C("UPLOADPATH"),
                            'savePath' => $savepath,
                            'maxSize' => 1024*1024*10,
                            'saveName'   =>    array('uniqid',''),
                            'exts'       =>    array('mp4'),
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
                        }
                        
                        $guide->thumb=$url;
                    } else {
                        //上传失败，返回错误 
                        $this->error($upload->getError());
                    }  
                     
                 }
             }
             $guide->uptime=time();
             $result=$guide->save(); 
             if($result!==false){
                  $this->success('修改成功');
             }else{
                  $this->error('修改失败');
             }
        }			
    }
        

}
