<?php

namespace App\Admin\Actions;

use Encore\Admin\Actions\Action;
use Illuminate\Http\Request;

class Feedback extends Action
{
    protected $selector = '.feedback';

    public function handle(Request $request)
    {
        return $this->response()->success($request->get('title'))->topCenter();
    }

    public function form()
    {
        $this->text('title')->rules('required');
        $this->email('email');
        $this->textarea('content');
        $this->datetime('created_at');
        $this->integer('count');
    }

    public function html()
    {
        return <<<HTML
<li>
    <a class="feedback" href="javascript:void(0);">
      <i class="fa fa-question"></i>
      <span>反馈</span>
    </a>
</li>
HTML;
    }
}