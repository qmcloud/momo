<?php

namespace App\Admin\Controllers;

use App\Models\Classes;
use App\Models\Good;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class GoodController extends Controller
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

            $content->header('商品列表');
            $content->description('商品管理');

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

            $content->header('商品信息修改');
            $content->description('商品管理');

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

            $content->header('创建商品');
            $content->description('商品管理');

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
        return Admin::grid(Good::class, function (Grid $grid) {

            $grid->id('序号')->sortable();
            $grid->goods_name('商品名')->label('info')->sortable();
            $grid->goods_main_image('商品主图')->image('', 75, 75);
            $grid->class_id('分类')
                ->select(Classes::getAllClasses(true))
                ->sortable();
            $grid->goods_price('单价');
            $grid->goods_marketprice('市场价');
            $grid->goods_onsaleprice('折扣价');
            $grid->goods_salenum('销售量');
            $grid->goods_click('点击量');
            $grid->goods_carousel('轮播图片')->image('', 50, 50);
            $grid->goods_description_pictures('描述图片')->image('', 50, 50);

//            $grid->goods_storage('库存');
            $grid->goods_state('状态')
                ->select(Good::getStateDispayMap());
            $grid->sort('排序');
            $grid->created_at('创建时间');
            $grid->updated_at('更新时间');

            $grid->filter(function ($filter) {
                $filter->like('goods_name', '商品名');
                $filter->in('class_id', '分类')
                    ->multipleSelect(Classes::getAllClasses(true));
                $filter->lt('goods_price', '单价')
                    ->currency();
                $filter->lt('goods_marketprice', '市场价')
                    ->currency();
                $filter->lt('goods_onsaleprice', '折扣价')
                    ->currency();
                $filter->between('goods_salenum', '销售量');
                $filter->between('goods_click', '点击量');
//                $filter->lt('goods_storage', 'Storage');
                $filter->equal('goods_state', '状态')
                    ->radio(Good::getStateDispayMap());
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
        return Admin::form(Good::class, function (Form $form) {

            $form->display('id', '序号');

            $form->text('goods_name', '商品名')
                ->rules('required');

            $form->select('class_id', '分类')
                ->rules('required')
                ->options(Classes::getAllClasses(true));

            $form->currency('goods_price', '单价')
                ->symbol('￥');

            $form->currency('goods_marketprice', '市场价')
                ->symbol('￥');

            $form->currency('goods_onsaleprice', '折扣价')
                ->symbol('￥');

            $form->image('goods_main_image', '主图')
                ->rules('required')
                ->uniqueName() ;

            $form->multipleImage('goods_carousel', '轮播图片')
                ->uniqueName();

            $form->textarea('goods_desc', '描述');

            $form->multipleImage('goods_description_pictures', '描述图片')
                ->uniqueName();

//            $form->number('goods_storage', '库存');
            $form->radio('goods_state', '状态')
                ->options(Good::getStateDispayMap())
                ->default(Good::STATE_NORMAL);
            $form->number('sort','排序')
                ->default(255);
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '更新时间');
        });
    }
}
