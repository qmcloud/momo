<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2012 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi.cn@gmail.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
// TypeEvent.class.php 2013-02-27
namespace Api\Event;

class TypeEvent{
	//登录成功，获取腾讯QQ用户信息
	public function qq($token){
		//import("ORG.ThinkSDK.ThinkOauth");
		$qq   = \ThinkOauth::getInstance('qq', $token);
		$data = $qq->call('user/get_user_info');

		if($data['ret'] == 0){
			$userInfo['type'] = 'QQ';
			$userInfo['name'] = $data['nickname'];
			$userInfo['nick'] = $data['nickname'];
			$userInfo['head'] = $data['figureurl_2'];
			return $userInfo;
		} else {
			throw_exception("获取腾讯QQ用户信息失败：{$data['msg']}");
		}
	}

	//登录成功，获取腾讯微博用户信息
	public function tencent($token){
		//import("ORG.ThinkSDK.ThinkOauth");
		$tencent = ThinkOauth::getInstance('tencent', $token);
		$data    = $tencent->call('user/info');

		if($data['ret'] == 0){
			$userInfo['type'] = 'TENCENT';
			$userInfo['name'] = $data['data']['name'];
			$userInfo['nick'] = $data['data']['nick'];
			$userInfo['head'] = $data['data']['head'];
			return $userInfo;
		} else {
			throw_exception("获取腾讯微博用户信息失败：{$data['msg']}");
		}
	}

	//登录成功，获取新浪微博用户信息
	public function sina($token){
		$sina = \ThinkOauth::getInstance('sina', $token);
		$data = $sina->call('users/show', "uid={$sina->openid()}");

		if($data['error_code'] == 0){
			$userInfo['type'] = 'SINA';
			$userInfo['name'] = $data['name'];
			$userInfo['nick'] = $data['screen_name'];
			$userInfo['head'] = $data['avatar_large'];
			return $userInfo;
		} else {
			throw_exception("获取新浪微博用户信息失败：{$data['error']}");
		}
	}

	//登录成功，获取网易微博用户信息
	public function t163($token){
		$t163 = ThinkOauth::getInstance('t163', $token);
		$data = $t163->call('users/show');

		if($data['error_code'] == 0){
			$userInfo['type'] = 'T163';
			$userInfo['name'] = $data['name'];
			$userInfo['nick'] = $data['screen_name'];
			$userInfo['head'] = str_replace('w=48&h=48', 'w=180&h=180', $data['profile_image_url']);
			return $userInfo;
		} else {
			throw_exception("获取网易微博用户信息失败：{$data['error']}");
		}
	}

	//登录成功，获取人人网用户信息
	public function renren($token){
		$renren = ThinkOauth::getInstance('renren', $token);
		$data   = $renren->call('users.getInfo');

		if(!isset($data['error_code'])){
			$userInfo['type'] = 'RENREN';
			$userInfo['name'] = $data[0]['name'];
			$userInfo['nick'] = $data[0]['name'];
			$userInfo['head'] = $data[0]['headurl'];
			return $userInfo;
		} else {
			throw_exception("获取人人网用户信息失败：{$data['error_msg']}");
		}
	}

	//登录成功，获取360用户信息
	public function x360($token){
		$x360 = ThinkOauth::getInstance('x360', $token);
		$data = $x360->call('user/me');

		if($data['error_code'] == 0){
			$userInfo['type'] = 'X360';
			$userInfo['name'] = $data['name'];
			$userInfo['nick'] = $data['name'];
			$userInfo['head'] = $data['avatar'];
			return $userInfo;
		} else {
			throw_exception("获取360用户信息失败：{$data['error']}");
		}
	}

	//登录成功，获取豆瓣用户信息
	public function douban($token){
		$douban = ThinkOauth::getInstance('douban', $token);
		$data   = $douban->call('user/~me');

		if(empty($data['code'])){
			$userInfo['type'] = 'DOUBAN';
			$userInfo['name'] = $data['name'];
			$userInfo['nick'] = $data['name'];
			$userInfo['head'] = $data['avatar'];
			return $userInfo;
		} else {
			throw_exception("获取豆瓣用户信息失败：{$data['msg']}");
		}
	}

	//登录成功，获取Github用户信息
	public function github($token){
		$github = ThinkOauth::getInstance('github', $token);
		$data   = $github->call('user');

		if(empty($data['code'])){
			$userInfo['type'] = 'GITHUB';
			$userInfo['name'] = $data['login'];
			$userInfo['nick'] = $data['name'];
			$userInfo['head'] = $data['avatar_url'];
			return $userInfo;
		} else {
			throw_exception("获取Github用户信息失败：{$data}");
		}
	}

	//登录成功，获取Google用户信息
	public function google($token){
		$google = ThinkOauth::getInstance('google', $token);
		$data   = $google->call('userinfo');

		if(!empty($data['id'])){
			$userInfo['type'] = 'GOOGLE';
			$userInfo['name'] = $data['name'];
			$userInfo['nick'] = $data['name'];
			$userInfo['head'] = $data['picture'];
			return $userInfo;
		} else {
			throw_exception("获取Google用户信息失败：{$data}");
		}
	}

	//登录成功，获取Google用户信息
	public function msn($token){
		$msn  = ThinkOauth::getInstance('msn', $token);
		$data = $msn->call('me');

		if(!empty($data['id'])){
			$userInfo['type'] = 'MSN';
			$userInfo['name'] = $data['name'];
			$userInfo['nick'] = $data['name'];
			$userInfo['head'] = '微软暂未提供头像URL，请通过 me/picture 接口下载';
			return $userInfo;
		} else {
			throw_exception("获取msn用户信息失败：{$data}");
		}
	}

	//登录成功，获取点点用户信息
	public function diandian($token){
		$diandian  = ThinkOauth::getInstance('diandian', $token);
		$data      = $diandian->call('user/info');

		if(!empty($data['meta']['status']) && $data['meta']['status'] == 200){
			$userInfo['type'] = 'DIANDIAN';
			$userInfo['name'] = $data['response']['name'];
			$userInfo['nick'] = $data['response']['name'];
			$userInfo['head'] = "https://api.diandian.com/v1/blog/{$data['response']['blogs'][0]['blogUuid']}/avatar/144";
			return $userInfo;
		} else {
			throw_exception("获取点点用户信息失败：{$data}");
		}
	}

	//登录成功，获取淘宝网用户信息
	public function taobao($token){
		$taobao = ThinkOauth::getInstance('taobao', $token);
		$fields = 'user_id,nick,sex,buyer_credit,avatar,has_shop,vip_info';
		$data   = $taobao->call('taobao.user.buyer.get', "fields={$fields}");
		
		if(!empty($data['user_buyer_get_response']['user'])){
			$user = $data['user_buyer_get_response']['user'];
			$userInfo['type'] = 'TAOBAO';
			$userInfo['name'] = $user['user_id'];
			$userInfo['nick'] = $user['nick'];
			$userInfo['head'] = $user['avatar'];
			return $userInfo;
		} else {
			throw_exception("获取淘宝网用户信息失败：{$data['error_response']['msg']}");
		}
	}
	
	//登录成功，获取百度用户信息
	public function baidu($token){
		$baidu = ThinkOauth::getInstance('baidu', $token);
		$data  = $baidu->call('passport/users/getLoggedInUser');
		
		if(!empty($data['uid'])){
			$userInfo['type'] = 'BAIDU';
			$userInfo['name'] = $data['uid'];
			$userInfo['nick'] = $data['uname'];
			$userInfo['head'] = "http://tb.himg.baidu.com/sys/portrait/item/{$data['portrait']}";
			return $userInfo;
		} else {
			throw_exception("获取百度用户信息失败：{$data['error_msg']}");
		}
	}

	//登录成功，获取开心网用户信息
	public function kaixin($token){
		$kaixin = ThinkOauth::getInstance('kaixin', $token);
		$data   = $kaixin->call('users/me');
		
		if(!empty($data['uid'])){
			$userInfo['type'] = 'KAIXIN';
			$userInfo['name'] = $data['uid'];
			$userInfo['nick'] = $data['name'];
			$userInfo['head'] = $data['logo50'];
			return $userInfo;
		} else {
			throw_exception("获取开心网用户信息失败：{$data['error']}");
		}
	}

	//登录成功，获取搜狐用户信息
	public function sohu($token){
		$sohu = ThinkOauth::getInstance('sohu', $token);
		$data = $sohu->call('i/prv/1/user/get-basic-info');
		
		if('success' == $data['message'] && !empty($data['data'])){
			$userInfo['type'] = 'SOHU';
			$userInfo['name'] = $data['data']['open_id'];
			$userInfo['nick'] = $data['data']['nick'];
			$userInfo['head'] = $data['data']['icon'];
			return $userInfo;
		} else {
			throw_exception("获取搜狐用户信息失败：{$data['message']}");
		}
	}

}