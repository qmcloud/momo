<?php
/**
 * 支付宝-当面付-扫码付
 */
namespace Appapi\Controller;
use Common\Controller\HomebaseController;
class AliscanController extends HomebaseController {

    /* 支付 */
	function index(){        
		//支付规则ID
        $uid = intval(I('uid'));
        $token = I('token');
		$changeid = intval(I('changeid'));
        
        if( !$uid || !$token || checkToken($uid,$token)==700 ){
			$this->assign("reason",'您的登陆状态失效，请重新登陆！');
			$this->display(':error');
			exit;
		} 
		$this->assign("uid",$uid);
		$this->assign("token",$token); 

        $configpub=getConfigPub();	
        $configpri=getConfigPri();	
        $switch=$configpri['aliscan_switch'];

        if(!$switch){
            $this->assign("reason",'该支付方式不可用');
			$this->display(':error');
			exit;
        }
		
		$charge=M("charge_rules")->where(['id'=>$changeid])->find();
		if(!$charge){
			$this->assign("reason",'订单信息有误，请重新提交');
			$this->display(':error');
			exit;
		}
        
		$money = $charge['money'];
		$coin = $charge['coin'];
		$give = $charge['give'];
		

		//商户订单号 //便于筛选，订单号为 uid_时间戳_随机数
		$orderid = $uid."_".date("mdHis")."_".rand(1000,9999); 
        
        
        //https://open.alipay.com 账户中心->密钥管理->开放平台密钥，填写添加了电脑网站支付的应用的APPID
        $appid = $configpri['aliscan_appid'];

        //付款成功后的异步回调地址
        $notifyUrl = $configpub['site'].'/appapi/aliscan/notify';

        //签名算法类型，支持RSA2和RSA，推荐使用RSA2
        $signType = 'RSA2';	

        //商户私钥，填写对应签名算法类型的私钥，如何生成密钥参考：https://docs.open.alipay.com/291/105971和https://docs.open.alipay.com/200/105310
        $rsaPrivateKey=$configpri['aliscan_rsakey'];
        
        require_once(SITE_PATH.'sdk/aliscan/AlipayF2F.class.php');

        $aliPay = new \AlipayF2F();

        $payInfo = array(
            'out_trade_no'=>$orderid,//订单号
            'total_amount'=>$money, //单位 元
            'subject'=>"虚拟币充值",  //订单标题
        );


        $aliPay->setAppid($appid);
        $aliPay->setNotifyUrl($notifyUrl);
        $aliPay->setRsaPrivateKey($rsaPrivateKey);
        $aliPay->setPayInfo($payInfo);


        $result = $aliPay->doPay();
        
        //$this->logali("doPay_result:".json_encode($result));
        
        $result = $result['alipay_trade_precreate_response'];
        if(!$result['code'] || $result['code']!='10000'){
            $this->assign("reason",$result['msg'].' : '.$result['sub_msg']);
			$this->display(':error');
			exit;
            

        }

        //生成二维码
        $aliPay->setQrContent($result['qr_code']);
        $url = $aliPay->createQr();
        
        //添加充值记录
		$chargedata=array(
            'uid'=>$uid,
			'touid' =>$uid,
			'money' => $money,
			'coin' =>$coin,
			'coin_give' =>$give,
			'orderno'=>$orderid,
			'status'=>0,		
			'addtime'=>time(),
			'type'=>'4',
		);	
        M('users_charge')->add($chargedata);
        
        echo '<!DOCTYPE html>
                <html>
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                    <title>支付宝-当面付</title>
                    <style>
                        .content{
                            text-align:center;
                        }
                        .content img{
                            width:60%;
                        }
                        .tips{
                            margin-top:30px;
                            font-size:32px;
                        }
                    </style>
                </head>
                <body>
                    <div class="content">
                        <a href="'.$result['qr_code'].'">
                            <img src="'.$url.'">
                        </a>
                        <div class="tips">
                            如果手机不能正常付款，请点击二维码！
                        </div>
                        <div id="scanInfo" class="tips"></div>
                    </div>
                    <script src="/public/js/jquery.js"></script>
                        <script type="text/javascript">
                            function query(){
                                $.ajax({
                                    type : "POST",
                                    url : "/index.php?g=appapi&m=aliscan&a=query",
                                    data : {orderid:"'.$orderid.'"},
                                    dataType:"json",
                                    success : function(result) {
                                        //console.log(result);
                                        res = result;

                                        if(res.alipay_trade_query_response.code==="10000" ){
                                            if(res.alipay_trade_query_response.trade_status=="WAIT_BUYER_PAY"){
                                                $("#scanInfo").html("<h1>二维码扫描成功，等待支付</h1>");               		
                                            }
                                            
                                            if(res.alipay_trade_query_response.trade_status=="TRADE_SUCCESS"){
                                                $("#content").empty();
                                                $("#scanInfo").html("<h1>支付成功</h1>");
                                            }

                                        }
                                    },
                                    error : function(e){
                                        console.log(e.status);
                                        console.log(e.responseText);
                                    }
                                });
                            }

                        setInterval("query()",2000);
                        </script>
                </body>
                </html>';
        exit;
	}
    
    public function query(){
        
        $configpub=getConfigPub();
        $configpri=getConfigPri();
        
        //https://open.alipay.com 账户中心->密钥管理->开放平台密钥，填写添加了电脑网站支付的应用的APPID
        $appid = $configpri['aliscan_appid'];

        //付款成功后的异步回调地址
        $notifyUrl = $configpub['site'].'/appapi/aliscan/notify';

        //签名算法类型，支持RSA2和RSA，推荐使用RSA2
        $signType = 'RSA2';	

        //商户私钥，填写对应签名算法类型的私钥，如何生成密钥参考：https://docs.open.alipay.com/291/105971和https://docs.open.alipay.com/200/105310
        $rsaPrivateKey=$configpri['aliscan_rsakey'];
        
        require_once(SITE_PATH.'sdk/aliscan/AlipayF2F.class.php');
        
        $aliPay = new \AlipayF2F();


        $aliPay->setAppid($appid);
        $aliPay->setNotifyUrl($notifyUrl);
        $aliPay->setRsaPrivateKey($rsaPrivateKey);

        $result = $aliPay->queryOrder($_POST['orderid']);

        echo $result;
        exit;
    }
    /* 回调 */
    function notify(){
        
        $request=$_POST;

        $this->logali("request:".json_encode($_REQUEST));	
        $this->logali("POST:".json_encode($_POST));	
        
        $configpri=getConfigPri();
        //支付宝公钥，账户中心->密钥管理->开放平台密钥，找到添加了支付功能的应用，根据你的加密类型，查看支付宝公钥
        $alipayPublicKey=$configpri['aliscan_pubkey'];
        
        require_once(SITE_PATH.'sdk/aliscan/AlipayF2F.class.php');
        
        $aliPay = new \AlipayF2F();
        $aliPay->setAlipayPublicKey($alipayPublicKey);


        //验证签名
        $result = $aliPay->rsaCheck($_POST);
        $this->logali("验签:".$result);	
        if($result===true){
            //处理你的逻辑，例如获取订单号$_POST['out_trade_no']，订单金额$_POST['total_amount']等
            //程序执行完后必须打印输出“success”（不包含引号）。如果商户反馈给支付宝的字符不是success这7个字符，支付宝服务器会不断重发通知，直到超过24小时22分钟。一般情况下，25小时以内完成8次通知（通知的间隔频率一般是：4m,10m,10m,1h,2h,6h,15h）；
            
            //商户订单号
            $out_trade_no = $_POST['out_trade_no'];
            //交易号
            $trade_no = $_POST['trade_no'];
        
            $orderinfo=M("users_charge")->where("orderno='{$out_trade_no}'and type='4'")->find();	
            $this->logali("orderinfo:".json_encode($orderinfo));	
            if($orderinfo){
                if($orderinfo['status']){
                    $this->logali("orderno:".$out_trade_no.' 订单已确认');
                    echo 'success';
                    exit;
                }
                
                /* 更新会员虚拟币 */
                $coin=$orderinfo['coin']+$orderinfo['coin_give'];
                M("users")->where("id='{$orderinfo['touid']}'")->setInc("coin",$coin);
                /* 更新 订单状态 */
                M("users_charge")->where("id='{$orderinfo['id']}'")->save(array("status"=>1,"trade_no"=>$trade_no));

                $this->logali("成功");	
                echo "success";		//请不要修改或删除
                exit;
            }else{
                $this->logali("orderno:".$out_trade_no.' 订单信息不存在');		
            }

            echo 'success';
            exit();
        }
        echo 'error';
        exit();
        
        	
        
    }
    
	/* 打印log */
	protected function logali($msg){
		file_put_contents(SITE_PATH.'data/paylog/aliscan_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').'  msg:'.$msg."\r\n",FILE_APPEND);
	}		    
}