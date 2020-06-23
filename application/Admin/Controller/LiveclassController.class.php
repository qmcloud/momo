<?php

/**
 * 直播分类
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class LiveclassController extends AdminbaseController {
    function index(){
			
    	$gift_model=M("live_class");
    	$count=$gift_model->count();
    	$page = $this->page($count, 20);
    	$lists = $gift_model
            //->where()
            ->order("orderno asc, id desc")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
    	$this->assign('lists', $lists);
    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display();
    }
		
    function del(){
        $id=intval($_GET['id']);
        if($id){
            $result=M("live_class")->delete($id);				
            if($result){
                $action="删除直播分类：{$id}";
                setAdminLog($action);
                $this->resetCache();
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
            M("live_class")->where(array('id' => $key))->save($data);
        }
				
        $status = true;
        if ($status) {
            $action="更新直播分类排序";
            setAdminLog($action);
            $this->resetCache();
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
            $name=I("name");
            if($name==''){
                $this->error('请填写名称');
            }
            $thumb=I("thumb");
            if($thumb==''){
                $this->error('请上传图标');
            }
            $Live_class=M("live_class");
            $Live_class->create();
            $result=$Live_class->add(); 
            if($result){
                $action="添加直播分类：{$result}";
                setAdminLog($action);
                $this->resetCache();
                $this->success('添加成功');
            }else{
                $this->error('添加失败');
            }
        }			
    }		
    function edit(){
        $id=intval($_GET['id']);
        if($id){
            $data=M("live_class")->where(["id"=>$id])->find();
            $this->assign('data', $data);						
        }else{				
            $this->error('数据传入失败！');
        }								  
        $this->display();				
    }
    
    function edit_post(){
        if(IS_POST){		
            $name=I("name");
            if($name==''){
                $this->error('请填写名称');
            }
            $thumb=I("thumb");
            if($thumb==''){
                $this->error('请上传图标');
            }
            $Live_class=M("live_class");
            $Live_class->create();
            $result=$Live_class->save(); 
            if($result!==false){
                $action="修改直播分类：{$_POST['id']}";
                setAdminLog($action);
                $this->resetCache();
                $this->success('修改成功');
            }else{
                $this->error('修改失败');
            }
        }			
    }
    
    function resetCache(){
        $key='getLiveClass';
        $rules= M("live_class")
            ->order('orderno asc,id desc')
            ->select();
        foreach($rules as $k=>$v){
            $v['thumb']=get_upload_path($v['thumb']);
            
            $rules[$k]=$v;
        }
        setcaches($key,$rules);
        return 1;
    }
}
