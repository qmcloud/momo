<?php

namespace App\Admin\Controllers;

use App\Models\Dynamic;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use App\Admin\Extensions\Tools\ReleasePost;
use App\Admin\Extensions\Tools\RestorePost;
use App\Admin\Extensions\Tools\ShowSelected;

class DynamicController extends AdminController
{

    protected $title = '动态列表';
    protected $optionArr=[0=>'纯文字',1=>'文字+图片',2=>'文字+视频',3=>'文字+语音',4=>'纯文字广告',5=>'文字+图片广告',6=>'文字+视频广告'];
    protected function detail($id)
    {
        $show = new Show(Dynamic::findOrFail($id));

        $show->panel()
            ->style('danger')
            ->title('post基本信息')
            ->tools(function ($tools) {
                $tools->disableEdit();
            });


        $show->id();
        $show->title();
        $show->content();
        $show->comments();
        $show->thumb();
        $show->created_at();
        $show->updated_at();


        return $show;
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->title('编辑')
            ->row($this->form()->edit($id))
            ->row(Admin::grid(Dynamic::class, function (Grid $grid) use ($id) {

                $grid->id();
                $grid->title();
                $grid->content();
                $grid->comments();
                $grid->thumb();
                $grid->type()->display(function ($status) {
                    return empty($this->optionArr[$status])?'-':$this->optionArr[$status];
                });
                $grid->created_at();
                $grid->updated_at();

            }));
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Dynamic);

        //$grid->quickSearch();
        //$grid->disableExport();//去掉导出
        $grid->disableCreation();//去掉新增


        $grid->column('id', 'ID')->sortable();

        $grid->column('title', '标题');

        $grid->column('content', '内容');

        $grid->column('thumb',"图片");

        $optionArr=$this->optionArr;
        // 第六列显示released字段，通过display($callback)方法来格式化显示输出
        $grid->column('type',"状态")->display(function ($status)use($optionArr){
            return empty($optionArr[$status])?'':$optionArr[$status];
        });

        // 下面为三个时间字段的列显示
        $grid->column('created_at', '提交时间');

        $grid->column('updated_at', '修改时间');


        // filter($callback)方法用来设置表格的简单搜索框
        $grid->filter(function ($filter) {
            $filter->like('id','ID');
            $filter->like('title','标题');
            $filter->like('thumb','图片');
            $filter->equal('type',"状态")->select($this->optionArr);
            // 设置created_at字段的范围查询
            $filter->between('created_at', '创建时间')->datetime();
        });

        return $grid;
    }

    protected function _form()
    {
        /*$form = new Form(new Skillorder);

        return $form;*/
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Dynamic);

        $form->text('id', 'ID');

        $form->text('title',"标题");

        $form->text('content',"内容");

        $form->text('thumb',"图片");

        $form->select('type',"状态")->options($this->optionArr);

        $form->datetime('created_at', '提交时间');

        $form->datetime('updated_at', '修改时间');

        /*$form->tools(function (Form\Tools $tools) {

            $tools->disableList();
            $tools->disableDelete();
            $tools->disableView();

            $tools->append('<a class="btn btn-sm btn-danger"><i class="fa fa-trash"></i>&nbsp;&nbsp;delete</a>');
        });*/

        return $form;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function users(Request $request)
    {
        $q = $request->get('q');

        return User::where('name', 'like', "%$q%")->paginate(null, ['id', 'name as text']);
    }

    public function release(Request $request)
    {
        foreach (Auth::find($request->get('ids')) as $post) {
            $post->released = $request->get('action');
            $post->save();
        }
    }

    public function restore(Request $request)
    {
        return Auth::onlyTrashed()->find($request->get('ids'))->each->restore();
    }

}
