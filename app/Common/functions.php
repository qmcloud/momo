<?php
/**************************
 * 本文件是公共功能函数库
 * author ：sqc
 * @php 小T科技
 *
 **************************/

/**
 * 获取客户端ip地址
 * @return string ip地址
 */
function getClientIP()
{
    if (getenv("HTTP_CLIENT_IP"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("HTTP_X_FORWARDED_FOR"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("REMOTE_ADDR"))
        $ip = getenv("REMOTE_ADDR");
    else $ip = "Unknow";
    return $ip;
}

/**
 * 价格格式化
 *
 * @param int $price
 * @return string    $price_format
 */
function PriceFormat($price)
{
    $price_format = number_format($price, 2, '.', '');
    return $price_format;
}


/**
 * PHP精确计算  主要用于货币的计算用法
 * @param $n1 第一个数
 * @param $symbol 计算符号 + - * / %
 * @param $n2 第二个数
 * @param string $scale 精度 默认为小数点后两位
 * @return  string
 */
function PriceCalculate($n1, $symbol, $n2, $scale = '2')
{
    $res = "";
    if (function_exists("bcadd")) {
        switch ($symbol) {
            case "+"://加法
                $res = bcadd($n1, $n2, $scale);
                break;
            case "-"://减法
                $res = bcsub($n1, $n2, $scale);
                break;
            case "*"://乘法
                $res = bcmul($n1, $n2, $scale);
                break;
            case "/"://除法
                $res = bcdiv($n1, $n2, $scale);
                break;
            case "%"://求余、取模
                $res = bcmod($n1, $n2, $scale);
                break;
            default:
                $res = "";
                break;
        }
    } else {
        switch ($symbol) {
            case "+"://加法
                $res = $n1 + $n2;
                break;
            case "-"://减法
                $res = $n1 - $n2;
                break;
            case "*"://乘法
                $res = $n1 * $n2;
                break;
            case "/"://除法
                $res = $n1 / $n2;
                break;
            case "%"://求余、取模
                $res = $n1 % $n2;
                break;
            default:
                $res = "";
                break;
        }

    }
    return $res;

}

/**
 * 价格由元转分
 * @param $price 金额
 * @return int
 */
function ncPriceYuan2fen($price)
{
    $price = (int)PriceCalculate(100, "*", PriceFormat($price));
    return $price;
}


/**
 * 日志记录函数  该方法是用户插入mysql库中的 （与文件型日志不冲突 该方法在于admin后台可以实时看到异常 然后及时处理）
 * @param [type]  $type        错误日志记录类型 如order 订单类型的错误
 * @param [type]  $info        错误日志记录说明
 * @param integer $error_level 错误等级  1:notice 2:warn 3:error
 * @param string $msg 错误内容
 * @param string $node 错误节点  一般可以写当前路由$request->path();
 * @param string $remark 其他备注
 */
function add_my_log($type, $info, $error_level = 1, $msg = '',  $node = 'node',$goal_id = 0, $remark = '')
{
    $new_log = new App\Models\ErrorsLog;
    $new_log->type = $type;
    $new_log->error_level = $error_level;
    $new_log->info = $info;
    $new_log->msg = $msg;
    $new_log->node = $node;
    $new_log->ip = getClientIP();
    $new_log->result = 1;
    $new_log->remark = $remark;
    $new_log->goal_id = $goal_id;
    $new_log->save();
}

/**
 * 微信消息的通知
 * @param [type] $order_info [订单信息]
 * @param [type] $status [操作代码]
 */
function addWxMsg_toUser($orders_info, $status = '26')
{
    if (empty($orders_info)) return false;
    $app = EasyWeChat\Factory::officialAccount(config('wechat.official_account.default'));
    $User = new App\User;
    $user_info = $User->find($orders_info['uid']);
    $url = env('APP_URL') . '/my-order?status=3';
    if (!$user_info->openid) {
        return false;
    }
    $data = array(
        "keyword1" => array($orders_info['order_sn'], "#888888"),// 订单号
        "keyword2" => array($orders_info['pay_price'], "#336699"),// 订单金额
        "keyword3" =>array($orders_info['plate_number'], "#888888"),// 车辆信息
        "keyword4" => array(date('Y-m-d',strtotime($orders_info['apply_time'])), "#888888"),
        "remark" => array($orders_info['check_info']."  请您尽快核查~", "#5599FF"),
    );
// 检测通过
    if($status == App\Models\CheckStation\ApplyOrder::STATUS_PASS){
        $data["first"] = array("您的车辆已检测通过", '#43CD80');
    }else if($status == App\Models\CheckStation\ApplyOrder::STATUS_NO_PASS){
        $data["first"] = array("您的车辆未检测通过", '#EE2C2C');
    }
    $app->template_message->send([
        'touser' => $user_info->openid,
        'template_id' => 'FQ7dAfjzepCgFtPhfLjOmONX0SkjVSuL_2zYNtLNuu8',
        'url' => $url,
        'data' =>$data
    ]);
}

//if (empty($orders_info)) return false;
//$app = EasyWeChat\Factory::officialAccount(config('wechat.official_account.default'));
//$User = new App\User;
//$user_info = $User->find($orders_info['uid']);
//$url = env('APP_URL') . '/my-order?status=3';
//if (!$user_info->openid) {
//    return false;
//}
//$data = array(
//    "keyword1" => array($orders_info['true_name'], "#336699"),// 客户
//    "keyword2" => array($orders_info['pay_price'], "#336699"),// 订单金额
//    "keyword3" => array($orders_info['order_sn'], "#888888"),// 订单号
//    "keyword4" => array(date('Y-m-d H:i:s'), "#888888"),
//    "remark" => array($orders_info['check_info']."  请您尽快核查~", "#5599FF"),
//);
//// 检测通过
//if($status == App\Models\CheckStation\ApplyOrder::STATUS_PASS){
//    $data["first"] = array("您的车辆已检测通过", '#43CD80');
//}else if($status == App\Models\CheckStation\ApplyOrder::STATUS_NO_PASS){
//    $data["first"] = array("您的车辆未检测通过", '#EE2C2C');
//}
//$app->template_message->send([
//    'touser' => $user_info->openid,
//    'template_id' => '30kgXpgxkuH-FK7f4iN3ya-l9vO4IS7wPbI5TghWr7M',
//    'url' => $url,
//    'data' =>$data
//]);

?>