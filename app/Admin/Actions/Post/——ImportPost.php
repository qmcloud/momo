<?php

namespace App\Admin\Actions\Post;

use Encore\Admin\Actions\Action;
use Illuminate\Http\Request;

class ImportPost extends Action
{
    protected $selector = '.import-posts';

    public function handle(Request $request)
    {
        $fileName = $request->file('file')->getClientOriginalName();

        return $this->response()->topCenter()->success($fileName);
    }

    public function form()
    {
        $this->file('file', '请选择文件');
    }

    /**
     * @return string
     */
    public function html()
    {
        return "<a class='import-posts btn btn-sm btn-default'>导入</a>";
    }
}