<?php

namespace App\Admin\Controllers;

use App\Models\Classes;
use App\Models\Special;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class SpecialController extends Controller
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

            $content->header('专栏列表');
            $content->description('专栏管理');

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

            $content->header('专栏修改');
            $content->description('专栏管理');

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

            $content->header('专栏创建');
            $content->description('专栏管理');

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
        return Admin::grid(Special::class, function (Grid $grid) {

            $grid->id('序号')->sortable();
            $grid->icon('图标')
                ->image('', 50, 50);
            $grid->class_id('分类')
                ->select(Classes::getAllClasses(true));
           // $grid->link_url('链接');
            $grid->special_title('标题');
            $grid->special_desc('描述');
            $grid->remark('备注');
            $grid->sort('排序');
            $grid->if_show('是否显示')
                ->switch(Special::getIfShowDisplayConfig());
            $grid->created_at('创建时间');
            $grid->updated_at('更新时间');
            $grid->actions(function ($actions) {
                // append一个操作
                $url =admin_base_path('module').'?id='.$actions->getKey();
                $actions->append("<a href='{$url}'><i class='fa fa-cog'></i></a>");
            });
            $grid->filter(function ($filter) {
                $filter->equal('class_id', '分类')
                    ->select(Classes::getAllClasses(true));
                $filter->like('special_title', '标题');
                $filter->equal('if_show', '是否显示')
                    ->radio(Special::getIfShowDisplayMap());
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
        return Admin::form(Special::class, function (Form $form) {

            $form->display('id', '序号');

            $form->image('icon', '图标')
                ->rules('required')->uniqueName();
            $form->select('class_id', '分类')
                ->rules("required")
                ->options(Classes::getAllClasses(true))
                ->default(0);
            //$form->text('link_url', '链接')->rules('required')->rules('nullable|max:60');
            $form->text('special_title', '标题')
                ->rules('required');
            $form->text('special_desc', '描述')
                ->rules('required|max:300')
                ->default('');
            $form->text('remark', '备注')
                ->rules('required|max:300')
                ->default('');
            $form->number('sort','排序')
                ->default(255);
            $form->switch('if_show', '是否显示')
                ->states(Special::getIfShowDisplayConfig())
                ->default(Special::IFSHOW_YES);

            $form->display('created_at', '创建时间');
            $form->display('updated_at', '更新时间');
        });
    }
}
