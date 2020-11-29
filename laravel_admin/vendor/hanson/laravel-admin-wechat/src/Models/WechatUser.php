<?php

namespace Hanson\LaravelAdminWechat\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class WechatUser extends Authenticatable implements JWTSubject
{
    protected $guarded = [];

    protected $appends = ['gender_readable'];

    public function getGenderReadableAttribute()
    {
        if ($this->attributes['gender'] ?? false) {
            return [0 => '未知', 1 => '男', 2 => '女'][$this->attributes['gender']];
        }
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
