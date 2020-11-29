<?php


namespace Hanson\LaravelAdminWechat\Services;


use Carbon\Carbon;
use EasyWeChat\Factory;
use Hanson\LaravelAdminWechat\Models\WechatConfig;
use Illuminate\Support\Facades\Cache;

class ConfigService
{
    /**
     * 通过 app id 获取微信实例
     *
     * @param string $appId
     * @return \EasyWeChat\MiniProgram\Application|\EasyWeChat\OfficialAccount\Application
     */
    public function getInstanceByAppId(string $appId)
    {
        $config = Cache::get('wechat.config.app_id.'.$appId);

        if (!$config) {
            $model = WechatConfig::query()->where('app_id', $appId)->firstOrFail();

            $config = ['app_id' => $model->app_id, 'secret' => $model->secret, 'type' => $model->type];

            Cache::forever('wechat.config.app_id.'.$model->app_id, $config);
        }

        return $this->getInstance($config);
    }

    /**
     * 获取实例
     *
     * @param array $config
     * @return \EasyWeChat\MiniProgram\Application|\EasyWeChat\OfficialAccount\Application
     */
    protected function getInstance(array $config)
    {
        if ($config['type'] == 1) {
            return Factory::officialAccount([
                'app_id' => $config['app_id'],
                'secret' => $config['secret'],
            ]);
        } else {
            return Factory::miniProgram([
                'app_id' => $config['app_id'],
                'secret' => $config['secret'],
            ]);
        }
    }

    /**
     * 获取后台当前操作的实例
     *
     * @return \EasyWeChat\MiniProgram\Application|\EasyWeChat\OfficialAccount\Application
     */
    public function getAdminCurrentApp()
    {
        $config = $this->getCurrent();

        return $this->getInstanceByAppId($config->app_id);
    }

    /**
     * 获取后台当前操作的 WechatConfig 类
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getCurrent()
    {
        $key = config('admin.extensions.wechat.admin_current_key', 'wechat.admin.current');

        $appId = Cache::get($key);

        if (!$appId) {
            $config = WechatConfig::query()->first();

            if (!$config) {
                return null;
            }

            Cache::put($key,$config->app_id, Carbon::now()->addHours(2));

            return $config;
        }

        return WechatConfig::query()->where('app_id', $appId)->first();
    }
}
