<?php

namespace Encore\Chartjs;

use Encore\Admin\Admin;
use Illuminate\Support\ServiceProvider;

class ChartjsServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot(Chartjs $extension)
    {
        if (! Chartjs::boot()) {
            return ;
        }

        if ($this->app->runningInConsole() && $assets = $extension->assets()) {
            $this->publishes(
                [$assets => public_path('vendor/laravel-admin-ext/chartjs')],
                'laravel-admin-chartjs'
            );
        }

        Admin::booting(function () {
            Admin::js('vendor/laravel-admin-ext/chartjs/Chart.bundle.min.js');
        });
    }
}