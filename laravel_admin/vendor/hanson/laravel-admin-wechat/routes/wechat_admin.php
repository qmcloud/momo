<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => config('admin.route.prefix').'/wechat',
    'namespace' => 'Hanson\\LaravelAdminWechat\\Http\\Controllers\\Admin',
    'middleware' => config('admin.route.middleware'),
], function () {
    Route::resources([
        'configs' => 'ConfigController',
    ]);

    // 公众号操作
    Route::group(['prefix' => 'official-account', 'namespace' => 'OfficialAccount'], function () {
        Route::resource('cards', 'CardController');
        Route::resource('users', 'UserController', ['as' => 'official-account']);
        Route::get('menu', 'MenuController@index')->name('admin.wechat.menu');
        Route::post('menu', 'MenuController@store')->name('admin.wechat.menu.update');

    });

    // 小程序操作
    Route::group(['prefix' => 'mini-program', 'namespace' => 'MiniProgram'], function () {
        Route::resource('users', 'UserController', ['as' => 'mini-program']);
    });

    // 支付操作
    Route::group(['prefix' => 'payment', 'namespace' => 'Payment'], function () {
        Route::resources([
            'merchants' => 'MerchantController',
        ]);
    });
});
