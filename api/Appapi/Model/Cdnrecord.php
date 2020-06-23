<?php

class Model_Cdnrecord extends PhalApi_Model_NotORM {
	/* 直播回放 */
	public function getCdnRecord($id) {
		
		$result=DI()->notorm->users_liverecord->select('id,starttime,endtime,stream,video_url')->where('id=?',$id)->fetchOne();
        
        if($result && !$result['video_url']){
            $configpri=getConfigPri();
            $cdn_switch=$configpri['cdn_switch'];
            $url='';
            switch($cdn_switch){
                case '1':
                    $url=$this->alicdn($result);
                    break;
                case '2':
                    //$url=PrivateKey_tx($host,$stream,$type);
                    break;
                case '3':
                    //$url=PrivateKey_qn($host,$stream,$type);
                    break;
                case '4':
                    //$url=PrivateKey_ws($host,$stream,$type);
                    break;
                case '5':
                    //$url=PrivateKey_wy($host,$stream,$type);
                    break;
                case '6':
                    //$url=PrivateKey_ady($host,$stream,$type);
                    break;
            }
            $result['video_url']=$url;
        }
		
		return $result;
	}

    /* 阿里云CDN */
    protected function alicdn($data){
        date_default_timezone_set("UTC");
        $video_url='';
        
        $configpri=getConfigPri();
        //阿里的配置信息
        $access_key_id=$configpri['aliy_key_id'];
        $access_key_secret=$configpri['aliy_key_secret'];
        //录播的流地址和应用名配置信息    
        $DomainName = $configpri['pull_url'];
        $AppName = '5showcam';
    
        if(empty($access_key_id) || empty($access_key_secret) || empty($DomainName) || empty($AppName)){
            return $video_url;
        }
        //file_put_contents('./alicdn.txt',date('Y-m-d H:i:s').' 提交参数信息 :'.$reqParam."\r\n",FILE_APPEND);
        $live_starttime = $data['starttime']-200;
        $live_endtime   = $data['endtime'] + 200;
        $StartTime=gmdate("Y-m-d\TH:i:s\Z",$live_starttime);
        $EndTime=gmdate("Y-m-d\TH:i:s\Z",$live_endtime);

        $StreamName=$data['stream'];
        $action = 'DescribeLiveStreamRecordIndexFiles';

       
        $specialParameter = array(
            'AccessKeyId'    => $access_key_id,
            'Action'         => $action,
            'DomainName'     => $DomainName,
            'AppName'        => $AppName,
            'StreamName'     => $StreamName,
            'StartTime'      => $StartTime,
            'EndTime'        => $EndTime,
        );
        //file_put_contents('./alicdn.txt',date('Y-m-d H:i:s').' 提交参数信息 specialParameter:'.json_encode($specialParameter)."\r\n",FILE_APPEND);
        $parameter = $this->setParameter($specialParameter);
        $url = $this->getStringToSign($parameter,$access_key_secret);
        $ret = $this->curl_get($url);

        $res_arr = json_decode($ret,true);
        //file_put_contents('./alicdn.txt',date('Y-m-d H:i:s').' 提交参数信息 res_arr:'.json_encode($res_arr)."\r\n",FILE_APPEND);
        if(!$res_arr['RecordIndexInfoList']['RecordIndexInfo']){
            return $video_url;
        }

        $video_url = $res_arr['RecordIndexInfoList']['RecordIndexInfo'][0]['RecordUrl'];
       
        DI()->notorm->users_liverecord->where('id=?',$data['id'])->update( array('video_url'=>$video_url) );

        return $video_url;
    }
    protected function curl_get($url){
       $ch = curl_init();
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    // https请求 不验证证书和hosts
       curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
       curl_setopt($ch, CURLOPT_URL, $url);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    // 要求结果为字符串且输出到屏幕上
       curl_setopt($ch, CURLOPT_HEADER, 0); // 不要http header 加快效率
       curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
       curl_setopt($ch, CURLOPT_TIMEOUT, 15);
       $output = curl_exec($ch);
       curl_close($ch);
       return $output;
	}
    protected function setParameter($specialParameter){
        $Timestamp = gmdate("Y-m-d\TH:i:s\Z",time()); 
        $signature_nonce = '';
        for($i =0 ; $i < 14; $i++){
            $signature_nonce .= mt_rand(0,9);
        }
        $publicParameter = array(
            'Format'         => 'JSON',
            'Version'        => '2016-11-01',
            'SignatureMethod'   => 'HMAC-SHA1',
            'Timestamp'         => $Timestamp,
            'SignatureVersion'  => '1.0',
            'SignatureNonce'    => $signature_nonce,
        );

        $parameter = array_merge($publicParameter, $specialParameter);
        return $parameter;
    }

    protected function getStringToSign($parameter,$access_key_secret){
        ksort($parameter); 
        foreach($parameter as $key => $value){
            $str[] = rawurlencode($key). "=" .rawurlencode($value);
        }
        $ss = "";
        if(!empty($str)){
            for($i=0; $i<count($str); $i++){
                if(!isset($str[$i+1])){
                    $ss .= $str[$i];
                }else{
                    $ss .= $str[$i]."&";
                }
            }
        }

        $StringToSign = "GET" . "&" . rawurlencode("/") . "&" . rawurlencode($ss);
        $signature = base64_encode(hash_hmac("sha1", $StringToSign, $access_key_secret."&", true));
       
        $url = "https://live.aliyuncs.com/?" . $ss . "&Signature=" . rawurlencode($signature);
        return $url;
    }
    /* 阿里云CDN */

}
