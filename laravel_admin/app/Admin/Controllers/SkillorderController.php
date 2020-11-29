<?php

namespace App\Admin\Controllers;

use App\Models\Skillorder;
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

class SkillorderController extends AdminController
{

    protected $title = '技能订单';
    protected $optionArr=[0=>"待接单",1=>"已接单",3=>"已到达",4=>"可开始",5=>"进行中",6=>"完成",7=>"仲裁",8=>"超时",9=>"拒绝",10=>"取消"];
    protected function detail($id)
    {
        $show = new Show(Skillorder::findOrFail($id));

        $show->panel()
            ->style('danger')
            ->title('post基本信息')
            ->tools(function ($tools) {
                $tools->disableEdit();
            });;


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
            ->row(Admin::grid(Skillorder::class, function (Grid $grid) use ($id) {

                $grid->id();
                $grid->uid();
                $grid->uuid();
                $grid->order_sn();

                $grid->order_type()->display(function ($status) {
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
        $grid = new Grid(new Skillorder);

        //$grid->quickSearch();
        //$grid->disableExport();//去掉导出
        $grid->disableCreation();//去掉新增


        $grid->column('id', 'ID')->sortable();

        $grid->column('uid', 'UID')->sortable();

        $grid->column('uuid', 'UUID')->sortable();

        $grid->column('order_sn', 'SN')->sortable();

        $grid->column('channel',"认证频道");

        $grid->column('order_amount',"金额");

        $grid->column('content',"备注");

        $optionArr=$this->optionArr;
        // 第六列显示released字段，通过display($callback)方法来格式化显示输出
        $grid->column('order_type',"订单状态")->display(function ($status)use($optionArr){
            return empty($optionArr[$status])?'':$optionArr[$status];
        });

        // 下面为三个时间字段的列显示
        $grid->column('created_at', '提交时间');

        $grid->column('updated_at', '修改时间');


        // filter($callback)方法用来设置表格的简单搜索框
        $grid->filter(function ($filter) {
            $filter->like('uid','UID');
            $filter->like('uuid','UUID');
            $filter->like('order_sn','SN');
            $filter->like('channel',"认证频道");
            $filter->equal('order_type',"订单状态")->select($this->optionArr);
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
        $form = new Form(new Skillorder);

        $form->text('uid', 'UID')->help('用户的uid');

        $form->text('uuid', 'UUID')->help('用户的uid');

        $form->text('order_amount',"金额");

        $form->text('channel',"认证分类");

        $form->text('content',"备注");

        $form->select('order_type',"订单状态")->options($this->optionArr);

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
