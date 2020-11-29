<?php

namespace App\Admin\Actions\Document;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class ShareDocument extends RowAction
{
    public $name = '分享';

    public function handle(Model $model)
    {
        // $model ...

        return $this->response()->success($model->title)->refresh();
    }

    public function form(Model $model = null)
    {
        $this->text('title')->rules('min:10')->default($model->title);

        $this->textarea('desc');
    }
}