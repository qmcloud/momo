<?php

namespace App\Admin\Actions\MobilePhone;

use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;

class BatchCopy extends BatchAction
{
    public $name = '复制';

    public function handle(Collection $collection)
    {
        foreach ($collection as $model) {
            $model->replicate()->save();
        }

        return $this->response()->success('Success message...')->refresh();
    }

}