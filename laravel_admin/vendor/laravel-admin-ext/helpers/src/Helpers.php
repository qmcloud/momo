<?php

namespace Encore\Admin\Helpers;

use Encore\Admin\Admin;
use Encore\Admin\Auth\Database\Menu;
use Encore\Admin\Extension;

class Helpers extends Extension
{
    /**
     * Bootstrap this package.
     *
     * @return void
     */
    public static function boot()
    {
        static::registerRoutes();

        Admin::extend('helpers', __CLASS__);
    }

    /**
     * Register routes for laravel-admin.
     *
     * @return void
     */
    public static function registerRoutes()
    {
        parent::routes(function ($router) {
            /* @var \Illuminate\Routing\Router $router */
            $router->get('helpers/terminal/database', 'Encore\Admin\Helpers\Controllers\TerminalController@database');
            $router->post('helpers/terminal/database', 'Encore\Admin\Helpers\Controllers\TerminalController@runDatabase');
            $router->get('helpers/terminal/artisan', 'Encore\Admin\Helpers\Controllers\TerminalController@artisan');
            $router->post('helpers/terminal/artisan', 'Encore\Admin\Helpers\Controllers\TerminalController@runArtisan');
            $router->get('helpers/scaffold', 'Encore\Admin\Helpers\Controllers\ScaffoldController@index');
            $router->post('helpers/scaffold', 'Encore\Admin\Helpers\Controllers\ScaffoldController@store');
            $router->get('helpers/routes', 'Encore\Admin\Helpers\Controllers\RouteController@index');
        });
    }

    public static function import()
    {
        $lastOrder = Menu::max('order');

        $root = [
            'parent_id' => 0,
            'order'     => $lastOrder++,
            'title'     => 'Helpers',
            'icon'      => 'fa-gears',
            'uri'       => '',
        ];

        $root = Menu::create($root);

        $menus = [
            [
                'title'     => 'Scaffold',
                'icon'      => 'fa-keyboard-o',
                'uri'       => 'helpers/scaffold',
            ],
            [
                'title'     => 'Database terminal',
                'icon'      => 'fa-database',
                'uri'       => 'helpers/terminal/database',
            ],
            [
                'title'     => 'Laravel artisan',
                'icon'      => 'fa-terminal',
                'uri'       => 'helpers/terminal/artisan',
            ],
            [
                'title'     => 'Routes',
                'icon'      => 'fa-list-alt',
                'uri'       => 'helpers/routes',
            ],
        ];

        foreach ($menus as $menu) {
            $menu['parent_id'] = $root->id;
            $menu['order'] = $lastOrder++;

            Menu::create($menu);
        }

        parent::createPermission('Admin helpers', 'ext.helpers', 'helpers/*');
    }
}
