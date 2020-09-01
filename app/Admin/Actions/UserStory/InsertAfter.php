<?php

namespace App\Admin\Actions\UserStory;

use App\Models\UserStory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class InsertAfter extends CreateChild
{
    public $name = '后方插入';

    public function handle(Model $model, Request $request)
    {
        $node = new UserStory($request->all());

        $node->insertAfterNode($model);

        return $this->response()->success('插入完成.')->refresh();
    }
}