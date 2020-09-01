<?php

namespace App\Admin\Actions\Post;

use Encore\Admin\Actions\RowAction;
use App\Models\Post;

class PinPost extends RowAction
{
    public $name = '置顶';

    protected $success = '置顶成功';

    protected $confirm = '确认置顶文章？';

    public function handle(Post $post)
    {
        $post->togglePin();

        return $this->response()->toastr()->success($this->success)->refresh();
    }

    public function dialog()
    {
        $this->confirm($this->confirm);
    }
}