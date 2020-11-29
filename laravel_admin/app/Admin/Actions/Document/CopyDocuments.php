<?php

namespace App\Admin\Actions\Document;

use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;

class CopyDocuments extends BatchAction
{
    public $name = '复制文档';

    public function handle(Collection $collection)
    {
        foreach ($collection as $model) {
            $model->replicate()->save();
        }

        return $this->response()->success('复制成功')->refresh();
    }

    public function dialog()
    {
        $this->confirm('确定复制？');
    }
}