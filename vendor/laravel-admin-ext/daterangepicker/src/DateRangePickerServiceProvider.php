<?php

namespace Encore\DateRangePicker;

use Encore\Admin\Admin;
use Encore\Admin\Form;
use Illuminate\Support\ServiceProvider;

class DateRangePickerServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot(DateRangePickerExtension $extension)
    {
        if (! DateRangePickerExtension::boot()) {
            return ;
        }

        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'laravel-admin-daterangepicker');
        }

        if ($this->app->runningInConsole() && $assets = $extension->assets()) {
            $this->publishes(
                [$assets => public_path('vendor/laravel-admin-ext/daterangepicker')],
                'laravel-admin-daterangepicker'
            );
        }

        Admin::booting(function () {
            Form::extend('daterangepicker', DateRangePicker::class);

            if ($alias = DateRangePickerExtension::config('alias')) {
                Form::alias('daterangepicker', $alias);
            }
        });
    }
}