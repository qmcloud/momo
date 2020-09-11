<?php


namespace Hanson\LaravelAdminWechat\Models;


use Illuminate\Database\Eloquent\Model;

class WechatConfig extends Model
{
    protected $guarded = [];

    protected $appends = ['type_readable'];

    public function getTypeReadableAttribute()
    {
        return [1 => '公众号', 2 => '小程序'][$this->attributes['type']];
    }
}
