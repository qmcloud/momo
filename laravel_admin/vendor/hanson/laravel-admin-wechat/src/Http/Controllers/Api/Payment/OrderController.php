<?php

namespace Hanson\LaravelAdminWechat\Http\Controllers\Api\Payment;

use Carbon\Carbon;
use Hanson\LaravelAdminWechat\Events\OrderPaid;
use Hanson\LaravelAdminWechat\Facades\MerchantService;
use Hanson\LaravelAdminWechat\Models\WechatOrder;
use Illuminate\Routing\Controller;

class OrderController extends Controller
{
    public function paidNotify()
    {
        $app = MerchantService::getInstanceByMchId(request('mch_id'));

        $app->handlePaidNotify(function ($message, $fail) {
            $order = WechatOrder::query()->where([
                'mch_id' => request('mch_id'),
                'out_trade_no' => $message['out_trade_no'],
            ])->first();

            if (!$order || $order->paid_at) {
                return  true;
            }

            if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                // 用户是否支付成功
                if ($message['result_code'] === 'SUCCESS') {
                    $order->update(['paid_at' => Carbon::parse($message['time_end'])->toDateTimeString()]);

                    event(new OrderPaid($order));
                }
            } else {
                return $fail('通信失败，请稍后再通知我');
            }
        });
    }
}
