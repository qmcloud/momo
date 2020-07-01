<?php

namespace App\Admin\Controllers;

use App\Models\Carousel;
use App\Models\Good;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class CarouselController extends Controller
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

            $content->header('轮播图列表');
            $content->description('轮播图管理');

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

            $content->header('轮播图修改');
            $content->description('轮播图管理');

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

            $content->header('轮播图创建');
            $content->description('轮播图管理');

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
        return Admin::grid(Carousel::class, function (Grid $grid) {

            $grid->id('序号')->sortable();

            $grid->carousel_title('标题');
            $grid->carousel_img('图片')
                ->image('', 75, 75);
            $grid->booth_type('展位')
                ->select(Carousel::getBoothTypeDisplayMap());
            $grid->goods_id('商品序号');
            $grid->carousel_info('显示内容');
            $grid->state('状态')
                ->switch(Carousel::getStateDisplayConfig());

            $grid->created_at('创建时间');
            $grid->updated_at('更改时间');

            $grid->filter(function ($filter) {
                $filter->like('carousel_title', '标题');
                $filter->equal('booth_type', '展位')
                    ->select(Carousel::getBoothTypeDisplayMap());
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
        return Admin::form(Carousel::class, function (Form $form) {

            $form->display('id', '序号');

            $form->text('carousel_title', '标题')
                ->rules('required|max:100');
            $form->image('carousel_img', '图片')
                ->rules('required')
                ->uniqueName();
            $form->select('booth_type', '展位')
                ->rules('required')
                ->options(Carousel::getBoothTypeDisplayMap())
                ->default(Carousel::BOOTH_TYPE_HOME);
//            $form->select('goods_id', '商品序号')
//                ->options(Good::getAllGoodsID())
//                ->rules('required');
            $form->text('carousel_info', '显示内容')
                ->rules('required|max:100');

            $stateDisplayConfig = Carousel::getStateDisplayConfig();
            $form->switch('state', '状态')
                ->states($stateDisplayConfig)
                ->default(Carousel::STATE_NORMAL);
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '更新时间');
        });
    }
}
