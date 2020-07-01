<?php

namespace App\Admin\Controllers\Shop;

use App\Models\ShopSpecification;
use App\Models\ShopCategory;
use App\Models\ShopSpecItem;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ShopSpecItemController extends Controller
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

            $content->header('规格条目');
            $content->description('规格条目管理');

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

            $content->header('规格条目修改');
            $content->description('规格条目管理');

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

            $content->header('新增规格条目');
            $content->description('规格条目管理');

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
        return Admin::grid(ShopSpecItem::class, function (Grid $grid) {
            $grid->id('序号')->sortable();
            $grid->spec_id('规格id')
                ->select(ShopSpecification::selectOptions(true));
            $grid->item('规格项');

            $grid->disableExport();// 禁用导出数据按钮
            $grid->filter(function ($filter) {
                $filter->like('item', '规格项');
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
        return Admin::form(ShopSpecItem::class, function (Form $form) {

            $form->display('id', '序号');
            $form->select('spec_id', '规格id')
                ->rules('required')
                ->options(ShopSpecification::selectOptions(true));
            $form->text('item', '规格条目名称')
                ->rules('required');
        });
    }

}
