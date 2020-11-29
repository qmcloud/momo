<?php

/*
 * This file is part of the overtrue/laravel-filesystem-qiniu.
 * (c) overtrue <i@overtrue.me>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Overtrue\LaravelFilesystem\Qiniu;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Overtrue\Flysystem\Qiniu\Plugins\FetchFile;
use Overtrue\Flysystem\Qiniu\Plugins\FileUrl;
use Overtrue\Flysystem\Qiniu\Plugins\PrivateDownloadUrl;
use Overtrue\Flysystem\Qiniu\Plugins\RefreshFile;
use Overtrue\Flysystem\Qiniu\Plugins\UploadToken;
use Overtrue\Flysystem\Qiniu\QiniuAdapter;

class QiniuStorageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        app('filesystem')->extend('qiniu', function ($app, $config) {
            $adapter = new QiniuAdapter(
                $config['access_key'], $config['secret_key'],
                $config['bucket'], $config['domain']
            );

            $flysystem = new Filesystem($adapter);

            $flysystem->addPlugin(new FetchFile());
            $flysystem->addPlugin(new UploadToken());
            $flysystem->addPlugin(new FileUrl());
            $flysystem->addPlugin(new PrivateDownloadUrl());
            $flysystem->addPlugin(new RefreshFile());

            return $flysystem;
        });
    }

    /**
     * Register any application services.
     */
    public function register()
    {
    }
}
