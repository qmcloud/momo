<?php
/**
 * 音乐
 *
 * @author: dogstar <chanzonghuang@gmail.com> 2014-10-04
 */

class Api_Music extends PhalApi_Api {

	public function getRules() {
        return array(
            'music_list'=>array(
                'classify'=>array('name' => 'classify', 'type' => 'int','default'=>0,'desc' => '音乐分类ID，0表示全部'),
                'uid'=>array('name' => 'uid', 'type' => 'int','desc' => '用户id'),
                'p' => array('name' => 'p', 'type' => 'int', 'min' => 1, 'default'=>1,'desc' => '页数'), //返回Top10，此参数暂时不用
            ),
            'searchMusic' => array(
                'key' => array('name' => 'key', 'type' => 'string','require' => true,'desc' => '关键词'),
				'uid' => array('name' => 'uid', 'type' => 'int','desc' => '用户id'),
				'p' => array('name' => 'p', 'type' => 'int', 'min' => 1, 'default'=>1,'desc' => '页数'),
            ),
            'collectMusic'=>array(
                'uid'=>array('name'=>'uid','type' => 'int','require' => true,'desc' => '用户id'),
                'token'=>array('name'=>'token','type' => 'string','require' => true,'desc' => '用户token'),
                'musicid'=>array('name'=>'musicid','type' => 'int','require' => true,'desc' => '音乐id'),
            ),
            'getCollectMusicLists'=>array(
                'uid'=>array('name'=>'uid','type' => 'int','require' => true,'desc' => '用户id'),
                'p' => array('name' => 'p', 'type' => 'int', 'min' => 1, 'default'=>1,'desc' => '页数'),
            ),
            'hotLists'=>array(
                'uid'=>array('name'=>'uid','type' => 'int','require' => true,'desc' => '用户id'),
            ),
            
            
        );
	}
	
	/**
     * 背景音乐分类列表
     * @desc 用于获取背景音乐分类列表
     * @return int code 操作码，0表示成功
     * @return string msg 提示信息
     * @return array info
     * @return string info[0].title 分类名称
     * @return string info[0].addtime 分类添加时间
     * @return string info[0].img_url 分类图标地址
     */
    
    public function classify_list(){
         $rs = array('code' => 0, 'msg' => '', 'info' =>array());
         $domain=new Domain_Music();
         $res=$domain->classify_list();

         if($res==1001){
            $rs['code']=0;
            $rs['msg']="暂无分类列表";
            return $rs;
         }

         $rs['info']=$res;

         return $rs;
    }


    /**
     * 音乐列表
     * @desc 用户获取音乐列表
     * @return int code 操作码，0表示成功
     * @return string msg 提示信息
     * @return array info 
     * @return string info[0].title 音乐名称
     * @return string info[0].author 演唱者
     * @return string info[0].img_url 封面
     * @return string info[0].length 音乐长度
     * @return string info[0].file_url 音乐文件地址
     * @return int info[0].use_nums 音乐被使用次数
     * @return int info[0].iscollect 音乐是否被该用户收藏
     */
    public function music_list(){
        $rs = array('code' => 0, 'msg' => '', 'info' =>array());

        $classify=checkNull($this->classify);
        $uid=checkNull($this->uid);
        $p=checkNull($this->p);

        $domain=new Domain_Music();
        $res=$domain->music_list($classify,$uid,$p);

        if($res==1001){
            $rs['code']=0;
            $rs['msg']="无音乐列表";
            return $rs;
        }

        $rs['info']=$res;

        return $rs;

    }

    /**
     * 搜索音乐
     * @desc 用于搜索音乐
     * @return int code 状态码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     * @return string info[0].title 音乐名称
     * @return string info[0].author 演唱者
     * @return string info[0].img_url 封面
     * @return string info[0].length 音乐长度
     * @return string info[0].file_url 音乐文件地址
     * @return int info[0].use_nums 音乐被使用次数
     * @return int info[0].iscollect 音乐是否被该用户收藏
     */
    public function searchMusic(){

        $rs = array('code' => 0, 'msg' => '', 'info' =>array());
        $keywords=checkNull($this->key);
        $uid=checkNull($this->uid);
        $p=$this->p;

        $domain=new Domain_Music();
        $res=$domain->searchMusic($keywords,$uid,$p);

        if($res==1001){
            $rs['code']=0;
            $rs['msg']="无音乐列表";
            return $rs;
        }

        $rs['info']=$res;

        return $rs;
    }

    /**
     * 收藏音乐/取消收藏
     * @return int code 状态码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     */
    public function collectMusic(){
       $rs = array('code' => 0, 'msg' => '', 'info' =>array());
       $uid=checkNull($this->uid);
       $token=checkNull($this->token);
       $musicid=checkNull($this->musicid);

       if($checkToken==700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }else if($checkToken==10020){
            $rs['code'] = 10020;
            $rs['msg'] = '该账号已被禁用';
            return $rs;
        }



        $domain=new Domain_Music();
        $res=$domain->collectMusic($uid,$musicid);

        if($res==1001){
            $rs['code']=1001;
            $rs['msg']='该音乐已下架';
            return $rs;
        }

        if($res==200){
            $rs['msg']="取消收藏成功";
            $rs['info'][0]['iscollect']=0;
            return $rs;
        }

        if($res==201){
            $rs['code']=1002;
            $rs['msg']="取消收藏失败";
            return $rs;
        }

        if($res==300){
            $rs['msg']="收藏成功";
            $rs['info'][0]['iscollect']=1;
            return $rs;
        }

        if($res==301){
            $rs['code']=1002;
            $rs['msg']="收藏失败";
            return $rs;
        }


    }

    /**
     * 获取用户收藏背景音乐列表
     * @return int code 状态码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回数据
     */
    public function getCollectMusicLists(){

        $rs = array('code' => 0, 'msg' => '', 'info' =>array());
        $uid=checkNull($this->uid);
        $p=checkNull($this->p);

        $domain=new Domain_Music();
        $res=$domain->getCollectMusicLists($uid,$p);
        if($res==0){
            $rs['code']=0;
            $rs['msg']="暂无收藏背景音乐";
            return $rs;
        }

        $rs['info']=$res;

        return $rs;

    }
    /**
     * 获取热门音乐列表
     * @return int code 状态码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回数据
     * @return string info[0].title 音乐名称
     * @return string info[0].author 演唱者
     * @return string info[0].img_url 封面
     * @return string info[0].length 音乐长度
     * @return string info[0].file_url 音乐文件地址
     * @return int info[0].use_nums 音乐被使用次数
     * @return int info[0].iscollect 音乐是否被该用户收藏
     */
    public function hotLists(){

        $rs = array('code' => 0, 'msg' => '', 'info' =>array());

        $uid=checkNull($this->uid);

        $domain=new Domain_Music();
        $res=$domain->hotLists($uid);

        if($res==1001){
            $rs['code']=0;
            $rs['msg']="无音乐列表";
            return $rs;
        }

        $rs['info']=$res;

        return $rs;
    }

	
} 
