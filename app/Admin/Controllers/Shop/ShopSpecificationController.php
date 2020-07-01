<?php

namespace App\Admin\Controllers\Shop;

use App\Models\ShopSpecification;
use App\Models\ShopCategory;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ShopSpecificationController extends Controller
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

            $content->header('商品规格列表');
            $content->description('商品规格管理');

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

            $content->header('商品规格修改');
            $content->description('商品规格管理');

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

            $content->header('新增商品规格');
            $content->description('商品规格管理');

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
        return Admin::grid(ShopSpecification::class, function (Grid $grid) {
            $grid->model()->orderBy('sort_order', 'asc');
            $grid->id('序号')->sortable();
            $grid->category_id('商品分类')
                ->select(ShopCategory::getAllClasses(true));
            $grid->name('规格');

            $grid->disableExport();// 禁用导出数据按钮
            $grid->filter(function ($filter) {
                $filter->like('name', '规格');
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
        return Admin::form(ShopSpecification::class, function (Form $form) {

            $form->display('id', '序号');
            $form->select('category_id', '商品分类')
                ->rules('required')
                ->options(ShopCategory::selectOptions(true));
            $form->text('name', '规格名称')
                ->rules('required');
            $form->radio('search_index', '是否限购')
                ->options(ShopSpecification::getSearchDispayMap())
                ->default(ShopSpecification::STATE_ON_SEARCH);
            $form->number('sort_order','排序')
                ->default(255);

            $form->hasMany('spec_items', '添加规格条目', function (Form\NestedForm $form) {
                $form->text('item', '规格项');
            });

        });
    }

}
