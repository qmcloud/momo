<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class MainController extends AdminbaseController {
	
    public function index(){
        
        $config=getConfigPub();
        $this->assign("config",$config);
        $User=M("users");
        
        $nowtime=time();
        //当天0点
        $today=date("Y-m-d",$nowtime);
        $today_start=strtotime($today);
        //当天 23:59:59
        $today_end=strtotime("{$today} + 1 day");
        
        /* 总注册数 */
        $users_total=$User->where("user_type=2")->count();
        $this->assign("users_total",number_format($users_total));
        
        /* 基础数据 */
        $congifpri=getConfigPri();

        if($congifpri['um_appkey_android']){
            $appkey=$congifpri['um_appkey_android'];
            $basic_today_android=$this->getDailyData($appkey,$today);
        }
        
        if($congifpri['um_appkey_ios']){
            $appkey=$congifpri['um_appkey_ios'];
            $basic_today_ios=$this->getDailyData($appkey,$today);
        }

        $basic_today['newUsers']=number_format($basic_today_android['newUsers']+$basic_today_ios['newUsers']);
        $basic_today['totalUsers']=number_format($basic_today_android['totalUsers']+$basic_today_ios['totalUsers']);
        $basic_today['activityUsers']=number_format($basic_today_android['activityUsers']+$basic_today_ios['activityUsers']);
        $basic_today['launches']=number_format($basic_today_android['launches']+$basic_today_ios['launches']);
        

        $data_basic=$this->getBasic($today_start,$today_end,1);
        $this->assign("basic_today",$basic_today);
        $this->assign("data_basicj",json_encode($data_basic));
        
    	//设备终端
        $source=$User
                ->field("count(id) as nums,source")
                ->where("user_type=2")
                ->group("source")
                ->select();
        $data_source=[
            'name'=>[],
            'nums'=>[],
            'nums_per'=>[],
            'color'=>[],
        ];
        $color=['#99ce87','#5ba1f8','#f4a76d'];
        if($source){
            $nums=array_column($source,'nums');
            $nums_totoal=array_sum($nums);
            
            foreach($source as $k=>$v){
                $data_source['name'][]=$v['source'];
                $data_source['nums'][]=$v['nums'];
                $data_source['color'][]=$color[$k];
                $data_source['nums_per'][]=round($v['nums']*100/$nums_totoal);
            }
        }
        
        $this->assign("data_sourcej",json_encode($data_source));
        /* 注册渠道 */
        $login_type=$User
                ->field("count(id) as nums,login_type")
                ->where("user_type=2")
                ->group("login_type")
                ->order("nums desc")
                ->select();
        $data_type=[
            'name'=>[],
            'v_n'=>[],
        ];
        $color_v_n=['#0972f4','#3289f6','#65a6f7','#8dbdf9','#b7d1f2'];
        if($login_type){

            foreach($login_type as $k=>$v){
                $data_type['v_n'][]=['value'=>$v['nums'],'name'=>$v['login_type'],'itemStyle'=>['color'=>$color_v_n[$k]]];
                $data_type['name'][]=$v['login_type'];
            }
        }
        
        $this->assign("data_typej",json_encode($data_type));
        
        
        /* 主播数据 */
        $Liverecord = M("users_liverecord");
        $anchor_total=$User->where("user_type=2")->count();
        $anchor_online=M("users_live")->where("islive=1")->count();

        
        $anchor_live_long_total=$Liverecord->sum("endtime-starttime");
        if(!$anchor_live_long_total){
            $anchor_live_long_total=0;
        }
        if($anchor_live_long_total>0){
            $anchor_live_long_total=number_format(floor($anchor_live_long_total/60));
        }
        
        $anchorinfo=$this->getAnchorInfo($today_start,$today_end);
        $anchor=[
            'anchor_total'=>$anchor_total,
            'anchor_online'=>$anchor_online,
            'anchor_live_long_total'=>$anchor_live_long_total,
        ];
        
        $anchor=array_merge($anchor,$anchorinfo);
        $this->assign("anchor",$anchor);
        
        
        /* 网红榜 */
        $votes_list=$User
                    ->field("id,user_nicename,avatar,avatar_thumb,votestotal")
                    ->where("user_type=2")
                    ->order("votestotal desc")
                    ->limit(0,3)
                    ->select();
        foreach($votes_list as $k=>$v){
            $v['avatar']=get_upload_path($v['avatar']);
            $v['avatar_thumb']=get_upload_path($v['avatar_thumb']);
            
            $votes_list[$k]=$v;
        }
        $this->assign("votes_list",$votes_list);
        /* 富豪榜 */
        $rich_list=$User
                    ->field("id,user_nicename,avatar,avatar_thumb,consumption")
                    ->where("user_type=2")
                    ->order("consumption desc")
                    ->limit(0,3)
                    ->select();
        foreach($rich_list as $k=>$v){
            $v['avatar']=get_upload_path($v['avatar']);
            $v['avatar_thumb']=get_upload_path($v['avatar_thumb']);
            
            $rich_list[$k]=$v;
        }
        $this->assign("rich_list",$rich_list);
        
        /* 财务 */
        $Charge=M('users_charge');
        $charge_total=$Charge->where("status=1")->sum("money");
        if(!$charge_total){
            $charge_total=0;
        }
        if($charge_total>0){
            $charge_total=number_format($charge_total);
        }
        
        $data_charge=$this->getCharge($today_start,$today_end);

        $this->assign("charge_total",$charge_total);
        $this->assign("data_chargej",json_encode($data_charge));
        
        /* 提现 */
        $Cash=M('users_cashrecord');
        
        $cashinfo=$this->getCash($today_start,$today_end);
        
        $cash_total=$Cash->where("status=1")->sum("money");
        if(!$cash_total){
            $cash_total=0;
        }
        if($cash_total>0){
            $cash_total=number_format($cash_total);
        }
        
        $this->assign("cashinfo",$cashinfo);
        $this->assign("cash_total",$cash_total);
        
        $this->display();
    }
    
    function getdata(){
        $rs=['code'=>0,'msg'=>'','info'=>[]];
        $action=I('action');
        $type=I('type');
        $start_time=I('start_time');
        $end_time=I('end_time');
        $basic_type=I('basic_type');
        $start=0;
        $end=time();
        if($type!=0){
            $nowtime=time();
            //当天0点
            $today=date("Ymd",$nowtime);
            $today_start=strtotime($today);
            //当天 23:59:59
            $today_end=strtotime("{$today} + 1 day");
            switch($type){
                case '1';
                    /* 今日 */
                    $start=$today_start;
                    $end=$today_end;
                    break;
                case '2';
                    /* 昨日 */
                    $yesterday_start=$today_start - 60*60*24;
                    $yesterday_end=$today_start;
                    
                    $start=$yesterday_start;
                    $end=$yesterday_end;
                    break;
                case '3';
                    /* 近7日 */
                    $week_start=$today_end - 60*60*24*7;
                    $week_end=$today_end;
                    
                    $start=$week_start;
                    $end=$week_end;
                    break;
                case '4';
                    /* 近30日 */
                    $month_start=$today_end - 60*60*24*30;
                    $month_end=$today_end;
                    
                    $start=$month_start;
                    $end=$month_end;
                    break;
            }
            
        }else{
            if($start_time){
                $start=strtotime($start_time);
            }
            if($end_time){
              $end=strtotime($end_time) + 60*60*24;  
            }
        }

        if(!$start){
            $rs['code']='1001';
            $rs['msg']='请选择开始时间';
            echo json_encode($rs);
            exit;
        }

        switch($action){
            case '1':
                $info=$this->getBasic($start,$end,$basic_type);
                break;
            case '2':
                $info=$this->getUsers($start,$end);
                break;
            case '3':
                $info=$this->getAnchorInfo($start,$end);
                break;
            case '4':
                $info=$this->getCharge($start,$end);
                break;
            case '5':
                $info=$this->getCash($start,$end);
                break;
        }
        
        $rs['info']=$info;
        echo json_encode($rs);
        exit;
    }
    
    /* 基础数据 */
    protected function getBasic($starttime,$endtime,$basic_type){
        $rs=[
            'name'=>[],
            'data'=>[],
            'nums'=>0,

        ];
        
        $start=date("Y-m-d",$starttime);
        $end=date("Y-m-d",($endtime - 60*60*24));
        $congifpri=getConfigPri();
        if($basic_type!= 3 && $start == $end){
            $periodType='hourly';
            $rs['name']=['00:00','01:00','02:00','03:00','04:00','05:00','06:00','07:00','08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00','21:00','22:00','23:00',];
        }else{
            $periodType='daily';
            for($i=$starttime;$i<$endtime;$i+=60*60*24){
                $rs['name'][]=date("Y-m-d",$i);
            }
            
        }
        
        if($congifpri['um_appkey_android']){
            $appkey=$congifpri['um_appkey_android'];
            switch($basic_type){
                case '1':
                    $newusers_android=$this->getNewUsers($appkey,$start,$end,$periodType);
                    break;
                case '2':
                    $launches_android=$this->getLaunches($appkey,$start,$end,$periodType);
                    break;
                case '3':
                    $durations_android=$this->getDurations($appkey,$start,$end,$periodType);
                    break;
                case '4':
                    $activeusers_android=$this->getActiveUsers($appkey,$start,$end,$periodType);
                    break;
                case '5':
                    $retentions_android=$this->getRetentions($appkey,$start,$end,$periodType);
                    break;
            }
        }
        
        if($congifpri['um_appkey_ios']){
            $appkey=$congifpri['um_appkey_ios'];
            switch($basic_type){
                case '1':
                    $newusers_ios=$this->getNewUsers($appkey,$start,$end,$periodType);
                    break;
                case '2':
                    $launches_ios=$this->getLaunches($appkey,$start,$end,$periodType);
                    break;
                case '3':
                    $durations_ios=$this->getDurations($appkey,$start,$end,$periodType);
                    break;
                case '4':
                    $activeusers_ios=$this->getActiveUsers($appkey,$start,$end,$periodType);
                    break;
                case '5':
                    $retentions_ios=$this->getRetentions($appkey,$start,$end,$periodType);
                    break;
            }
        }
        
        switch($basic_type){
            case '1':
                if($periodType=='hourly'){
                    /* hourValue */
                    foreach($rs['name'] as $k=>$v){
                        $rs['data'][]=$newusers_android[0]['hourValue'][$k] + $newusers_ios[0]['hourValue'][$k];
                    }
                }else{
                    /* value */
                    foreach($rs['name'] as $k=>$v){
                        $rs['data'][]=$newusers_android[$k]['value'] + $newusers_ios[$k]['value'];
                    }
                }

                break;
            case '2':
                if($periodType=='hourly'){
                    /* hourValue */
                    foreach($rs['name'] as $k=>$v){
                        $rs['data'][]=$launches_android[0]['hourValue'][$k] + $launches_ios[0]['hourValue'][$k];
                    }
                }else{
                    /* value */
                    foreach($rs['name'] as $k=>$v){
                        $rs['data'][]=$launches_android[$k]['value'] + $launches_ios[$k]['value'];
                    }
                }
                break;
            case '3':
                    
                    foreach($rs['name'] as $k=>$v){
                        $rs['data'][]= floor( ($durations_android[$k] + $durations_ios[$k])/60);
                    }
 
                break;
            case '4':
                if($periodType=='hourly'){
                    /* hourValue */
                    foreach($rs['name'] as $k=>$v){
                        $rs['data'][]=$activeusers_android[0]['hourValue'][$k] + $activeusers_ios[0]['hourValue'][$k];
                    }
                }else{
                    /* value */
                    foreach($rs['name'] as $k=>$v){
                        $rs['data'][]=$activeusers_android[$k]['value'] + $activeusers_ios[$k]['value'];
                    }
                }
                break;
            case '5':
                if($periodType=='hourly'){
                    /* hourValue */
                    foreach($rs['name'] as $k=>$v){
                        $rs['data'][]=$retentions_android[0]['hourValue'][$k] + $retentions_ios[0]['hourValue'][$k];
                    }
                }else{
                    /* value */
                    foreach($rs['name'] as $k=>$v){
                        $rs['data'][]=$retentions_android[$k]['value'] + $retentions_ios[$k]['value'];
                    }
                }
                break;
        }
        if($basic_type==3){
            $rs['nums']=floor( array_sum($rs['data'])/( ($endtime-$starttime)/(60*60*24) ) );
        }else{
            $rs['nums']=array_sum($rs['data']);
        }
        

        return $rs;
        
    }
    /* 获取某天总数 */
    protected function getDailyData($appkey,$start){
            $data=[
                'appkey'=>$appkey,
                'date'=>$start,
            ];
            
            $urlPath='param2/1/com.umeng.uapp/umeng.uapp.getDailyData/';
            
            $rs=$this->getUmengData($urlPath,$data);
            
            return $rs['dailyData'];
    }
    /* 获取App新增用户数 */
    protected function getNewUsers($appkey,$start,$end,$periodType){
            $data=[
                'appkey'=>$appkey,
                'startDate'=>$start,
                'endDate'=>$end,
                'periodType'=>$periodType,
            ];
            
            $urlPath='param2/1/com.umeng.uapp/umeng.uapp.getNewUsers/';
            
            $rs=$this->getUmengData($urlPath,$data);
            return $rs['newUserInfo'];

    }
    /* 获取App启动次数 */
    protected function getLaunches($appkey,$start,$end,$periodType){

            $data=[
                'appkey'=>$appkey,
                'startDate'=>$start,
                'endDate'=>$end,
                'periodType'=>$periodType,
            ];
            
            $urlPath='param2/1/com.umeng.uapp/umeng.uapp.getLaunches/';
            
            $rs=$this->getUmengData($urlPath,$data);
            
            return $rs['launchInfo'];
    }
    /* 获取App使用时长 */
    protected function getDurations($appkey,$start,$end,$periodType){
            
            $urlPath='param2/1/com.umeng.uapp/umeng.uapp.getDurations/';
            $info=[];
            
            $start_time=strtotime($start);
            $end_time=strtotime($end);
            for($i=$start_time;$i<=$end_time;$i+=60*60*24){
                $date=date("Y-m-d",$i);
                $data=[
                    'appkey'=>$appkey,
                    'date'=>$date,
                    'statType'=>'daily',
                ];

                $rs=$this->getUmengData($urlPath,$data);
                
                $info[]=$rs['average'];
            }
            return $info;
    }
    /* 活跃用户数 */
    protected function getActiveUsers($appkey,$start,$end,$periodType){

            $data=[
                'appkey'=>$appkey,
                'startDate'=>$start,
                'endDate'=>$end,
                'periodType'=>$periodType,
            ];
            
            $urlPath='param2/1/com.umeng.uapp/umeng.uapp.getActiveUsers/';
            
            $rs=$this->getUmengData($urlPath,$data);
            
            return $rs['activeUserInfo'];
    }
    /* 留存用户数 */
    protected function getRetentions($appkey,$start,$end,$periodType){

            $data=[
                'appkey'=>$appkey,
                'startDate'=>$start,
                'endDate'=>$end,
                'periodType'=>$periodType,
            ];
            
            $urlPath='param2/1/com.umeng.uapp/umeng.uapp.getRetentions/';
            
            $rs=$this->getUmengData($urlPath,$data);
            
            return $rs['retentionInfo'];
    }
    protected function getUmengData($urlPath,$data){
        $congifpri=getConfigPri();
        
        $url='https://gateway.open.umeng.com/openapi/';
        
        $appkey=$congifpri['um_apikey'];
        $apiSecurity=$congifpri['um_apisecurity'];
        
        $urlPath.=$appkey;
        
        ksort($data);
        $param='';
        foreach($data as $k=>$v){
            $param.=$k.$v;
        }
        $s=$urlPath.$param;
        $Signature=strtoupper ( bin2hex ( hash_hmac("sha1", $s, $apiSecurity, true) )  );
        
        $url.=$urlPath;
        
        $query=http_build_query($data);
        
        $query.='&_aop_signature='.$Signature;

        $rs=$this->Post($query,$url);

        return json_decode($rs,true);
        
    }
    protected function Post($curlPost,$url){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_NOBODY, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 检查证书中是否设置域名
		$return_str = curl_exec($curl);
		curl_close($curl);
		return $return_str;
    }
    /* 用户画像 */
    protected function getUsers($start,$end){
        
    }
    
    /* 主播数据 */
    protected function getAnchorInfo($start,$end){
        $Liverecord = M("users_liverecord");

        $anchor_today=$Liverecord->where("starttime >= {$start} and starttime<{$end}")->group("uid")->count();
        if(!$anchor_today){
            $anchor_today=0;
        }
        $anchor_live_today=$Liverecord->where("starttime >= {$start} and starttime<{$end}")->count();
        $anchor_live_long_today=$Liverecord->where("starttime >= {$start} and starttime<{$end}")->sum("endtime-starttime");
        if(!$anchor_live_long_today){
            $anchor_live_long_today=0;
        }
        if($anchor_live_long_today>0){
            $anchor_live_long_today=number_format(floor($anchor_live_long_today/60));
        }
        $info=[
            'anchor_today'=>$anchor_today,
            'anchor_live_today'=>$anchor_live_today,
            'anchor_live_long_today'=>$anchor_live_long_today,
        ];
        return $info;
    }
    
    /* 财务 */
    protected function getCharge($start,$end){
        $Charge=M('users_charge');
        $data_charge=[
            'name'=>[],
            'money'=>[],
            'color'=>[],
        ];
        $charge_type=[
            '1'=>'支付宝',
            '2'=>'微信',
            '3'=>'苹果支付',
        ];
        $charge_ambient=[
            '1'=>[
                '0'=>'APP',
                '1'=>'PC',
            ],
            '2'=>[
                '0'=>'APP',
                '1'=>'公众号',
                '2'=>'PC',
            ],
            '3'=>[
                '0'=>'沙盒',
                '1'=>'生产',
            ],
        ];
        $charge_color=['#f44957','#5bc189','#33c5f1','#f8c299'];

        $charge_total_today=$Charge->where("status=1 and addtime>={$start} and addtime<{$end}")->sum("money");
        if(!$charge_total_today){
            $charge_total_today=0;
        }
        
        $data_charge['color']=$charge_color;
        $data_charge['name'][]='充值总额';
        $data_charge['money'][]=$charge_total_today;

        foreach($charge_type as $k=>$v){
            $data_charge['name'][]=$v;
            $money=$Charge->where("status=1 and type={$k} and addtime>={$start} and addtime<{$end}")->sum("money");
            if(!$money){
                $money=0;
            }
            
            $data_charge['money'][]=$money;
            
        }
        
        return $data_charge;  
    }
    
    /* 提现 */
    protected function getCash($start,$end){
        $Cash=M('users_cashrecord');
        $cash_apply=$Cash->where("status=0 and addtime>={$start} and addtime<{$end}")->sum("money");
        if(!$cash_apply){
            $cash_apply=0;
        }
        if($cash_apply>0){
            $cash_apply=number_format($cash_apply);
        }
        
        $cash_adopt=$Cash->where("status=1 and addtime>={$start} and addtime<{$end}")->sum("money");
        if(!$cash_adopt){
            $cash_adopt=0;
        }
        if($cash_adopt>0){
            $cash_adopt=number_format($cash_adopt);
        }
        
        $cash_anchor=$Cash->where("addtime>={$start} and addtime<{$end}")->group("uid")->count();
        if(!$cash_anchor){
            $cash_anchor=0;
        }
        if($cash_anchor>0){
            $cash_anchor=number_format($cash_anchor);
        }
        

        $info=[
            'cash_apply'=>$cash_apply,
            'cash_adopt'=>$cash_adopt,
            'cash_anchor'=>$cash_anchor,
        ];

        return $info;
        
    }
    
    /* 导出 */
    function export(){

        $action=I('action');
        $type=I('type');
        $start_time=I('start_time');
        $end_time=I('end_time');
        $basic_type=I('basic_type');
        $start=0;
        $end=time();
        if($type!=0){
            $nowtime=time();
            //当天0点
            $today=date("Ymd",$nowtime);
            $today_start=strtotime($today);
            //当天 23:59:59
            $today_end=strtotime("{$today} + 1 day");
            switch($type){
                case '1';
                    /* 今日 */
                    $start=$today_start;
                    $end=$today_end;
                    break;
                case '2';
                    /* 昨日 */
                    $yesterday_start=$today_start - 60*60*24;
                    $yesterday_end=$today_start;
                    
                    $start=$yesterday_start;
                    $end=$yesterday_end;
                    break;
                case '3';
                    /* 近7日 */
                    $week_start=$today_end - 60*60*24*7;
                    $week_end=$today_end;
                    
                    $start=$week_start;
                    $end=$week_end;
                    break;
                case '4';
                    /* 近30日 */
                    $month_start=$today_end - 60*60*24*30;
                    $month_end=$today_end;
                    
                    $start=$month_start;
                    $end=$month_end;
                    break;
            }
            
        }else{
            if($start_time){
                $start=strtotime($start_time);
            }
            if($end_time){
                $end=strtotime($end_time) + 60*60*24;  
            }
        }
        
        if($start==0){
            exit;
        }
        

        $xlsData=[];
        switch($action){
            case '1':
                $starttime=$start;
                $endtime=$end;
                $start=date("Y-m-d",$starttime);
                $end=date("Y-m-d",($endtime - 60*60*24));
                $congifpri=getConfigPri();

                if($start == $end){
                    $periodType='hourly';
                    $title='  基本指标    '.$start;
                    
                    $date=['00:00','01:00','02:00','03:00','04:00','05:00','06:00','07:00','08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00','21:00','22:00','23:00',];
                }else{
                    $periodType='daily';
                    $title='  基本指标    '.$start.'至'.$end;
                }
                $xlsName  = "  基本指标导出";
                $cellName = array('A','B','C','D','E');
                
                $xlsCell  = array('date','newUsers','launches','duration','activityUsers');
                
                $xlsData[]=[
                    'date'=>$title,
                    'newUsers'=>'',
                    'launches'=>'',
                    'duration'=>'',
                    'activityUsers'=>'',
                    'ismerge'=>'1',
                ];

                
                $xlsData[]=[
                    'date'=>'时间',
                    'newUsers'=>'新注册用户（位）',
                    'launches'=>'APP启动次数（次）',
                    'duration'=>'平均使用时长（ 分钟）',
                    'activityUsers'=>'活跃用户数（位）',
                    'ismerge'=>'0',
                ];
                
                
                if($congifpri['um_appkey_android']){
                    $appkey=$congifpri['um_appkey_android'];
                    $newusers_android=$this->getNewUsers($appkey,$start,$end,$periodType);
                    $launches_android=$this->getLaunches($appkey,$start,$end,$periodType);
                    if($periodType!='hourly'){
                        $durations_android=$this->getDurations($appkey,$start,$end,$periodType);  
                    }
                    
                    $activeusers_android=$this->getActiveUsers($appkey,$start,$end,$periodType);
                }
                
                if($congifpri['um_appkey_ios']){
                    $appkey=$congifpri['um_appkey_ios'];
                    $newusers_ios=$this->getNewUsers($appkey,$start,$end,$periodType);
                    $launches_ios=$this->getLaunches($appkey,$start,$end,$periodType);
                    if($periodType!='hourly'){
                        $durations_ios=$this->getDurations($appkey,$start,$end,$periodType);
                    }
                    $activeusers_ios=$this->getActiveUsers($appkey,$start,$end,$periodType);
                }
                
                
                if($periodType!='hourly'){
                    for($i=$starttime,$n=0;$i<$endtime;$i+=60*60*24,$n++){
                        $info=[];
                        $info['date']=date("Y-m-d",$i);
                        $info['newUsers']=$newusers_android[$n]['value'] + $newusers_ios[$n]['value'];
                        $info['launches']=$launches_android[$n]['value'] + $launches_ios[$n]['value'];
                        $info['duration']=floor( ($durations_android[$n] + $durations_ios[$n])/60);
                        $info['activityUsers']=$activeusers_android[$n]['value'] + $activeusers_ios[$n]['value'];
                        $info['ismerge']='0';
                        $xlsData[]=$info;
                    }
                        
                }else{
                    for($n=0;$n<24;$n++){
                        $info=[];
                        $info['date']=$date[$n];
                        $info['newUsers']=$newusers_android[0]['hourValue'][$n] + $newusers_ios[0]['hourValue'][$n];
                        $info['launches']=$launches_android[0]['hourValue'][$n] + $launches_ios[0]['hourValue'][$n];
                        $info['duration']=0;
                        $info['activityUsers']=$activeusers_android[0]['hourValue'][$n] + $activeusers_ios[0]['hourValue'][$n];
                        $info['ismerge']='0';
                        $xlsData[]=$info;
                    } 
                }
                break;
            case '2':
                //$info=$this->getUsers($start,$end);
                break;
            case '3':
                $Liverecord = M("users_liverecord");

                $anchor_live_long_total=$Liverecord->sum("endtime-starttime");
                if(!$anchor_live_long_total){
                    $anchor_live_long_total=0;
                }
                if($anchor_live_long_total>0){
                    $anchor_live_long_total=number_format(floor($anchor_live_long_total/60));
                }            
                $xlsName  = "主播数据导出";
                $cellName = array('A','B','C','D','E');
                
                $xlsCell  = array('date','anchor_today','anchor_live_today','anchor_live_long_today');
                
                $xlsData[]=[
                    'date'=>'总直播时长    '.$anchor_live_long_total.' 分钟',
                    'anchor_today'=>'',
                    'anchor_live_today'=>'',
                    'anchor_live_long_today'=>'',
                    'ismerge'=>'1',
                ];
                
                $xlsData[]=[
                    'date'=>date("Y-m-d",$start).'至'.date("Y-m-d",($end-1)),
                    'anchor_today'=>'',
                    'anchor_live_today'=>'',
                    'anchor_live_long_today'=>'',
                    'ismerge'=>'1',
                ];
                
                $xlsData[]=[
                    'date'=>'时间',
                    'anchor_today'=>'开播主播数量（位）',
                    'anchor_live_today'=>'直播次数（ 次）',
                    'anchor_live_long_today'=>'直播时长（分钟）',
                    'ismerge'=>'0',
                ];
                
                for($i=$start;$i<$end;$i+=60*60*24){
                    $i2=$i+60*60*24;
                    $info=$this->getAnchorInfo($i,$i2);
                    $info['date']=date("Y-m-d",$i);

                    $info['ismerge']='0';
                    $xlsData[]=$info;
                }

                
                break;
            case '4':
            
                $xlsName  = "财务导出";
                $cellName = array('A','B','C','D','E');
                
                $Charge=M('users_charge');
                $charge_total=$Charge->where("status=1")->sum("money");
                if(!$charge_total){
                    $charge_total=0;
                }
                if($charge_total>0){
                    $charge_total=number_format($charge_total);
                }
                
                $xlsCell  = array('date','total','ali','wx','apple');
                
                $xlsData[]=[
                    'date'=>'历史总收益    '.$charge_total.' 元',
                    'total'=>'',
                    'ali'=>'',
                    'wx'=>'',
                    'apple'=>'',
                    'ismerge'=>'1',
                ];
                
                $xlsData[]=[
                    'date'=>date("Y-m-d",$start).'至'.date("Y-m-d",($end-1)),
                    'total'=>'',
                    'ali'=>'',
                    'wx'=>'',
                    'apple'=>'',
                    'ismerge'=>'1',
                ];
                
                $xlsData[]=[
                    'date'=>'时间',
                    'total'=>'充值金额（元）',
                    'ali'=>'支付宝充值（ 元）',
                    'wx'=>'微信充值（元）',
                    'apple'=>'苹果充值（ 元）',
                    'ismerge'=>'0',
                ];
                
                for($i=$start;$i<$end;$i+=60*60*24){
                    $i2=$i+60*60*24;
                    $char_info=$this->getCharge($i,$i2);
                    $info=[];
                    $info['date']=date("Y-m-d",$i);
                    $info['total']=$char_info['money'][0];
                    $info['ali']=$char_info['money'][1];
                    $info['wx']=$char_info['money'][2];
                    $info['apple']=$char_info['money'][3];
                    $info['ismerge']='0';
                    $xlsData[]=$info;
                }
                break;
            case '5':
                $xlsName  = "提现导出";
                $cellName = array('A','B','C','D');
                $Cash=M('users_cashrecord');
                
                $cash_total=$Cash->where("status=1")->sum("money");
                if(!$cash_total){
                    $cash_total=0;
                }
                if($cash_total>0){
                    $cash_total=number_format($cash_total);
                }
                
                $xlsCell  = array('date','cash_apply','cash_adopt','cash_anchor');
                
                $xlsData[]=[
                    'date'=>'总提现金额    '.$cash_total.' 元',
                    'cash_apply'=>'',
                    'cash_adopt'=>'',
                    'cash_anchor'=>'',
                    'ismerge'=>'1',
                ];
                
                $xlsData[]=[
                    'date'=>date("Y-m-d",$start).'至'.date("Y-m-d",($end-1)),
                    'cash_apply'=>'',
                    'cash_adopt'=>'',
                    'cash_anchor'=>'',
                    'ismerge'=>'1',
                ];
                
                $xlsData[]=[
                    'date'=>'时间',
                    'cash_apply'=>'申请金额（元）',
                    'cash_adopt'=>'已通过金额（元）',
                    'cash_anchor'=>'主播提现数量（ 位）',
                    'ismerge'=>'0',
                ];
                
                for($i=$start;$i<$end;$i+=60*60*24){
                    $i2=$i+60*60*24;
                    $info=$this->getCash($i,$i2);
                    $info['date']=date("Y-m-d",$i);
                    $info['ismerge']='0';
                    $xlsData[]=$info;
                }
                break;
        }
        
        $this->exportExcel($xlsName,$xlsCell,$xlsData,$cellName);    
    }
	/**导出Excel 表格
   * @param $expTitle 名称
   * @param $expCellName 参数
   * @param $expTableData 内容
   * @throws \PHPExcel_Exception
   * @throws \PHPExcel_Reader_Exception
   */
	protected function exportExcel($xlsName,$expCellName,$expTableData,$cellName)
	{
		$xlsTitle = iconv('utf-8', 'gb2312', $xlsName);//文件名称
		$fileName = $xlsTitle.'_'.date('YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
		$cellNum = count($expCellName);
		$dataNum = count($expTableData);
		vendor("PHPExcel.PHPExcel");
		$objPHPExcel = new \PHPExcel();
        $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);


		for($i=0;$i<$dataNum;$i++){
            $cellinfo=$expTableData[$i];
            if($cellinfo['ismerge']==1){
                $objPHPExcel->getActiveSheet()->mergeCells('A'.($i+1).':'.end($cellName).($i+1));//合并单元格（如果要拆分单元格是需要先合并再拆分的，否则程序会报错）
                
                $objPHPExcel->getActiveSheet(0)->setCellValue('A'.($i+1), $cellinfo[$expCellName[0]]);
            }else{
                for($j=0;$j<$cellNum;$j++){
                    $key=$expCellName[$j];
                    $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+1), $cellinfo[$key]);
                }
            }
			
		}
		header('pragma:public');
		header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
		header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');//Excel5为xls格式，excel2007为xlsx格式
		$objWriter->save('php://output');
		exit;
	}    
}