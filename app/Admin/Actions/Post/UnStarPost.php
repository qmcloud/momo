<?php

namespace App\Admin\Actions\Post;

class UnStarPost extends StarPost
{
    protected $success = '已取消加精';

    protected $confirm = '确认取消加精文章？';

    protected $icon = 'star';
}