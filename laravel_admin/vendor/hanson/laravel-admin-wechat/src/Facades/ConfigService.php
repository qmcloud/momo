<?php


namespace Hanson\LaravelAdminWechat\Facades;


use Hanson\LaravelAdminWechat\Models\WechatConfig;
use Illuminate\Support\Facades\Facade;

/**
 * Class MerchantService
 * @package Hanson\LaravelAdminWechat\Facades
 *
 * @method static \EasyWeChat\MiniProgram\Application|\EasyWeChat\OfficialAccount\Application getInstanceByAppId(string $appId)
 * @method static \EasyWeChat\MiniProgram\Application|\EasyWeChat\OfficialAccount\Application getInstance(array $config)
 * @method static \EasyWeChat\MiniProgram\Application|\EasyWeChat\OfficialAccount\Application getAdminCurrentApp()
 * @method static WechatConfig getCurrent()
 */
class ConfigService extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Hanson\LaravelAdminWechat\Services\ConfigService::class;
    }
}
