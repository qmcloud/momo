<?php

return array(
    0  =>
        array(
            'name'    => 'status',
            'title'   => '开关',
            'type'    => 'radio',
            'content' =>
                array(
                    1 => '开启',
                    0 => '关闭',
                ),
            'value'   => '1',
            'rule'    => 'required',
            'msg'     => '',
            'tip'     => '',
            'ok'      => '',
            'extend'  => '',
        ),
    1  =>
        array(
            'name'    => 'type',
            'title'   => '返回类型',
            'type'    => 'select',
            'content' =>
                array(
                    'json' => 'json',
                    'html' => 'html',
                ),
            'value'   => 'json',
            'rule'    => 'required',
            'msg'     => '',
            'tip'     => '',
            'ok'      => '',
            'extend'  => '',
        ),
    2  =>
        array(
            'name'    => 'msg',
            'title'   => '错误提示',
            'type'    => 'string',
            'content' =>
                array(),
            'value'   => '禁止访问',
            'rule'    => 'required',
            'msg'     => '',
            'tip'     => '',
            'ok'      => '',
            'extend'  => '',
        ),
    59 =>
        array(
            'name'    => '__tips__',
            'title'   => '温馨提示',
            'type'    => 'other',
            'content' =>
                array(),
            'value'   => '1.使用此功能必须先开启开关<br>2.后台"常规管理=>系统配置"里面设置禁止IP，<a href="general/config" class="btn-addtabs" >点击设置</a><br>3.支持通配符，例如:192.168.*.*',
            'rule'    => '',
            'msg'     => '',
            'tip'     => '',
            'ok'      => '',
            'extend'  => '',
        ),
);
