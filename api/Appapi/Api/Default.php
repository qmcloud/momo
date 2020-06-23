<?php
/**
 * 默认接口服务类
 *
 * @author: dogstar <chanzonghuang@gmail.com> 2014-10-04
 */

class Api_Default extends PhalApi_Api {

	public function getRules() {
        return array(
            'index2' => array(
                'username' 	=> array('name' => 'username', 'default' => 'PHPer', ),
            ),
        );
	}
	
	/**
	 * 默认接口服务
	 * @return string title 标题
	 * @return string content 内容
	 * @return string version 版本，格式：X.X.X
	 * @return int time 当前时间戳
	 */
	public function index() {
          
	}

	public function test() {
        // $stream='123';
        // $push=PrivateKeyA('rtmp',$stream,1);
        // $pull=PrivateKeyA('rtmp',$stream,0);
        // echo $push;
        // echo '<br>';
        // echo $pull;
        // exit;
        // $start='21800';
        // $end='21870';
        // $nowtime=1541742938;
        
        // for($i=$start;$i<=$end;$i++){
            // $nowtime--;
            // $stream=$i.'_'.$nowtime;
            
            // $data=[
                // "uid"=>$i,
				// "ishot"=>1,
				// "isrecommend"=>1,
                
				// "showid"=>$nowtime,
				// "starttime"=>$nowtime,
				// "title"=>'',
				// "province"=>'',
				// "city"=>'好像在火星',
				// "stream"=>$stream,
				// "thumb"=>'',
				// "pull"=>'http://falsevideo.yunbaozb.com/4.mp4',
				// "lng"=>'',
				// "lat"=>'',
				// "type"=>'0',
				// "type_val"=>'0',
				// "isvideo"=>1,
				// "islive"=>1,
				// "anyway"=>'0',
				// "liveclassid"=>'1',
            // ];
            
            // DI()->notorm->users_live->insert($data);
            
        // }
	}
    
} 
