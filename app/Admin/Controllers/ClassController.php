<?php

namespace App\Admin\Controllers;

use App\Models\Classes;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ClassController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('商品分类列表');
            $content->description('分类管理');

            $content->body($this->grid());
//            $content->body(Classes::tree());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('商品分类信息修改');
            $content->description('分类管理');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('创建商品分类');
            $content->description('分类管理');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Classes::class, function (Grid $grid) {

            $grid->id('序号')->sortable();
            $grid->class_name('分类名')
                ->label('info')
                ->sortable();

            $grid->class_pid('父分类')
                ->select(Classes::getAllClasses());

            $stateDisplayConfig = Classes::getStateDisplayConfig();

            $grid->class_state('状态')
                ->switch($stateDisplayConfig);

            $grid->created_at('创建时间');
            $grid->updated_at('修改时间');

            $grid->filter(function ($filter) {
                $filter->like('class_name', '分类名');
                $filter->in('class_pid', '父分类')
                    ->multipleSelect(Classes::getAllClasses());
                $filter->equal('class_state', '状态')
                    ->radio(Classes::getStateDisplayMap());
            });
            $grid->actions(function ($actions) {
                $actions->disableDelete();
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {

        return Admin::form(Classes::class, function (Form $form) {
            $form->display('id', '序号');

            $form->text('class_name', '分类名')
                ->rules('required|max:100')
                ->placeholder('请输入分类名');

            $form->select('class_pid', '父分类')
                ->rules('required')
                ->options(Classes::getAllClasses());

            $form->text('class_desc', '描述')
                ->placeholder('请输入分类描述');

            $form->switch('class_state', '状态')
                ->states(Classes::getStateDisplayConfig())
                ->default(Classes::STATE_ACTIVE);

            $form->display('created_at', '创建时间');
            $form->display('updated_at', '修改时间');

        });
    }
}
