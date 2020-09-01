<?php

namespace App\Admin\Actions\Post;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Report extends RowAction
{
    public $name = '举报';

    public function handle(Model $model, Request $request)
    {
        // $model ...

        return $this->response()->success('Success message.')->refresh();
    }

    public function form()
    {
        $type = [
            1 => '广告',
            2 => '违法',
            3 => '钓鱼',
        ];
        $this->checkbox('type', '类型')->options($type);
        $this->textarea('reason', '原因');
    }
}