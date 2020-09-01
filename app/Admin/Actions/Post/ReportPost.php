<?php

namespace App\Admin\Actions\Post;

use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class ReportPost extends BatchAction
{
    protected $selector = '.report-posts';

    public function handle(Collection $collection, Request $request)
    {
//        $collection->each->delete();

        return $this->response()->topCenter()->success($request->get('title'))->refresh();
    }

    public function form()
    {
        $options = [
            1 => 'PHP',
            2 => 'Python',
            3 => 'Ruby',
            4 => 'C++',
        ];

        $this->text('title', '标题')->rules('string|min:100');
        $this->textarea('reason', '理由');
        $this->select('type', '类型')->options($options);
        $this->multipleSelect('type_2')->options($options);
        $this->datetime('created_at', '时间');

        $this->checkbox('status')->options($options);
        $this->radio('kind')->options($options);

        $this->file('file');
        $this->image('image');
    }

    /**
     * @return string
     */
    public function html()
    {
        return "<a class='report-posts btn btn-sm btn-danger'>举报</a>";
    }
}