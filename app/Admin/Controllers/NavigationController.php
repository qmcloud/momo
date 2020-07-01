<?php

namespace App\Admin\Controllers;

use App\Models\Classes;
use App\Models\Navigation;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class NavigationController extends Controller
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

            $content->header('导航列表');
            $content->description('导航管理');

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

            $content->header('导航修改');
            $content->description('导航管理');

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

            $content->header('导航创建');
            $content->description('导航管理');

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
        return Admin::grid(Navigation::class, function (Grid $grid) {

            $grid->id('序号')->sortable();
            $grid->icon('图标')
                ->image('', 50, 50);
            $grid->link_type('链接类型')
                ->select(Navigation::getLinkTypes());
            $grid->link_data('链接数据');
            $grid->nav_title('标题');
            $grid->desc('描述');
            $grid->remark('备注');
            $grid->sort('排序');
            $grid->if_show('是否显示')
                ->switch(Navigation::getIfShowDisplayConfig());
            $grid->created_at('创建时间');
            $grid->updated_at('更新时间');

            $grid->filter(function ($filter) {
                $filter->equal('link_type', '链接类型')
                    ->select(Navigation::getLinkTypes());
                $filter->like('nav_title', '标题');
                $filter->equal('if_show', '是否显示')
                    ->radio(Navigation::getIfShowDisplayMap());
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
        return Admin::form(Navigation::class, function (Form $form) {

            $form->display('id', '序号');

            $form->image('icon', '图标')
                ->rules('required')->uniqueName();
            $form->select('link_type', '链接类型')
                ->rules("required")
                ->options(Navigation::getLinkTypes())
                ->default('special');
            $form->text('link_data', '链接数据')
                ->rules('required')
                ->rules('nullable|max:60');
            $form->text('nav_title', '标题')
                ->rules('required');
            $form->text('desc', '描述')
                ->rules('required|max:300')
                ->default('');
            $form->text('remark', '备注')
                ->rules('required|max:300')
                ->default('');
            $form->number('sort','排序')
                ->default(255);
            $form->switch('if_show', '是否显示')
                ->states(Navigation::getIfShowDisplayConfig())
                ->default(Navigation::IFSHOW_YES);

            $form->display('created_at', '创建时间');
            $form->display('updated_at', '更新时间');
        });
    }
}
