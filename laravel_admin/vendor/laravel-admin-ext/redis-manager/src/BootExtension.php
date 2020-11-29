<?php

namespace Encore\Admin\RedisManager;

use Encore\Admin\Facades\Admin;

trait BootExtension
{
    public static function boot()
    {
        static::registerRoutes();

        Admin::extend('redis-manager', __CLASS__);
    }

    /**
     * Register routes for laravel-admin.
     *
     * @return void
     */
    protected static function registerRoutes()
    {
        parent::routes(function ($router) {
            /* @var \Illuminate\Routing\Router $router */
            $router->get('redis', 'Encore\Admin\RedisManager\RedisController@index')->name('redis-index');
            $router->delete('redis/key', 'Encore\Admin\RedisManager\RedisController@destroy')->name('redis-key-delete');
            $router->get('redis/fetch', 'Encore\Admin\RedisManager\RedisController@fetch')->name('redis-fetch-key');
            $router->get('redis/create', 'Encore\Admin\RedisManager\RedisController@create')->name('redis-create-key');
            $router->post('redis/store', 'Encore\Admin\RedisManager\RedisController@store')->name('redis-store-key');
            $router->get('redis/edit', 'Encore\Admin\RedisManager\RedisController@edit')->name('redis-edit-key');
            $router->put('redis/key', 'Encore\Admin\RedisManager\RedisController@update')->name('redis-update-key');
            $router->delete('redis/item', 'Encore\Admin\RedisManager\RedisController@remove')->name('redis-remove-item');

            $router->get('redis/console', 'Encore\Admin\RedisManager\RedisController@console')->name('redis-console');
            $router->post('redis/console', 'Encore\Admin\RedisManager\RedisController@execute')->name('redis-execute');
        });
    }

    /**
     * {@inheritdoc}
     */
    public static function import()
    {
        parent::createMenu('Redis manager', 'redis', 'fa-database');

        parent::createPermission('Redis Manager', 'ext.redis-manager', 'redis*');
    }
}
