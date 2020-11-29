cropper extension for laravel-admin
======

这是一个`laravel-admin`扩展，用来将`cropper`集成进`laravel-admin`的表单中

现在 支持hasMany操作了~，但是hasMany内存在图片冗余问题！这貌似是个底层问题，当然推荐大家在使用hasMany的时候自行维护图片状态。

## 截图

![](./demo.jpg)

## 安装

```bash
composer require laravel-admin-ext/cropper
```

然后使用artisan 命令发布资源
```bash
php artisan vendor:publish --provider='Encore\Cropper\CropperServiceProvider' --force
```

## 配置

在`config/admin.php`文件的`extensions`，加上属于这个扩展的一些配置
```php

    'extensions' => [

        'cropper' => [
        
            // 如果要关掉这个扩展，设置为false
            'enable' => true,
        ]
    ]
```
同时记住必须要配置基础的disks配置，`config/filesystems.php`内添加一项`disk`：
```
'disks' => [
    ... ,
    // 配置目录可以自己定义
    'admin' => [
        'driver' => 'local',
        'root' => public_path('uploads'),
        'visibility' => 'public',
        'url' => env('APP_URL').'/uploads',
    ],
],
```

## 使用

在form表单中使用它：
```php
$form->cropper('content','label');
```
默认模式是自由剪裁模式，如果需要强制剪裁尺寸，请使用（注意该尺寸就是最后得到的图片尺寸 非“比例”）
```php
$form->cropper('content','label')->cRatio($width,$height);
```
## PS （特性预读）
1、图片并不是预上传的，而是前端转base64之后填入input，服务端再转回图片保存的

2、图片格式是默认原格式保存的。就是说，如果原图是透明底色的png图片，保存之后仍旧是透明底色的png图片，并不会损失（前端logo神器）

3、该扩展是可多次调用的。在同一个表单内能调动多次，不会相互干扰。

4、扩展继承了laravel-admin 的ImageField类 和File类。 
所以你不必去纠结图片的修改 和删除问题。他们都是自动操作的。 
当然，因为继承了ImageField类，所以也能使用 `intervention/image` 的各种(crop,fit,insert)方法
（前提是你已经`composer require intervention/image`）

5、现在终于支持hasMany了！！！！同时修复了之前的各种逻辑bug，并且支持本地化翻译，翻译文件发布后位于`resources\lang\zh-CN\admin_cropper.php`，目前支持了中英两种语言，可以自己选择增加别的语言支持。

6、比较糟糕的问题，因为admin框架底层的改动，现在删除条目不会自动删除图片了（代码没读完，和插件本身应该没关系，因为删除条目的时候完全不会调用插件代码，这我也没办法了）

7、未来的更新，将会替换使用框架内提供的sweetalert2 来替换目前 layer以减少前端负担。

License
------------
Licensed under [The MIT License (MIT)](LICENSE).