<?php

namespace App\Admin\Actions\UserStory;

use App\Models\User;
use App\Models\UserStory;
use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CreateChild extends RowAction
{
    public $name = '创建子需求';

    protected static $owners;

    public function handle(Model $model, Request $request)
    {
        UserStory::create($request->all(), $model);

        return $this->response()->success('创建成功')->refresh();
    }

    public function form(UserStory $story)
    {
        $this->text('title', __('Title'));
        $this->radio('priority', __('Priority'))->stacked()->options(UserStory::$priorities);
        $this->radio('status', __('Status'))->options(UserStory::$status);
        $this->select('owner_id', __('Owner id'))->options(static::getOwners());
        $this->datetime('begin_at', __('Begin at'))->default(date('Y-m-d H:i:s'));
        $this->datetime('end_at', __('End at'))->default(date('Y-m-d H:i:s'));
    }

    public static function getOwners()
    {
        if (!static::$owners) {
            static::$owners = User::all()->pluck('name', 'id');
        }

        return static::$owners;
    }
}