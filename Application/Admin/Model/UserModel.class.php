<?php
// +----------------------------------------------------------------------
// | QQ群274904994 [ 简单 高效 卓越 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 51zhibo.top All rights reserved.
// +----------------------------------------------------------------------
// | Author: 51zhibo.top
// +----------------------------------------------------------------------
namespace Admin\Model;

use Common\Model\ModelModel;

/**
 * 用户模型
 * @author 51zhibo.top
 */
class UserModel extends ModelModel
{
    /**
     * 数据库表名
     * @author 51zhibo.top
     */
    protected $tableName = 'admin_user';

    /**
     * 自动验证规则
     * @author 51zhibo.top
     */
    protected $_validate = array(
        //验证用户名
        array('nickname', 'require', '昵称不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),

        //验证用户名
        array('username', 'require', '用户名不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('username', '3,32', '用户名长度为1-32个字符', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
        array('username', '', '用户名被占用', self::MUST_VALIDATE, 'unique', self::MODEL_BOTH),
        array('username', '/^(?!_)(?!\d)(?!.*?_$)[\w]+$/', '用户名只可含有数字、字母、下划线且不以下划线开头结尾，不以数字开头！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),

        //验证密码
        array('password', 'require', '密码不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('password', '6,30', '密码长度为6-30位', self::MUST_VALIDATE, 'length', self::MODEL_INSERT),
        array('password', '/(?!^(\d+|[a-zA-Z]+|[~!@#$%^&*()_+{}:"<>?\-=[\];\',.\/]+)$)^[\w~!@#$%^&*()_+{}:"<>?\-=[\];\',.\/]+$/', '密码至少由数字、字符、特殊字符三种中的两种组成', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('repassword', 'password', '两次输入的密码不一致', self::EXISTS_VALIDATE, 'confirm', self::MODEL_INSERT),

        //验证密码
        array('password', 'require', '密码不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_UPDATE),
        array('password', '6,30', '密码长度为6-30位', self::EXISTS_VALIDATE, 'length', self::MODEL_UPDATE),
        array('password', '/(?!^(\d+|[a-zA-Z]+|[~!@#$%^&*()_+{}:"<>?\-=[\];\',.\/]+)$)^[\w~!@#$%^&*()_+{}:"<>?\-=[\];\',.\/]+$/', '密码至少由数字、字符、特殊字符三种中的两种组成', self::EXISTS_VALIDATE, 'regex', self::MODEL_UPDATE),
        array('repassword', 'password', '两次输入的密码不一致', self::EXISTS_VALIDATE, 'confirm', self::MODEL_UPDATE),

        //验证邮箱
        array('email', 'email', '邮箱格式不正确', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('email', '1,32', '邮箱长度为1-32个字符', self::EXISTS_VALIDATE, 'length', self::MODEL_BOTH),
        array('email', '', '邮箱被占用', self::EXISTS_VALIDATE, 'unique', self::MODEL_BOTH),

        //验证手机号码
        array('mobile', '/^1\d{10}$/', '手机号码格式不正确', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('mobile', '', '手机号被占用', self::EXISTS_VALIDATE, 'unique', self::MODEL_BOTH),

        // 验证注册来源
        array('reg_type', 'require', '注册来源不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
    );

    /**
     * 自动完成规则
     * @author 51zhibo.top
     */
    protected $_auto = array(
        array('score', '0', self::MODEL_INSERT),
        array('money', '0', self::MODEL_INSERT),
        array('reg_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1),
        array('password', 'user_md5', self::MODEL_BOTH, 'function'),
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_BOTH, 'function'),
        array('status', '1', self::MODEL_INSERT),
    );

    /**
     * 查找后置操作
     * @author 51zhibo.top
     */
    protected function _after_find(&$result, $options)
    {
        $result['avatar_url'] = get_cover($result['avatar'], 'avatar');

        $result['label'] = $result['nickname'] . '(' . $result['id'];
        if ($result['email']) {
            $result['label'] = $result['label'] . '-' . $result['email'];
        }
        $result['label'] = $result['label'] . ')';
    }

    /**
     * 查找后置操作
     * @author 51zhibo.top
     */
    protected function _after_select(&$result, $options)
    {
        foreach ($result as &$record) {
            $this->_after_find($record, $options);
        }
    }

    /**
     * 根据用户ID获取用户信息
     * @param  integer $id 用户ID
     * @param  string $field
     * @return array  用户信息
     * @author 51zhibo.top
     */
    public function getUserInfo($id = null, $field = null)
    {
        if (!$id) {
            return false;
        }
        if (D('Admin/Module')->where('name="User" and status="1"')->count()) {
            $user_info = D('User/User')->detail($id);
        } else {
            $user_info = $this->find($id);
        }
        unset($user_info['password']);
        if (!$field) {
            return $user_info;
        }
        if ($user_info[$field]) {
            return $user_info[$field];
        } else {
            return false;
        }
    }

    /**
     * 用户登录
     * @author 51zhibo.top
     */
    public function login($username, $password, $map = null)
    {
        //去除前后空格
        $username = trim($username);

        //匹配登录方式
        if (preg_match("/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/", $username)) {
            $map['email'] = array('eq', $username); // 邮箱登陆
        } elseif (preg_match("/^1\d{10}$/", $username)) {
            $map['mobile'] = array('eq', $username); // 手机号登陆
        } else {
            $map['username'] = array('eq', $username); // 用户名登陆
        }

        $map['status'] = array('eq', 1);
        $user_info     = $this->where($map)->find(); //查找用户
        if (!$user_info) {
            $this->error = '用户不存在或被禁用！';
        } else {
            if (user_md5($password) !== $user_info['password']) {
                $this->error = '密码错误！';
            } else {
                return $user_info;
            }
        }
        return false;
    }

    /**
     * 设置登录状态
     * @author 51zhibo.top
     */
    public function auto_login($user)
    {
        // 记录登录SESSION和COOKIES
        $auth = array(
            'uid'      => $user['id'],
            'username' => $user['username'],
            'nickname' => $user['nickname'],
            'avatar'   => $user['avatar'],
        );
        session('user_auth', $auth);
        session('user_auth_sign', $this->data_auth_sign($auth));
        return $this->is_login();
    }

    /**
     * 数据签名认证
     * @param  array  $data 被认证的数据
     * @return string       签名
     * @author 51zhibo.top
     */
    public function data_auth_sign($data)
    {
        // 数据类型检测
        if (!is_array($data)) {
            $data = (array) $data;
        }
        ksort($data); //排序
        $code = http_build_query($data); // url编码并生成query字符串
        $sign = sha1($code); // 生成签名
        return $sign;
    }

    /**
     * 检测用户是否登录
     * @return integer 0-未登录，大于0-当前登录用户ID
     * @author 51zhibo.top
     */
    public function is_login()
    {
        $user = session('user_auth');
        if (empty($user)) {
            return 0;
        } else {
            if (session('user_auth_sign') == $this->data_auth_sign($user)) {
                return $user['uid'];
            } else {
                return 0;
            }
        }
    }
    
    public function check_register_user($user){
    
    	$where=array("user"=>$user);
    	$User = M("user"); // 实例化User对象
    	$ras=$User->where($where)->find();
    	//echo $User->getLastSql();die;
    	return $ras;
    	
    	
    }
    
    public function add_register_user($user,$pass,$nik){
    	$User = M("user"); // 实例化User对象
		$data=array("user"=>$user,"pass"=>md5($pass),"nik"=>$nik);
    	$ras=$User->data($data)->add();
		if($ras){
			return $data;
		}
		return false;
    	
    }
    
    /**
     * 生成登录shell
     * @param  int    $id       shell的id
     * @param  string $password shell的密码
     * @return string
     */
    private function genShell($id, $password) {
    	return md5($password . C('AUTH_TOKEN')) . $id;
    }
    
    public function login_momo($data){
    	
    	$User = M("user"); // 实例化User对象
    	$ras=$User->where($data)->find();
    	//echo $User->getLastSql();die;
    	if($ras['id']){
    		$loginMarked = "AUTH";
    		 
    		$shell = $this->genShell($ras['id'], $ras['pass']);
    		 
    		// 生成登录session
    		$_SESSION[$loginMarked] = $shell;
    		 
    		// 生成登录cookie
    		$shell .= '_' . time();
    		setcookie($loginMarked, $shell, 0, '/');
    		$_SESSION['current_account'] = $ras;
    		 
    		$_SESSION[C('USER_AUTH_KEY')] = $ras['id'];
    		return $ras;
    	}else{
    		return false;
    	}
    	
    	
    }
    
    /**
     * 销毁登录标记
     * @return
     */
    private function unsetLoginMarked() {
    	$loginMarked = "AUTH";
    	setcookie("{$loginMarked}", null, -3600, '/');
    	unset($_SESSION[$loginMarked], $_COOKIE[$loginMarked]);
    
    	return ;
    }
    
    public function logout_momo(){
    	
    	$this->unsetLoginMarked();
    	
    	if (C('USER_AUTH_ON')) {
    		unset($_SESSION[C('USER_AUTH_KEY')]);
    		unset($_SESSION[C('ADMIN_AUTH_KEY')]);
    	}
    	
    	session_destroy();
    	
    }
}
