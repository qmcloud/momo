<?php

namespace App\Admin\Actions\Document;

use Encore\Admin\Actions\Action;
use Illuminate\Http\Request;

class ImportDocument extends Action
{
    public $name = '导入数据';

    protected $selector = '.import-document';

    public function handle(Request $request)
    {
        $name = $request->file('document')->getClientOriginalName();

        return $this->response()->success("上传成功 : {$name}")->refresh();
    }

    public function form()
    {
        $this->file('document', '选择文件');
    }

    public function html()
    {
        return <<<HTML
        <a class="btn btn-sm btn-default import-document">导入</a>
HTML;
    }
}