<?php

namespace App\Admin\Actions\Post;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class Replicate extends RowAction
{
    public $name = '复制';

    public function handle(Model $model)
    {
        $model->replicate()->save();

        return $this->response()->success('复制成功.')->refresh();
    }

    public function dialog()
    {
        $this->confirm('确定复制？');
    }
}