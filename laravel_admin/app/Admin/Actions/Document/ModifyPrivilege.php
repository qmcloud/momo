<?php

namespace App\Admin\Actions\Document;

use Encore\Admin\Actions\BatchAction;
use App\Models\Document;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class ModifyPrivilege extends BatchAction
{
    public $name = '修改权限';

    public function handle(Collection $documents, Request $request)
    {
        foreach ($documents as $document) {
            $document->privilege = $request->privilege;
            $document->save();
        }

        return $this->response()->success('修改成功')->refresh();
    }

    public function form()
    {
        $this->radio('privilege', '权限')->options(Document::$privileges);
    }
}