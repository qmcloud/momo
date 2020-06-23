<?php

class Model_Guide extends PhalApi_Model_NotORM {
	/* 引导页 */
	public function getGuide() {
		
        $config=DI()->notorm->options
            ->select('option_value')
            ->where("option_name='guide'")
            ->fetchOne();
            
        $config = json_decode($config['option_value'],true);
        
        $where="type={$config['type']}";
        
        $list=DI()->notorm->guide
            ->select('thumb,href')
            ->where($where)
            ->order('orderno asc,uptime desc')
            ->fetchAll();
        foreach($list as $k=>$v){
            $v['thumb']=get_upload_path($v['thumb']);
            $v['href']=urldecode($v['href']);
            $list[$k]=$v;
        }

        $config['list']=$list;
		return $config;
	}			

}
