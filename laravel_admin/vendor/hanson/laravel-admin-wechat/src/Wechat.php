<?php


namespace Hanson\LaravelAdminWechat;



use Encore\Admin\Extension;

class Wechat extends Extension
{
    public $name = 'wechat';

    public $views = __DIR__.'/../resources/views';

    public $assets = __DIR__.'/../resources/assets';
}
