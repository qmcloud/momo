<?php

namespace App\Admin\Actions\Post;

use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;

class ShowSelected extends BatchAction
{
    protected $selector = '.show-selected';

    public function handle(Collection $collection)
    {
        return $this->response()->topCenter()->success($collection->keys());
    }

    /**
     * @return string
     */
    public function render()
    {
        return "<a href=\"#\" class='show-selected btn btn-sm btn-default'>已选择</a>";
    }
}