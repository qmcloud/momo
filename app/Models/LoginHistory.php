<?php

namespace App\Models;

use Encore\Admin\Traits\AdminBuilder;
use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    use AdminBuilder;

    protected $table = 'admin_login_history';

    protected $fillable = ['country', 'province', 'city', 'district', 'isp', 'ip',];

    public static function log($ip)
    {
        $json = file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=' . $ip);

        $info = json_decode($json, true);

        if (!is_array($info)) {
            return;
        }

        $log = new static();

        $log->country   = array_get($info, 'country');
        $log->province  = array_get($info, 'province');
        $log->city      = array_get($info, 'city');
        $log->district  = array_get($info, 'district');
        $log->isp       = array_get($info, 'isp');
        $log->ip        = $ip;

        return $log->save();
    }
}
