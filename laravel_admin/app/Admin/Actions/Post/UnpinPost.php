<?php

namespace App\Admin\Actions\Post;

class UnpinPost extends PinPost
{
    public $name = '取消置顶';

    protected $success = '取消置顶成功';

    protected $confirm = '确认取消置顶？';
}