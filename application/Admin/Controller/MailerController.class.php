<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Tuolaji <479923197@qq.com>
// +----------------------------------------------------------------------
/**
 * 邮箱配置
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class MailerController extends AdminbaseController {

	//SMTP配置
    public function index() {
    	$this->display();
    }
    
    //SMTP配置处理
    public function index_post() {
    	$_POST = array_map('trim', I('post.'));
    	if(in_array('', $_POST)) $this->error("不能留空！");
    	$configs['SP_MAIL_ADDRESS'] = $_POST['address'];
    	$configs['SP_MAIL_SENDER'] = $_POST['sender'];
    	$configs['SP_MAIL_SMTP'] = $_POST['smtp'];
    	$configs['SP_MAIL_SMTP_PORT'] = $_POST['smtp_port'];
    	$configs['SP_MAIL_LOGINNAME'] = $_POST['loginname'];
    	$configs['SP_MAIL_PASSWORD'] = $_POST['password'];
    	$rst=sp_set_dynamic_config($configs);
    	sp_clear_cache();
    	if ($rst) {
    		$this->success("保存成功！");
    	} else {
    		$this->error("保存失败！");
    	}
    }
    
    //member账号激活
    public function active(){
    	$where = array('option_name'=>'member_email_active');
    	$option = M('Options')->where($where)->find();
    	if($option){
    		$options = json_decode($option['option_value'], true);
    		$this->assign('options', $options);
    	}
    	$this->display();
    }
    
    public function active_post(){
    	$configs['SP_MEMBER_EMAIL_ACTIVE'] = intval($_POST['lightup']);
    	sp_set_dynamic_config($configs);

    	$data['option_name'] = "member_email_active";
    	$stripChar = '?<*>\'\"';
    	$_POST['options']['title'] = preg_replace('/['.$stripChar.']/s','',$_POST['options']['title']);
    	$data['option_value']= json_encode($_POST['options']);
    	$posts_model= M('Options');
    	if($posts_model->where("option_name='member_email_active'")->find()){
    		$rst2 = $posts_model->where("option_name='member_email_active'")->save($data);
    	}else{
    		$rst2 = $posts_model->add($data);
    	}
    	
    	if ($rst2!==false) {
    		$this->success("保存成功！");
    	} else {
    		$this->error("保存失败！");
    	}
    }
}

