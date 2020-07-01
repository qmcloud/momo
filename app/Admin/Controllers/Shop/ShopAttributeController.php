<?php

namespace App\Admin\Controllers\Shop;

use App\Models\ShopAttribute;
use App\Models\ShopAttributeCategory;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ShopAttributeController extends Controller
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

            $content->header('属性条目列表');
            $content->description('属性条目管理');

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

            $content->header('属性条目修改');
            $content->description('属性条目管理');

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

            $content->header('新增属性条目');
            $content->description('属性条目管理');

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
        return Admin::grid(ShopAttribute::class, function (Grid $grid) {
            $grid->model()->orderBy('sort_order', 'asc');
            $grid->id('序号')->sortable();
            $grid->name('属性名称');
            $grid->attribute_category_id('属性分类')->select(ShopAttributeCategory::pluck('name','id'));
            $grid->disableExport();// 禁用导出数据按钮
            $grid->filter(function ($filter) {
                $filter->like('name', '属性名称');
                $filter->equal('attribute_category_id','属性分类')->select(ShopAttributeCategory::pluck('name','id'));
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
        return Admin::form(ShopAttribute::class, function (Form $form) {

            $form->display('id', '序号');
            $form->text('name', '属性名称')
                ->rules('required');
            $form->select('attribute_category_id', '属性分类')->options(ShopAttributeCategory::pluck('name','id'));
            $form->number('sort_order','排序')
                ->default(255);
        });
    }

}
