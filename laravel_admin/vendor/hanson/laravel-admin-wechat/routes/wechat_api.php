<?php

/**
 * 小程序相关的接口
 */

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'        => 'api/wechat/mini',
    'namespace'     => 'Hanson\\LaravelAdminWechat\\Http\\Controllers\\Api\\Mini',
], function () {
    Route::post('login', 'AuthController@login');

    Route::middleware('auth:mini')->group(function () {
        Route::post('check-token', 'AuthController@checkToken');
        Route::post('decrypt-user-info', 'AuthController@decryptUserInfo');
        Route::post('decrypt-mobile', 'AuthController@decryptMobile');
    });
});

/**
 * 支付相关的接口
 */
Route::group([
    'prefix'        => 'api/wechat/payment',
    'namespace'     => 'Hanson\\LaravelAdminWechat\\Http\\Controllers\\Api\\Payment',
], function () {
    Route::post('paid-notify', 'OrderController@paidNotify');
});
