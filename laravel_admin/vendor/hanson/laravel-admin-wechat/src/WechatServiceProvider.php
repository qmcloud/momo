<?php


namespace Hanson\LaravelAdminWechat;


use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Hanson\LaravelAdminWechat\Console\Commands\CreateMenu;
use Hanson\LaravelAdminWechat\Console\Commands\Install;
use Hanson\LaravelAdminWechat\Fields\MenuFormField;
use Illuminate\Support\ServiceProvider;

class WechatServiceProvider extends ServiceProvider
{
    protected $commands = [
        CreateMenu::class,
        Install::class,
    ];

    public function register()
    {
        $this->commands($this->commands);
    }

    public function boot(Wechat $extension)
    {
        if (!Wechat::boot()) {
            return;
        }

        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'wechat');
        }

        if (file_exists($routes = base_path('routes/wechat_admin.php'))) {
            $this->loadRoutesFrom($routes);
        }
        if (file_exists($routes = base_path('routes/wechat_api.php'))) {
            $this->loadRoutesFrom($routes);
        }

        Admin::booting(function () {
            Form::extend('wechatMenu', MenuFormField::class);
        });

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../database' => database_path(),
                __DIR__.'/../routes' => base_path('routes'),
                $extension->assets() => public_path('vendor/laravel-admin-ext/wechat'),
            ],'laravel-admin-wechat');
        }
    }
}
