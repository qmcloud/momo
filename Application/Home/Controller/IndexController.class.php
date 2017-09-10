<?php
// +----------------------------------------------------------------------
// | QQ群274904994 [ 不要用于商业用途 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 51zhibo.top All rights reserved.
// +----------------------------------------------------------------------
// | Author: 51zhibo.top
// +----------------------------------------------------------------------
namespace Home\Controller;

/**
 * 前台默认控制器
 * @author 51zhibo.top
 */
class IndexController extends HomeController
{
    /**
     * 默认方法
     * @author 51zhibo.top
     */
    public function index()
    {
        $this->display();
    }
    
/* 注册模块 */
    public function regist(){
		
  		$user=$_POST['user'];
  		$pass=$_POST['pass'];
  		$nik=$_POST['nik'];
  		if($user=="" || $pass=="" || $nik==""){
  			$data=array("code"=>-1,'msg'=>"登录失败");
  			echo json_encode($data);die;
  		}else{
  			
  			/* 反查注册信息 */
  			$chk_regist=D("Admin/User");
  			$res_chk=$chk_regist->check_register_user($user);
  			if($res_chk==null) {
  				$res_add=$chk_regist->add_register_user($user,$pass,$nik);
  				if($res_add!=false){
  					$res_array=array("code"=>1,'msg'=>"注册成功");
  					echo json_encode($res_array);die;
  				}
  			}else{
  				$data=array("code"=>-1,"msg"=>"用户名已经存在！");
  				echo json_encode($data);die;
  			} 
  			
  			
  		}
		
    }
    
    /* 登录 */
    public function login(){
    	$user=$_POST['user'];
    	$pass=$_POST['pass'];
    	
    	if($user=="" || $pass==""){
    		$data=array("code"=>-1,"msg"=>"账号密码补不能为空");
    		echo json_encode($data);die;
    	}else{
    		$data=array("user"=>$user,"pass"=>md5($pass));
    		$roles = D("Admin/User")->login_momo($data);
    		if($roles==false) {
    			$data=array("code"=>-1,"msg"=>"error");
    			echo json_encode($data);die;
    		}else{
    			$data=array("code"=>1,"data"=>$roles);
    			echo json_encode($data);die;
    		}
    			
    			
    	}
    	
    }
    
    public function logout(){
    	$roles = D("Admin/User")->logout_momo();
    	echo json_encode(array("code"=>1));
    }
   
}
