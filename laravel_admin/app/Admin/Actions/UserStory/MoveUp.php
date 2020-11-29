<?php

namespace App\Admin\Actions\UserStory;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class MoveUp extends RowAction
{
    public $name = '上移';

    public function handle(Model $model)
    {
        $model->up();

        return $this->response()->success('移动完成.')->refresh();
    }

}