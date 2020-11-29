<?php

namespace App\Admin\Actions\Post;

use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;

class BatchReplicate extends BatchAction
{
    public $name = '批量复制';

    public function handle(Collection $collection)
    {
        foreach ($collection as $model) {
            $model->replicate()->save();
        }

        return $this->response()->success('复制成功.')->refresh();
    }
}