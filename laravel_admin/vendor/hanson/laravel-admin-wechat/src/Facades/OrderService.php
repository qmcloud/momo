<?php


namespace Hanson\LaravelAdminWechat\Facades;

use Hanson\LaravelAdminWechat\Models\WechatOrder;
use Illuminate\Support\Facades\Facade;

/**
 * Class OrderService
 * @package Hanson\LaravelAdminWechat\Facades
 *
 * @method static WechatOrder create(array $data)
 * @method static array unify(string $mchId, string $tradeType, array $data)
 * @method static array jsConfig(string $mchId, string $tradeType, array $data)
 */
class OrderService extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Hanson\LaravelAdminWechat\Services\OrderService::class;
    }
}
