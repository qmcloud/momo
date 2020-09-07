<?php

namespace AdminNewDev\Adminlte;

use Encore\Admin\Admin;
use Illuminate\Support\ServiceProvider;

class AdminlteServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot(Adminlte $extension)
    {
        if (! Adminlte::boot()) {
            return ;
        }

        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'adminlte');
            view()->replaceNamespace('admin', $views);
        }

        if ($this->app->runningInConsole() && $assets = $extension->assets()) {
            $this->publishes(
                [$assets => public_path('vendor/wenxuanjun/laravel-adminlte3')],
                'adminlte'
            );
        }

        $this->app->booted(function () {

            Admin::baseCss([
                'vendor/wenxuanjun/laravel-adminlte3/plugins/font-awesome/css/font-awesome.min.css',
                'vendor/wenxuanjun/laravel-adminlte3/plugins/glyphicons/css/glyphicons.css',
                'vendor/wenxuanjun/laravel-adminlte3/dist/css/adminlte.min.css',
                'vendor/laravel-admin/nprogress/nprogress.css',
                'vendor/laravel-admin/sweetalert2/dist/sweetalert2.css',
                'vendor/laravel-admin/nestable/nestable.css',
                'vendor/laravel-admin/toastr/build/toastr.min.css',
                'vendor/laravel-admin/bootstrap3-editable/css/bootstrap-editable.css',
                'vendor/laravel-admin/google-fonts/fonts.css',
                'vendor/wenxuanjun/laravel-adminlte3/dist/css/app.css'
            ]);

            Admin::baseJs([
                'vendor/wenxuanjun/laravel-adminlte3/plugins/bootstrap/js/bootstrap.bundle.min.js',
                'vendor/wenxuanjun/laravel-adminlte3/plugins/slimScroll/jquery.slimscroll.min.js',
                'vendor/wenxuanjun/laravel-adminlte3/plugins/fastclick/fastclick.js',
                'vendor/wenxuanjun/laravel-adminlte3/dist/js/adminlte.min.js',
                'vendor/laravel-admin/jquery-pjax/jquery.pjax.js',
                'vendor/laravel-admin/nprogress/nprogress.js',
                'vendor/laravel-admin/nestable/jquery.nestable.js',
                'vendor/laravel-admin/toastr/build/toastr.min.js',
                'vendor/laravel-admin/sweetalert2/dist/sweetalert2.min.js',
                'vendor/laravel-admin/bootstrap3-editable/js/bootstrap-editable.min.js',
                'vendor/wenxuanjun/laravel-adminlte3/dist/js/app.js',
                'vendor/wenxuanjun/laravel-adminlte3/dist/js/demo.js',
            ]);
        });
    }
}
