<?php

namespace Encore\ChunkFileUpload;

use Encore\Admin\Admin;
use Illuminate\Support\ServiceProvider;

class ChunkFileUploadServiceProvider extends ServiceProvider {
    /**
     * {@inheritdoc}
     */
    public function boot ( ChunkFileUpload $extension ) {
        if ( ! ChunkFileUpload::boot () ) {
            return;
        }

        if ( $views = $extension->views () ) {
            $this->loadViewsFrom ( $views , 'chunk-file-upload' );
        }
        $source = realpath ( __DIR__ . '/chunk_file_upload.php' );

        if ( $this->app->runningInConsole () && $assets = $extension->assets () ) {
            $this->publishes (
                [
                    $assets => public_path ( 'vendor/catlane/chunk-file-upload' ) ,
                    $source => config_path ( 'chunk_file_upload.php' )
                ] ,
                'chunk-file-upload'
            );
            $this->mergeConfigFrom ( $source , 'chunk_file_upload' );
        }


        $this->app->booted ( function () {
            ChunkFileUpload::routes ( __DIR__ . '/../routes/web.php' );
        } );
        Admin::booting ( function () {
            Admin::js ( 'vendor/catlane/chunk-file-upload/js/main.js' );
            Admin::js ( 'vendor/catlane/chunk-file-upload/js/webuploader.js' );

            Admin::css ( 'vendor/catlane/chunk-file-upload/css/style.css' );
            Admin::css ( 'vendor/catlane/chunk-file-upload/css/webuploader.css' );
        } );
    }

}