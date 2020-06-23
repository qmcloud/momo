<?php

/**
 * 印象标签
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class ImpressionController extends AdminbaseController {
    function index(){
			
    	$gift_model=M("impression_label");
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
            $result=M("impression_label")->delete($id);				
            if($result){
                $action="删除印象标签：{$id}";
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
            M("impression_label")->where(array('id' => $key))->save($data);
        }
				
        $status = true;
        if ($status) {
            $action="更新印象标签排序";
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
            
            $colour=I("colour");
            if($colour==''){
                $this->error('请选择首色');
            }
            
            $colour2=I("colour2");
            if($colour2==''){
                $this->error('请选择尾色');
            }
            
            
            $Live_class=M("impression_label");
            $Live_class->create();
            $result=$Live_class->add(); 
            if($result){
                $action="添加印象标签：{$result}";
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
            $data=M("impression_label")->where(["id"=>$id])->find();
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
            
            $colour=I("colour");
            if($colour==''){
                $this->error('请选择首色');
            }
            
            $colour2=I("colour2");
            if($colour2==''){
                $this->error('请选择尾色');
            }
            $Live_class=M("impression_label");
            $Live_class->create();
            $result=$Live_class->save(); 
            if($result!==false){
                $action="修改印象标签：{$_POST['id']}";
                setAdminLog($action);
                $this->resetCache();
                $this->success('修改成功');
            }else{
                $this->error('修改失败');
            }
        }			
    }
    
    function resetCache(){
        $key='getImpressionLabel';
        $rules= M("impression_label")
            ->order('orderno asc,id desc')
            ->select();
        foreach($rules as $k=>$v){
            $rules[$k]['colour']='#'.$v['colour'];
            $rules[$k]['colour2']='#'.$v['colour2'];
        }
        setcaches($key,$rules);
        return 1;
    }
}
