<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace Home\Controller;
use Common\Controller\HomebaseController; 

class PaymentController extends HomebaseController {
	
    
	public function index() {	
		LogIn();
		$uid=session("uid");
        if(!$uid){
            $this->error('请先登录');
        }
		$rules=M("charge_rules");
    	$lists = $rules
				->order("orderno asc")
				->select();
    	$this->assign('lists', $lists);
		$getConfigPri=getConfigPri();	
		$user=M('users')->where("id={$uid}")->find();
		$this->assign("user",$user);
		$this->assign("getConfigPri",$getConfigPri);
    	$this->display();
    }
	//调用微信扫码支付接口=以及支付宝扫码支付===
  public function chargepay()
	{
        $uid=session("uid");
        if($uid<1){
            $this->error("您的登陆状态失效，请重新登陆！");
        }
		$Chargedetail = D("users_charge");
		$changeid = (int)I('changeid');
		
        if(!$changeid){
            $this->error("参数错误");
        }
		$where['id']=$changeid;
		$charge=M("charge_rules")->where($where)->find();
		
		if(!$charge ){
			$this->error("订单信息有误，请重新提交");
		}
		
		
		$money = $charge['money'];
		$coin = $charge['coin'];
		$give = $charge['give'];
		//读取后台配置信息
		$getConfigPri=getConfigPri();	
		$getConfigPub=getConfigPub();
		//当前域名
		$pay_url=$getConfigPub['site']; 
		//商户订单号 //便于筛选，订单号为 uid_touid_时间戳_随机数
		$orderid = $uid."_".$uid."_".date("mdHis")."_".rand(999,9999);
        $paytype=$_POST['c_PPPayID'];
		if($paytype == 'weixin')
		{			  
			require_once "./wxpay/lib/WxPay.Api.php"; 
			require_once "./wxpay/pay/WxPay.NativePay.php";
			//debug_backtrace
			$notify = new \NativePay();
			//订单名称
			$order_name = "充值".$getConfigPub['name_coin'].",价值为".$coin;
			///支付记录
            //touid 赠送人id money充值金额 coin兑换点数 orderno商户订单号 trade_no第三方订单号 status订单支付状态 addtime订单提交时间 type支付方式(1 支付宝2微信 3苹果支付)
			$data=array(
				'touid' =>$uid,
				'uid'=>$uid,
				'money' => $money,
				'coin' =>$coin,
				'coin_give' =>$give,
				'trade_no'=>'',
				'orderno'=>$orderid,
				'status'=>0,		
				'addtime'=>time(),
				'type'=>2,
				'ambient'=>2,
			);	
			$userid=$Chargedetail->add($data);
			//支付记录
			$money2 = $money*100;
			$input = new \WxPayUnifiedOrder();
			$input->SetBody($order_name);
			$input->SetAttach($order_name);
			$input->SetOut_trade_no($orderid);
			$input->SetTotal_fee($money2);
			$input->SetTime_start(date("YmdHis"));
			$input->SetTime_expire(date("YmdHis", time() + 600));
			$input->SetGoods_tag("test");
			$input->SetNotify_url($pay_url."/wxpay/pay/notify_jsapi.php");
			$input->SetTrade_type("NATIVE");
			$input->SetProduct_id("123456789");
			$result = $notify->GetPayUrl($input);
			$url2 = $result["code_url"];        			
			echo '<html>
				    <head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head>
				    <body>
				      <form name="form1" id="form1" method="post" action="/index.php?m=Payment&a=wxpay" target="_self">
								<input type="hidden" name="url" value="'. $url2.'" />
								<input type="hidden" name="money" value="'. $money.'" />
								<input type="hidden" name="coin" value="'. $coin.'" />
								<input type="hidden" name="orderid" value="'. $orderid.'" />
								<script language="javascript">document.form1.submit();</script>
							</form></body></html>
						';	
				exit();	
        }
		//支付宝扫码支付===========================
        if($paytype == 'zhifubao')
		{
			//获取后台设置的 配置信息
			/*  $siteconfig=M("siteconfig")->where("id='1'")->find(); */
			//↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
			//合作身份者id，以2088开头的16位纯数字
			$alipay_config['partner']=$getConfigPri['aliapp_partner'];
			//安全检验码，以数字和字母组成的32位字符
			$alipay_config['key']			= $getConfigPri['aliapp_check'];
			//支付宝账号
			$alipay_config['seller_email'] =$getConfigPri['aliapp_seller_id'];
			//↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
			//签名方式 不需修改
			$alipay_config['sign_type']    = strtoupper('MD5');
			//字符编码格式 目前支持 gbk 或 utf-8
			$alipay_config['input_charset']= strtolower('utf-8');
			//ca证书路径地址，用于curl中ssl校验
			//请保证cacert.pem文件在当前文件夹目录中
			$alipay_config['cacert']    = SITE_PATH.'alipay/cacert.pem';
			//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
			$alipay_config['transport']    = 'http';
			//↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
			require_once "./alipay/lib/alipay_submit.class.php";
			//支付记录
			//touid 赠送人id money充值金额 coin兑换点数 orderno商户订单号 trade_no第三方订单号 status订单支付状态 addtime订单提交时间 type支付方式(1 支付宝2微信 3苹果支付)
			$data=array(
					'touid' =>$uid,
					'uid'=>$uid,
					'money' => $money,
					'coin' =>$coin,
					'coin_give' =>$give,
					'trade_no'=>'',
					'orderno'=>$orderid,
					'status'=>0,
					'addtime'=>time(),
					'type'=>1,
					'ambient'=>1,
				);	
			$userid=$Chargedetail->add($data);
            //支付记录					
			/**************************请求参数**************************/
			//支付类型
			$payment_type = "1";
			//必填，不能修改
			//服务器异步通知页面路径
			$notify_url = $pay_url."/index.php/Payment/alipay_d_notify";
			//需http://格式的完整路径，不能加?id=123这类自定义参数
			//页面跳转同步通知页面路径
			$return_url = $pay_url."/index.php?m=Payment&a=index";
			//需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
			//商户网站订单系统中唯一订单号，必填
			//订单名称
			$subject ="支付宝充值".$getConfigPub['name_coin'];
			//付款金额
			$total_fee =$money;
			//订单描述
			$body = "充值".$getConfigPub['name_coin'].",价格为".$coin;
			//商品展示地址
			$show_url = $this->sitename;
			//需以http://开头的完整路径，例如：http://www.xxx.com/myorder.html
			//防钓鱼时间戳
			$anti_phishing_key = "";
			//若要使用请调用类文件submit中的query_timestamp函数
			//客户端的IP地址
			$exter_invoke_ip = "";
			//非局域网的外网IP地址，如：221.0.0.1
			/************************************************************/
			//构造要请求的参数数组，无需改动
				$parameter = array(
						"service" => "create_direct_pay_by_user",
						"partner" => trim($alipay_config['partner']),
						"payment_type"	=> $payment_type,
						"notify_url"	=> $notify_url,
						"return_url"	=> $return_url,
						"seller_email"	=> trim($alipay_config['seller_email']),
						"out_trade_no"	=> $orderid,
						"subject"	=> $subject,
						"total_fee"	=> $total_fee,
						"body"	=> $body,
						"show_url"	=> $show_url,
						"qr_pay_mode"=>2,
						"anti_phishing_key"	=> $anti_phishing_key,
						"exter_invoke_ip"	=> $exter_invoke_ip,
						"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
				);
			//建立请求
			$alipaySubmit = new \AlipaySubmit($alipay_config);
			$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "1"); 
			/*  $html_text = $alipaySubmit->buildRequestPara($parameter); */      
			echo $html_text;
            exit;
		}

        if($paytype == 'aliscan'){
            //https://open.alipay.com 账户中心->密钥管理->开放平台密钥，填写添加了电脑网站支付的应用的APPID
            $appid = $getConfigPri['aliscan_appid'];

            //付款成功后的异步回调地址
            $notifyUrl = $getConfigPub['site'].'/appapi/aliscan/notify';

            //签名算法类型，支持RSA2和RSA，推荐使用RSA2
            $signType = 'RSA2';	

            //商户私钥，填写对应签名算法类型的私钥，如何生成密钥参考：https://docs.open.alipay.com/291/105971和https://docs.open.alipay.com/200/105310
            $rsaPrivateKey=$getConfigPri['aliscan_rsakey'];
            
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
                'ambient'=>'1',
            );	
            M('users_charge')->add($chargedata);
            
            echo '<!DOCTYPE html>
                    <html lang="zh-CN">
                    <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                        <title>支付宝-当面付</title>
                        <meta http-equiv="X-UA-Compatible" content="IE=edge">
                        <link type="text/css" rel="stylesheet" href="/public/home/css/style.css"/>
                        
                    </head>
                    <body id="bp_center">   
                        <div class="cashier_content">
                            <div class="cashier_head">
                                <div class="cashier_head_logo"><a href="/">支付宝-当面付</a></div>
                            </div>
                            <div class="cashier_center">
                                <div class="wechat_head">
                                    <span>使用支付宝支付：</span>
                                    <span class="wechat_head_info">消费￥'.$money.'元充值'.$coin.'虚拟币</span>
                                </div>
                                <div class="wechat_qrcode" title=""><canvas width="248" height="248" style="display: none;"></canvas><img alt="Scan me!" src="'.$url.'" style="display: block;width:248px;height:248px;"></div>
                                <div class="wechat_btn">
                                    <span class="wechat_btn_info">使用支付宝扫描二维码以完成支付</span>
                                </div>
                            </div>
                        </div>
                        <script src="/public/home/js/jquery.1.10.2.js"></script>
                        <script type="text/javascript">
                            function query(){
                                $.ajax({
                                    type : "POST",
                                    url : "/index.php?g=home&m=Payment&a=aliscan_query",
                                    data : {orderid:"'.$orderid.'"},
                                    dataType:"json",
                                    success : function(result) {
                                        //console.log(result);
                                        res = result;

                                        if(res.alipay_trade_query_response.code==="10000" ){
                                            if(res.alipay_trade_query_response.trade_status=="WAIT_BUYER_PAY"){
                                                $(".wechat_btn_info").html("二维码扫描成功，等待支付");               		
                                            }
                                            
                                            if(res.alipay_trade_query_response.trade_status=="TRADE_SUCCESS"){
                                                alert("支付成功")
                                                window.location.href="/index.php?g=home&m=Payment&a=index"; 
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
	}	
	
	//==========================
	
	
	//支付宝即时到帐  返回处理
	public function alipay_d_notify(){
		$Chargedetail = D("users_charge");
		//读取后台配置信息
		$getConfigPri=getConfigPri();	
		$getConfigPub=getConfigPub();
		//↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
		//合作身份者id，以2088开头的16位纯数字
		$alipay_config['partner']=$getConfigPri['aliapp_partner'];
		//安全检验码，以数字和字母组成的32位字符
		$alipay_config['key']			= $getConfigPri['aliapp_check'];
		//支付宝账号
		$alipay_config['seller_email'] =$getConfigPri['aliapp_seller_id'];
		//↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
		//签名方式 不需修改
		$alipay_config['sign_type']    = strtoupper('MD5');
		//字符编码格式 目前支持 gbk 或 utf-8
		$alipay_config['input_charset']= strtolower('utf-8');
		//ca证书路径地址，用于curl中ssl校验
		//请保证cacert.pem文件在当前文件夹目录中
		$alipay_config['cacert']    = SITE_PATH.'alipay/cacert.pem';
		//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
		$alipay_config['transport']    = 'http';
		//↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓	
		require_once("./alipay/lib/alipay_notify.class.php");
		//计算得出通知验证结果
		$alipayNotify = new \AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyNotify();
		if($verify_result) {//验证成功
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//请在这里加上商户的业务逻辑程序代
			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
			//获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
			//session("uid")."_".session("uid")."_".date("mdHis")."_".rand(999,9999); 
			//商户订单号
			$out_trade_no = $_POST['out_trade_no'];
			//支付宝交易号
			$trade_no = $_POST['trade_no'];
			//交易状态
			$trade_status = $_POST['trade_status'];
			//交易金额
			$total_fee = $_POST['total_fee'];

			if($trade_status == 'TRADE_FINISHED') {
				//判断该笔订单是否在商户网站中已经做过处理
				//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
				//如果有做过处理，不执行商户的业务程序
					
				//注意：
				//退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
				//请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的

				//调试用，写文本函数记录程序运行情况是否正常
				//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		
			}else if ($trade_status == 'TRADE_SUCCESS') {
				//判断该笔订单是否在商户网站中已经做过处理
				//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
				//如果有做过处理，不执行商户的业务程序
					
				//注意：
				//付款完成后，支付宝系统发送该交易状态通知
				//请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的

				//调试用，写文本函数记录程序运行情况是否正常
				//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
	
				$orderinfo=$Chargedetail->where("orderno='{$out_trade_no}' and money='{$total_fee}' and status='0' and type='1'")->find();	

				if($orderinfo){
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
			}
			
		
			//更新会员余额
			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
			echo "fail";		//请不要修改或删除
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	/* 打印log */
		//file_put_contents('./logali.txt',date('y-m-d h:i:s').'  msg:'.$msg."\r\n",FILE_APPEND);
		}	
		else {
			//验证失败
			echo "fail";
			//调试用，写文本函数记录程序运行情况是否正常
			//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		}	
	}	
	//支付宝即时到帐  返回处理	
	
	
	/* 打印log */
	public function logali($msg){
		//file_put_contents('./logali.txt',date('y-m-d h:i:s').'  msg:'.$msg."\r\n",FILE_APPEND);
	}	
	//===========================
	
	
	public function getOrderStatus(){
		require_once "./wxpay/lib/WxPay.Api.php";
		require_once "./wxpay/lib/WxPay.Notify.php";
		require_once "./wxpay/pay/notify.php";
	   
	 	$orderid = $_GET['orderid'];
	 	$notify = new \PayNotifyCallBack();
		$wxpayStatus=$notify->Queryorder($orderid);
		 
		$order_info = explode("_",$orderid); 
		$uid = $order_info[0];
		$touid = $order_info[1];
		
		//获取该订单在数据库内的信息
		$Chargedetail = D("users_charge");
        
        $where['orderno']=$orderid;
        $where['touid']=$touid;
        $where['uid']=$uid;
        
		$orderinfo=$Chargedetail->where($where)->find();
		
	
		if($orderinfo['status']==1){
				echo "订单已完成";
				exit;
		}
       
		//订单是否真正支付
		if($wxpayStatus['trade_state']=='SUCCESS'){
			
			if($wxpayStatus['out_trade_no']==$orderid && $orderinfo['status']==0){

				//该数据库状态
				$data['status']="1";
				$coin=$orderinfo['coin']+$orderinfo['coin_give'];
				$Chargedetail -> where("id={$orderinfo['id']}")->save($data);

				$aaa=D("users")->execute('update cmf_users set coin=coin+'.($coin).' where id='.$orderinfo['touid']);
				echo 1;
			}else{
				echo "订单已完成";
			}

		}elseif($wxpayStatus['trade_state']=='NOTPAY'){
			echo 0 ; //未支付
		}else{
			echo -1;//未知错误
		} 
	}
	public function wxpay(){
		$url2=I('url');
		$money=I('money');
		$coin=I('coin');
		$orderid=I('orderid');
   	//logo
   	/* $logo = M("siteconfig")->getField("sitelogo"); */
   	$site = "http://".$_SERVER['HTTP_HOST'];
    $logo="微信扫码支付";
		$this->assign("site",$site);
		$this->assign("logo",$logo);
		$this->assign("nickname",$_SESSION['nickname']);
		$this->assign("url",$url2);
		
    $this->assign("money",$money);
    $this->assign("coin",$coin);
    $this->assign("orderid",$orderid);
    $this->display("wxpay");
	}
  

    public function aliscan_query(){
        
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
    public function aliscan_notify(){
        $request=$_POST;

        $this->logaliscan("request:".json_encode($_REQUEST));	
        $this->logaliscan("POST:".json_encode($_POST));	
        
        $configpri=getConfigPri();
        //支付宝公钥，账户中心->密钥管理->开放平台密钥，找到添加了支付功能的应用，根据你的加密类型，查看支付宝公钥
        $alipayPublicKey=$configpri['aliscan_pubkey'];
        
        require_once(SITE_PATH.'sdk/aliscan/AlipayF2F.class.php');
        
        $aliPay = new \AlipayF2F();
        $aliPay->setAlipayPublicKey($alipayPublicKey);


        //验证签名
        $result = $aliPay->rsaCheck($_POST);
        $this->logaliscan("验签:".$result);	
        if($result===true){
            //处理你的逻辑，例如获取订单号$_POST['out_trade_no']，订单金额$_POST['total_amount']等
            //程序执行完后必须打印输出“success”（不包含引号）。如果商户反馈给支付宝的字符不是success这7个字符，支付宝服务器会不断重发通知，直到超过24小时22分钟。一般情况下，25小时以内完成8次通知（通知的间隔频率一般是：4m,10m,10m,1h,2h,6h,15h）；
            
            //商户订单号
            $out_trade_no = $_POST['out_trade_no'];
            //交易号
            $trade_no = $_POST['trade_no'];
        
            $orderinfo=M("users_charge")->where("orderno='{$out_trade_no}'and type='4'")->find();	
            $this->logaliscan("orderinfo:".json_encode($orderinfo));	
            if($orderinfo){
                if($orderinfo['status']){
                    $this->logaliscan("orderno:".$out_trade_no.' 订单已确认');
                    echo 'success';
                    exit;
                }
                
                /* 更新会员虚拟币 */
                $coin=$orderinfo['coin']+$orderinfo['coin_give'];
                M("users")->where("id='{$orderinfo['touid']}'")->setInc("coin",$coin);
                /* 更新 订单状态 */
                M("users_charge")->where("id='{$orderinfo['id']}'")->save(array("status"=>1,"trade_no"=>$trade_no));

                $this->logaliscan("成功");	
                echo "success";		//请不要修改或删除
                exit;
            }else{
                $this->logaliscan("orderno:".$out_trade_no.' 订单信息不存在');		
            }

            echo 'success';
            exit();
        }
        echo 'error';
        exit();
    }
    
    /* 打印log */
	protected function logaliscan($msg){
		file_put_contents(SITE_PATH.'data/paylog/aliscan_pc_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').'  msg:'.$msg."\r\n",FILE_APPEND);
	}
    
    public function mylist() {
		
    	$this->display();
    }	

}


