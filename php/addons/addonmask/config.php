<?php

return [
    [
        'name' => '__tips__',
        'title' => '温馨提示',
        'type' => 'string',
        'content' => [],
        'value' => '建议仅在插件安装提示"从官网下载.."时开启,安装成功后再关闭以节省系统消耗<br>目前仅实现了valid方法,默认返回成功,如需其它方法,可自行扩展',
        'rule' => '',
        'msg' => '',
        'tip' => '',
        'ok' => '',
        'extend' => '',
    ],
    [
        'name' => 'myrewrite',
        'title' => '动态路由',
        'type' => 'array',
        'content' => [],
        'value' => [
            'addon/valid' => 'api/Addonmask/valid',
        ],
        'rule' => '',
        'msg' => '',
        'tip' => '',
        'ok' => '',
        'extend' => '',
    ],
];
