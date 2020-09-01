<?php

namespace App\Admin\Actions;

use Encore\Admin\Actions\Action;
use Illuminate\Http\Request;

class System extends Action
{
    public $name = '系统设置';

    protected $selector = '.system';

    public function handle(Request $request)
    {
        // $request ...

        return $this->response()->success($request->get('name'))->refresh();
    }

    public function form()
    {
        $this->text('name', '网站名称');
        $this->textarea('desc', '网站介绍');
    }

    public function html()
    {
        return <<<HTML
<li>
    <a href="javascript:void(0);" class="system">
      <i class="fa fa-wrench"></i>
    </a>
</li>
HTML;
    }
}