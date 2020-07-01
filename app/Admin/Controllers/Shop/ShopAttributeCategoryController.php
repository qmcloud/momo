<?php

namespace App\Admin\Controllers\Shop;

use App\Models\ShopAttributeCategory;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ShopAttributeCategoryController extends Controller
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

            $content->header('属性分类列表');
            $content->description('属性分类管理');

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

            $content->header('属性分类修改');
            $content->description('属性分类管理');

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

            $content->header('新增属性分类');
            $content->description('属性分类管理');

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
        return Admin::grid(ShopAttributeCategory::class, function (Grid $grid) {
            $grid->id('序号')->sortable();
            $grid->name('属性分类名称');
            $grid->enabled('状态')
                ->select(ShopAttributeCategory::getEnabledDispayMap());
            $grid->disableExport();// 禁用导出数据按钮
            $grid->filter(function ($filter) {
                $filter->like('name', '属性分类名称');
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
        return Admin::form(ShopAttributeCategory::class, function (Form $form) {

            $form->display('id', '序号');
            $form->text('name', '属性分类名称')
                ->rules('required');
            $form->radio('enabled', '是否推荐')
                ->options(ShopAttributeCategory::getEnabledDispayMap())
                ->default(ShopAttributeCategory::STATE_ENABLED);
        });
    }

}
