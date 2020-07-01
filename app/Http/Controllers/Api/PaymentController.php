<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
// 引入自己需要操作的数据模型
use App\Http\Requests;
use App\Logic\Buy;
use App\Models\ShopOrder;
use Carbon\Carbon;
use EasyWeChat\Factory;
use Validator;

class PaymentController extends ApiController
{


    public function toPay(Request $request)
    {
        // 验证规则
        $validator = Validator::make($request->all(),
            [
                'orderId' => 'required'
            ],
            [
                'orderId.required' => '订单号缺失'
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 401);
        }
        // 先获取当前登录的用户信息
        $user_id = \Auth::user()->id;
        if (empty($user_id)) {
            return $this->failed('用户未登录', 401);
        }
        // 查询订单
        $order_info = ShopOrder::find($request->orderId)->toArray();
        if (empty($order_info)) {
            return $this->failed('订单不存在', 401);
        }
        // 开始生成预支付订单
        $buy = new Buy();
        $buy_info = $buy->pay_step1($order_info, \Auth::user()->openid);
        if ($buy_info) {
            return $this->success($buy_info);
        }
        return $this->failed('支付失败', 401);

    }

    /**
     * 支付提醒
     */
    public function notify()
    {
        $this->pay_log(var_export(file_get_contents('php://input'), true));
        $app = Factory::payment(config('wechat.payment.default'));
        $response = $app->handlePaidNotify(function ($message, $fail) {
            $this->pay_log('notify  ' . var_export($message, true));
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order = ShopOrder::where(['order_sn' => $message['out_trade_no']])->first();
            if (!$order) { // 如果订单不存在
                return true; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }
            // 如果订单存在
            // 检查订单是否已经更新过支付状态
            if ($order->order_status >= ShopOrder::STATUS_ALREADY_PAID) {
                return true; // 已经支付成功了就不再更新了
            }

            ///////////// <- 建议在这里调用微信的【订单查询】接口查一下该笔订单的情况，确认是已经支付 （给的钱少 这次就算了）//////////

            if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                // 用户是否支付成功
                if ($message['result_code'] === 'SUCCESS') {
                    // 判断支付金额
                    $order->pay_time = time(); // 更新支付时间为当前时间
                    $order->trade_no = $message['transaction_id'];// 微信交易号存放到数据库【退款等会用到】
                    if ($message['total_fee'] == ($order->actual_price * 100)) {
                        // 不是已经支付状态则修改为已经支付状态
                        $order->order_status = ShopOrder::STATUS_ALREADY_PAID;
                    } else {
                        //生产的
                        $order->order_status = ShopOrder::STATUS_WAIT_PAY;
                        //add_my_log('order', '支付金额与订单金额不符：' . $order->actual_price . '（元）=>' . $notify->total_fee . '(分)', 3, json_encode($order->toArray()), '微信支付回调');
                    }

                } elseif ($message['result_code'] === 'FAIL') {
                    // 用户支付失败
                    $order->order_status = ShopOrder::STATUS_WAIT_PAY;
                }
            } else { // 用户支付通讯失败
                return $fail('通信失败，请稍后再通知我');
            }
            $order->save(); // 保存订单
            return true; // 返回处理完成
        });
        return $response;
    }


    /**
     * 记录日志
     */
    private function pay_log($msg)
    {
        $msg = date('H:i:s') . "|" . $msg . "\r\n";
        $msg .= '| GET:' . var_export($_GET, true) . "\r\n";
        file_put_contents('./log/member_pay' . date('Y-m-d') . ".log", $msg, FILE_APPEND);
    }
}
