<?php


namespace Hanson\LaravelAdminWechat\Facades;


use EasyWeChat\Payment\Application;
use Illuminate\Support\Facades\Facade;

/**
 * Class MerchantService
 * @package Hanson\LaravelAdminWechat\Facades
 *
 * @method static Application getInstanceByMchId(string $mchId)
 */
class MerchantService extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Hanson\LaravelAdminWechat\Services\MerchantService::class;
    }
}
