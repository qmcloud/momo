该插件复用时会导致第一个无法生效，因最近工作较忙，晚点会修复1

laravel-admin extension
======


文件上传小扩展，可支持大文件`分块分片`上传到七牛云or本地，优化您上传大文件的苦恼

## 截图

![IMG](https://qn.lovyou.top/blog/2019/05/201905299427_2182.png?watermark/1/image/aHR0cHM6Ly93d3cubG92eW91LnRvcC96Yl91c2Vycy9wbHVnaW4vcWluaXV5dW4vd2F0ZXIucG5n/dissolve/80/gravity/SouthEast/dx/10/dy/10)

## 安装

```
$ composer require catlane/chunk-file-upload

$ php artisan vendor:publish --tag=chunk-file-upload

$ php artisan storage:link

```

## 注册进Laravel-admin
```
Encore\Admin\Form::extend('chunk_file', \Encore\ChunkFileUpload\ChunkFileField::class);
```

然后配置 `config/chunk_file_upload.php`:

```php

    'disks' => [
    
            'local' => [
                'driver' => 'local' ,
                'root' => storage_path ( 'app' ) ,
            ] ,
    
            'public' => [
                'driver' => 'local' ,
                'root' => storage_path ( 'app/public' ) ,
            ] ,
            'qiniu_live' => [//七牛云
                'driver' => 'qiniu' ,//如果是七牛云空间，必填qiniu
                'domains' => [
                    'default' => '****' , //你的七牛域名
                    'https' => '' , //你的HTTPS域名
                    'custom'    => '****',                //你的自定义域名
                ] ,
                'access_key' => '****' ,  //AccessKey
                'secret_key' => '*****' ,  //SecretKey
                'bucket' => '***' ,  //Bucket名字
                'url' => '*******' ,  // 填写文件访问根url
            ]
        ] ,
        'default' => [
            'disk' => 'public' ,//默认磁盘
            'extensions' => 'jpg,png,mp4' ,//后缀
            'mimeTypes' => 'image/*,video/*' ,//类型
            'fileSizeLimit' => 10737418240 ,//上传文件限制总大小，默认10G,默认单位为b
            'fileNumLimit' => '5' ,//文件上传总数量
            'saveType' => 'json', //单文件默认为字符串，多文件上传存储格式，json:['a.jpg','b.jpg']
        ]
```

## 使用

单文件上传：
```
$form->chunk_file('file', '视频');
```
## 显示

在grid中显示，单图：
```
$grid->picture ( '图片' )->image ('http://test.com:81/storage/', 300);
```
多图:
```
$grid->picture ( '图片' )->display(function ($picture) {
    return json_decode($picture, true);
})->image ('http://test.com:81/storage/', 300);
```

###可选方法

当然，以下各种方法也可以在config中直接定义

`disk`：选择存储磁盘

`extensions`：文件后缀

`mimeTypes`：文件类型

`mimeTypes`：文件类型

`fileSizeLimit`：文件上传大小

`fileNumLimit`：上传文件数量

`saveType`：多文件上传存储格式，json:['a.jpg','b.jpg']

License
------------
Licensed under [The MIT License (MIT)](LICENSE).
