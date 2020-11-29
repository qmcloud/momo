<?php

namespace App\Admin\Actions\Post;

use Encore\Admin\Actions\RowAction;
use App\Models\Post;

class StarPost extends RowAction
{
    public $name = '加精';

    protected $success = '加精成功';

    protected $confirm = '确认加精文章？';

    public function handle(Post $post)
    {
        $post->toggleStar();

        return $this->response()->success($this->success)->refresh();
    }

    public function dialog()
    {
        $this->text($this->confirm);
    }
}