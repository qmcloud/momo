<?php

namespace App\Admin\Actions\Document;

use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;

class ShareDocuments extends BatchAction
{
    public $name = '分享文档';

    public function handle(Collection $collection)
    {
        foreach ($collection as $model) {
            // ...
        }

        return $this->response()->success('分享成功')->refresh();
    }

    public function dialog()
    {
        $this->confirm('确定分享文档？');
    }
}