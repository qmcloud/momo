<?php

namespace Hanson\LaravelAdminWechat\Models;

use Illuminate\Database\Eloquent\Model;

class WechatMerchant extends Model
{
    protected $guarded = [];

    protected $appends = ['type_readable'];

    public function getTypeReadableAttribute()
    {
        return [1 => '普通商户号', 2 => '服务商'][$this->attributes['type']];
    }
}
