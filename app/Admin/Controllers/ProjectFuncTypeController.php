<?php

namespace App\Admin\Controllers;

use App\Logics\ProjectControl;
use App\Models\ProjectFunctype;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ProjectFuncTypeController extends Controller
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

            $content->header('项目功能分类');
            $content->description('项目功能分类管理');

            $content->body($this->grid());
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

            $content->header('项目功能分类');
            $content->description('项目功能分类修改');

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

            $content->header('项目功能分类');
            $content->description('项目功能分类创建');

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
        return Admin::grid(ProjectFunctype::class, function (Grid $grid) {

            $grid->id('序号')->sortable();
            $grid->type_id('所属项目类型id')->select(ProjectControl::getProjectTypes());
            $grid->functype_name('功能分类名');
            $grid->functype_desc('功能分类描述');
            $grid->sort('排序');
            $grid->status('状态')
                ->switch([
                    'on'  => ['value' => 1, 'text' => '启用', 'color' => 'success'],
                    'off' => ['value' => 0, 'text' => '禁用', 'color' => 'danger'],
                ]);
            $grid->created_at('创建时间');
            $grid->updated_at('更新时间');

            $grid->filter(function ($filter) {
                $filter->equal('type_id', '所属项目类型id')
                    ->select(ProjectControl::getProjectTypes(0));
                $filter->like('functype_name', '功能分类名');
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
        return Admin::form(ProjectFunctype::class, function (Form $form) {

            $form->display('id', '序号');
            $form->select('type_id', '所属项目类型')
                ->rules("required|numeric|min:0")
                ->options(ProjectControl::getProjectTypes())->default(-1);
            $form->text('functype_name', '功能分类名')
                ->rules('required');
            $form->text('functype_desc', '功能分类描述')
                ->rules('required|max:300')
                ->default('');
            $form->number('sort','排序')
                ->default(255);
            $form->switch('status', '是否启用')
                ->states([
                    'on'  => ['value' => 1, 'text' => '启用', 'color' => 'success'],
                    'off' => ['value' => 0, 'text' => '禁用', 'color' => 'danger'],
                ])->default(1);
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '更新时间');
        });
    }
}
