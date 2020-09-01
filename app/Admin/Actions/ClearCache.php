<?php

namespace App\Admin\Actions;

use Encore\Admin\Actions\Action;
use Illuminate\Http\Request;

class ClearCache extends Action
{
    protected $selector = '.clear-cache';

    public function handle(Request $request)
    {
        return $this->response()->success('清理完成')->refresh();
    }

    public function dialog()
    {
        $this->confirm('确认清除缓存');
    }

    public function html()
    {
        return <<<HTML
<li>
    <a class="clear-cache" href="#">
      <i class="fa fa-trash"></i>
      <span>清理缓存</span>
    </a>
</li>
HTML;
    }
}