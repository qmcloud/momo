<?php

namespace Encore\Cropper;

use Encore\Admin\Admin;
use Encore\Admin\Form;
use Illuminate\Support\ServiceProvider;

class CropperServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot(Cropper $extension)
    {
        if (! Cropper::boot()) {
            return ;
        }

        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'laravel-admin-cropper');
        }

        if ($this->app->runningInConsole() && $assets = $extension->assets()) {
            $this->publishes(
                [$assets => public_path('vendor/laravel-admin-ext/cropper')],
                'laravel-admin-cropper'
            );
            $this->publishes([__DIR__.'/../resources/lang' => resource_path('lang')], 'laravel-admin-cropper-lang');
        }

        Admin::booting(function () {
            Form::extend('cropper', Crop::class);
        });
    }
}