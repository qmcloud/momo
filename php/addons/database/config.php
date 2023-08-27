<?php

return array(
    array(
        'name'    => 'backupDir',
        'title'   => '备份存放目录',
        'type'    => 'string',
        'content' =>
            array(),
        'value'   => '../data/',
        'rule'    => 'required',
        'msg'     => '',
        'tip'     => '备份目录,请使用相对目录',
        'ok'      => '',
        'extend'  => '',
    ),
    array(
        'name'    => 'backupIgnoreTables',
        'title'   => '备份忽略的表',
        'type'    => 'string',
        'content' =>
            array(),
        'value'   => 'fa_admin_log',
        'rule'    => '',
        'msg'     => '',
        'tip'     => '忽略备份的表,多个表以,进行分隔',
        'ok'      => '',
        'extend'  => '',
    ),
    array(
        'name'    => '__tips__',
        'title'   => '温馨提示',
        'type'    => '',
        'content' =>
            array(),
        'value'   => '请做好数据库离线备份工作，建议此插件仅用于开发阶段，项目正式上线建议卸载此插件',
        'rule'    => '',
        'msg'     => '',
        'tip'     => '',
        'ok'      => '',
        'extend'  => '',
    ),
);
