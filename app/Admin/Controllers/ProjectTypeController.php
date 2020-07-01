<?php

namespace App\Admin\Controllers;

use App\Models\ProjectType;
use App\Models\ShopBrand;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ProjectTypeController extends Controller
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

            $content->header('项目类型');
            $content->description('项目类型管理');

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

            $content->header('项目类型');
            $content->description('项目类型修改');

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

            $content->header('项目类型');
            $content->description('新增项目类型');

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
        return Admin::grid(ProjectType::class, function (Grid $grid) {

            $grid->id('序号')->sortable();

            $grid->type_name('项目类型名');
            $grid->type_desc('项目类型描述')->limit(100);
            $grid->type_img('类型的图片')->image('', 50, 50);
            $grid->brand_id('品牌id')
                ->select(ShopBrand::getAllClasses(true));
            $grid->basal_price('该类项目的基础价格');
            $grid->carousel_imgs('轮播图片')->image('', 50, 50);
            $grid->created_at('创建时间');
            $grid->updated_at('更新时间');
            $grid->filter(function ($filter) {
                $filter->like('type_name', '项目类型名');
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
        return Admin::form(ProjectType::class, function (Form $form) {
            $form->display('id', '序号');
            $form->text('type_name', '项目类型名')
                ->rules('required');
            $form->text('type_desc', '项目类型描述')
                ->rules('required|max:300')
                ->default('');
            $form->image('type_img', '类型的图片')
                ->rules('required')
                ->uniqueName();
            $form->select('brand_id', '品牌id')
                ->rules('required')
                ->options(ShopBrand::getAllClasses(true));
            $form->multipleImage('carousel_imgs', '轮播图片')
                ->uniqueName();

            $form->number('salenum','下单量')
                ->default(10);

            $form->textarea('description', '详情描述');

            $form->currency('basal_price','该类项目的基础价格');

            $form->display('created_at', '创建时间');
            $form->display('updated_at', '更新时间');
        });
    }
}
