<?php


namespace Hanson\LaravelAdminWechat\Services;


use Carbon\Carbon;
use EasyWeChat\Factory;
use Hanson\LaravelAdminWechat\Models\WechatConfig;
use Hanson\LaravelAdminWechat\Models\WechatMerchant;
use Illuminate\Support\Facades\Cache;

class MerchantService
{
    /**
     * 通过 mch id 获取微信支付实例
     *
     * @param string $mchId
     * @return \EasyWeChat\Payment\Application
     */
    public function getInstanceByMchId(string $mchId)
    {
        $config = Cache::get('wechat.merchant.mch_id.'.$mchId);

        if (!$config) {
            $model = WechatMerchant::query()->where('mch_id', $mchId)->firstOrFail();

            $config = ['app_id' => $model->app_id, 'mch_id' => $model->mch_id, 'key' => $model->key, 'notify_url' => $model->notify_url];

            Cache::forever('wechat.merchant.mch_id.'.$mchId, $config);
        }

        return Factory::payment([
            'app_id' => $config['app_id'],
            'mch_id' => $config['mch_id'],
            'key' => $config['key'],
            'notify_url' => $config['notify_url'],
        ]);
    }
}
