<?php
/*
alipay当面付，个人版
申请地址：https://b.alipay.com/signing/productDetailV2.htm?productId=I1011000290000001003
申请权限：个人真实信息，营业执照（非必须），门店照片（可PS）
使用限额：
1、签约时未提供同名营业执照（营业执照主体与支付宝账户认证主体同名），收款将会受到一定的限制，具体限制规则为交易限额：单笔收款≤1000，单日收款≤5W，不区分借记或贷记渠道。
2、签约时提供了同名营业执照，或者签约后补充了同名营业执照，收款不受限额。
使用方式：手机扫码支付，手机可直接跳转APP支付，PC可展示二维码供手机扫码

*/

class AlipayF2F{

    protected $appid;
    protected $notifyUrl;
    protected $rsaPrivateKey;
    protected $alipayPublicKey;
    protected $qrContent;

    protected $charset;

    protected $payInfo;

    public function __construct(){
        $this->charset = 'utf8';
    }

    //初始化基础信息
    public function setAppid($appid){
        $this->appId = $appid;
    }
    public function setNotifyUrl($notifyUrl){
        $this->notifyUrl = $notifyUrl;
    }
    public function setRsaPrivateKey($rsaPrivateKey){
        $this->rsaPrivateKey = $rsaPrivateKey;
    }
    public function setAlipayPublicKey($alipayPublicKey){
        $this->alipayPublicKey = $alipayPublicKey;
    }
    public function setQrContent($qrContent){
        $this->qrContent = $qrContent;
    }


    //初始化支付信息

    public function setPayInfo($payInfo){
        /*
        $requestConfigs = array(
            'out_trade_no'=>$this->outTradeNo,//订单号
            'total_amount'=>$this->totalFee, //单位 元
            'subject'=>$this->orderName,  //订单标题
        );
        */
        $this->payInfo = $payInfo;
    }

    //统一下单
    public function doPay(){
        $commonConfigs = array(
            //公共参数
            'app_id' => $this->appId,
            'method' => 'alipay.trade.precreate', 
            'format' => 'JSON',
            'charset'=>$this->charset,
            'sign_type'=>'RSA2',
            'timestamp'=>date('Y-m-d H:i:s'),
            'version'=>'1.0',
            'notify_url' => $this->notifyUrl,
            'biz_content'=>json_encode($this->payInfo),
        );
        $commonConfigs["sign"] = $this->generateSign($commonConfigs, $commonConfigs['sign_type']);
        $result = $this->curlPost('https://openapi.alipay.com/gateway.do',$commonConfigs);
        return json_decode($result,true);


    }

    //查单
    public function queryOrder($out_trade_no){
        $orderIdInfo = array('out_trade_no' =>$out_trade_no);
        $commonConfigs = array(
            //公共参数
            'app_id' => $this->appId,
            'method' => 'alipay.trade.query', 
            'format' => 'JSON',
            'charset'=>$this->charset,
            'sign_type'=>'RSA2',
            'timestamp'=>date('Y-m-d H:i:s'),
            'version'=>'1.0',
            'notify_url' => $this->notifyUrl,
            'biz_content'=>json_encode($orderIdInfo)
        );
        $commonConfigs["sign"] = $this->generateSign($commonConfigs, $commonConfigs['sign_type']);
        $result = $this->curlPost('https://openapi.alipay.com/gateway.do',$commonConfigs);
        //return json_decode($result,true);
        return $result;

    }
    
    //关闭订单
    public function closeOrder($out_trade_no){
        $orderIdInfo = array('out_trade_no' =>$out_trade_no);
        $commonConfigs = array(
            //公共参数
            'app_id' => $this->appId,
            'method' => 'alipay.trade.close', 
            'format' => 'JSON',
            'charset'=>$this->charset,
            'sign_type'=>'RSA2',
            'timestamp'=>date('Y-m-d H:i:s'),
            'version'=>'1.0',
            'notify_url' => $this->notifyUrl,
            'biz_content'=>json_encode($orderIdInfo)
        );
        $commonConfigs["sign"] = $this->generateSign($commonConfigs, $commonConfigs['sign_type']);
        $result = $this->curlPost('https://openapi.alipay.com/gateway.do',$commonConfigs);
        //return json_decode($result,true);
        return $result;

    }    

    //生成二维码
    public function createQr(){
       $qrUrl = "https://www.pgyer.com/qrCodePNG/generateQR?content=".$this->qrContent;

       return $qrUrl;
    }
    
    //打印日志
    public function wLog($info){
    	file_put_contents('alipayF2F_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').json_encode($info)."\r\n",FILE_APPEND);

    }

    //验签,用于异步回调的验证
    public function rsaCheck($params) {
        $sign = $params['sign'];
        $signType = $params['sign_type'];
        unset($params['sign_type']);
        unset($params['sign']);
        return $this->verify($this->getSignContent($params), $sign, $signType);
    }

    function verify($data, $sign, $signType = 'RSA') {
        $pubKey= $this->alipayPublicKey;
        $res = "-----BEGIN PUBLIC KEY-----\n" .
            wordwrap($pubKey, 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";
        ($res) or die('支付宝RSA公钥错误。请检查公钥文件格式是否正确');

        //调用openssl内置方法验签，返回bool值
        if ("RSA2" == $signType) {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res, version_compare(PHP_VERSION,'5.4.0', '<') ? SHA256 : OPENSSL_ALGO_SHA256);
        } else {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res);
        }

        return $result;
    }

    public function generateSign($params, $signType = "RSA"){
        return $this->sign($this->getSignContent($params), $signType);
    }

    protected function sign($data, $signType = "RSA"){
        $priKey=$this->rsaPrivateKey;
        $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($priKey, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";
        ($res) or die('您使用的私钥格式错误，请检查RSA私钥配置');
        if ("RSA2" == $signType) {
            openssl_sign($data, $sign, $res, version_compare(PHP_VERSION,'5.4.0', '<') ? SHA256 : OPENSSL_ALGO_SHA256); //OPENSSL_ALGO_SHA256是php5.4.8以上版本才支持
        } else {
            openssl_sign($data, $sign, $res);
        }
        $sign = base64_encode($sign);
        return $sign;
    }

    //校验$value是否非空
    protected function checkEmpty($value){
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;
        return false;
    }
    //拼接签名串
    public function getSignContent($params) {
        ksort($params);
        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {
                // 转换成目标字符集
                $v = $this->characet($v, $this->charset);
                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }
        unset ($k, $v);
        return $stringToBeSigned;
    }

    //转换字符集编码

    public function characet($data, $targetCharset) {
        if (!empty($data)) {
            $fileType = $this->charset;
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
            }
        }
        return $data;
    }

    //post请求
    public function curlPost($url = '', $postData = '', $options = array()){
        if (is_array($postData)) {
            $postData = http_build_query($postData);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); //设置cURL允许执行的最长秒数
        if (!empty($options)) {
            curl_setopt_array($ch, $options);
        }
        //https请求 不验证证书和host
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

}
