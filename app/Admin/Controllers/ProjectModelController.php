<?php

namespace App\Admin\Controllers;

use App\Models\ProjectModel;
use App\Logics\ProjectControl;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ProjectModelController extends Controller
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

            $content->header('功能模块管理');
            $content->description('功能模块管理列表');

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

            $content->header('功能模块修改');
            $content->description('功能模块修改');

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

            $content->header('功能模块创建');
            $content->description('功能模块创建');

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
        return Admin::grid(ProjectModel::class, function (Grid $grid) {

            $grid->id('序号')->sortable();

            $grid->type_id('所属项目类型id');
            $grid->functype_id('所属功能分类id');
            $grid->model_name('模块名');
            $grid->model_desc('模块描述');
            $grid->created_at('创建时间');
            $grid->updated_at('更新时间');

            $grid->filter(function ($filter) {
//                $filter->equal('functype_id', '所属功能分类id')
//                    ->select(Classes::getAllClasses(true));
                $filter->like('model_name', '模块名');
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
        return Admin::form(ProjectModel::class, function (Form $form) {

            $form->display('id', '序号');
            $form->select('type_id', '所属项目类型')
                ->rules("required|min:0")
                ->options(ProjectControl::getProjectTypes())
                ->default(-1)->load('functype_id', '/api/project_json/func-types', 'id', 'functype_name');
            $form->select('functype_id', '所属功能分类id')->options(function ($id) {});
            $form->text('model_name', '模块名')
                ->rules('required');
            $form->text('model_desc', '模块描述')
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
