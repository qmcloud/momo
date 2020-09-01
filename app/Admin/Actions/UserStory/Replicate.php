<?php

namespace App\Admin\Actions\UserStory;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class Replicate extends RowAction
{
    public $name = '复制';

    public function handle(Model $model)
    {
        $new = $model->replicate();

        $new->parent_id = $model->parent_id;

        $new->save();

        return $this->response()->success('复制成功.')->refresh();
    }

}