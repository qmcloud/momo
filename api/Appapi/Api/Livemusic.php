<?php
/**
 * 直播间音乐
 *
 * @author: dogstar <chanzonghuang@gmail.com> 2014-10-04
 */

class Api_Livemusic extends PhalApi_Api {

	public function getRules() {
        return array(
            'searchMusic' => array(
				'key' => array('name' => 'key', 'type' => 'string','require' => true,'desc' => '关键词'),
				'p' => array('name' => 'p', 'type' => 'int', 'min' => 1, 'default'=>1,'desc' => '页数'),
            ),
            'getDownurl' => array(
				'audio_id' => array('name' => 'audio_id', 'type' => 'string','require' => true,'desc' => '歌曲ID'),
            ),
        );
	}
	
	/**
     * 歌曲查询
     * @desc 用于直播间主播搜索歌曲
     * @return int code 操作码，0表示成功
     * @return array info 
     * @return string info[].audio_id 歌曲ID 
     * @return string info[].audio_name 歌曲名称 
     * @return string info[].artist_name 歌手 
     * @return string msg 提示信息
     */
	public function searchMusic(){
        
        $rs = array('code' => 0, 'msg' => '', 'info' =>array());
        
        $keyword=checkNull($this->key);
        
        require API_ROOT.'/public/Meting/Meting.php';
        
        $api = new Metowolf\Meting('kugou');

        // Get data
        $data = $api->format(true)->search($keyword, [
            'page' => 1,
            'limit' => 50
        ]);

        $req = json_decode($data,1);

        $list = array();
        foreach ( $req as $k => $v )
        {
            $song = array();
            //$song['api_type'] = 0;//http://tingapi.ting.baidu.com
            $song['audio_id'] = $v['id'];
            $song['audio_name'] = preg_replace('/\\\|\\/|:|\*|\?|"|<|>|\|/',' ',$v['name']);
            $song['artist_name'] = implode(',',preg_replace('/\\\|\\/|:|\*|\?|"|<|>|\|/',' ',$v['artist']));
            //$song['audio_link'] = '';
            //$song['lrc_link'] = '';
            //$song['time_len'] = 0;

            $list[] = $song;
        }
        $rs['info'] = $list;
        
        return $rs;
	}

	/**
     * 歌曲信息
     * @desc 用于获取歌曲详细信息
     * @return int code 操作码，0表示成功
     * @return array info 
     * @return string info[0].audio_link 歌曲下载链接 
     * @return string info[0].audio_ext 文件后缀 
     * @return string info[0].time_len 时长 
     * @return string info[0].audio_size 大小 
     * @return string info[0].lrc_title 歌词名 
     * @return string info[0].lrc_content 歌词 
     * @return string msg 提示信息
     */
	public function getDownurl() {
        $rs = array('code' => 0, 'msg' => '', 'info' =>array());
        
        $audio_id = checkNull($this->audio_id);


        require API_ROOT.'/public/Meting/Meting.php';
        
        $api = new Metowolf\Meting('kugou');

        $link=$api->format(true)->url($audio_id);

        $link=json_decode($link,1);

        if(!$link['url']){
            $rs['code'] = 1001;
            $rs['msg'] = '无法下载该音乐';
            return $rs;
        }
        $audio=array();
        
        $audio['audio_link'] = $link['url'];
        $audio['audio_ext'] = '';
        $audio['audio_size'] = '';
        $audio['time_len'] = '';

        
        
        //====================获得歌词===================
        $lrc_content=$api->format(true)->lyric($audio_id);
        $lrc_content=json_decode($lrc_content,1);
        
        $audio['lrc_title'] = '';
        $audio['lrc_content'] = $lrc_content['lyric'];

        $rs['info'][0]= $audio;
        
        return $rs;          
	}
} 
