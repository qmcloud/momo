<?php

namespace App\Admin\Controllers;

use App\Models\ProjectFuncdot;
use App\Logics\ProjectControl;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ProjectFuncDotController extends Controller
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

            $content->header('功能点管理');
            $content->description('功能点管理列表');

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

            $content->header('功能点修改');
            $content->description('功能点修改');

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

            $content->header('功能点创建');
            $content->description('功能点创建');

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
        return Admin::grid(ProjectFuncdot::class, function (Grid $grid) {

            $grid->id('序号')->sortable();

            $grid->type_id('所属项目类型id');
            $grid->functype_id('所属功能分类id');
            $grid->column('FunctypeFid.functype_name','所属功能分类');
            $grid->column('getModels.model_name','所属功能模块');
            $grid->funcdot_name('功能点名');
            $grid->funcdot_desc('功能点描述');
            $grid->bottom_time('最低周期(h)');
            $grid->time('周期(h)');
            $grid->discount_price('该功能的折扣价格');
            $grid->price('市场价格');
            $grid->sort('排序');
            $grid->status('状态')
                ->switch([
                    'on'  => ['value' => 1, 'text' => '启用', 'color' => 'success'],
                    'off' => ['value' => 0, 'text' => '禁用', 'color' => 'danger'],
                ]);
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
        return Admin::form(ProjectFuncdot::class, function (Form $form) {

            $form->display('id', '序号');
            $form->select('type_id', '所属项目类型')
                ->rules("required|min:0")
                ->options(ProjectControl::getProjectTypes())
                ->default(-1)->load('functype_id', '/api/project_json/func-types', 'id', 'functype_name');
            $form->select('functype_id', '所属功能分类id')->options(function ($id) {})
                ->load('model_id', '/api/project_json/models', 'id', 'model_name');
            $form->select('model_id', '所属功能模块id')->options(function ($id) {});

            $form->text('funcdot_name', '功能点名')
                ->rules('required');
            $form->text('funcdot_desc', '功能点描述')
                ->rules('required|max:300')
                ->default('');

            $form->number('bottom_time','最低周期(h)')
                ->default(4);
            $form->number('time','周期(h)')
                ->default(6);
            $form->currency('discount_price','该功能的折扣价格');
            $form->currency('price','市场价格');

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
