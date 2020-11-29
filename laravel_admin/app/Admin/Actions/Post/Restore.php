<?php

namespace App\Admin\Actions\Post;

use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;

class Restore extends BatchAction
{
    public $name = '恢复数据';

    public function handle(Collection $collection)
    {
        return $this->response()->success($collection->keys())->refresh();
    }

    public function dialog()
    {
        $this->confirm('确定恢复吗？');
    }
}