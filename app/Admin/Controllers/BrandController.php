<?php

namespace App\Admin\Controllers;

use App\Models\ShopBrand;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class BrandController extends Controller
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

            $content->header('品牌列表');
            $content->description('品牌管理');

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

            $content->header('品牌信息修改');
            $content->description('品牌管理');

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

            $content->header('创建新的品牌');
            $content->description('品牌管理');

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
        return Admin::grid(ShopBrand::class, function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');
            $grid->id('序号')->sortable();
            $grid->name('品牌名称')->label('info')->sortable();
            $grid->pic_url('品牌图片')->image('', 100, 100);
            $grid->list_pic_url('品牌列表展示图片')->image('', 100, 100);
            $grid->simple_desc('品牌描述');
            $grid->sort_order('展示排序');
            $grid->floor_price('品牌显示的最低价');
            $grid->is_new('是否新增')
                ->select(ShopBrand::getTypeStateDispayMap());
            $grid->new_pic_url('新增展示图片')->image('', 100, 100);
            $grid->new_sort_order('新增逻辑下的排序');
            $grid->is_show('状态')
                ->select(ShopBrand::getStateDispayMap());

            $grid->created_at('创建时间');
            $grid->updated_at('更新时间');

            $grid->filter(function ($filter) {
                $filter->like('name', '品牌名称');
                $filter->equal('is_show', '状态')
                    ->radio(ShopBrand::getStateDispayMap());
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
        return Admin::form(ShopBrand::class, function (Form $form) {
            $form->display('id', '序号');
            $form->text('name', '品牌名称')
                ->rules('required');

            $form->multipleImage('list_pic_url', '品牌列表展示图片')
                ->rules('required');

            $form->textarea('simple_desc', '品牌描述');

            $form->image('pic_url', '品牌图片')
                ->rules('required')
                ->uniqueName();

            $form->number('sort_order','展示排序')
                ->default(255);

            $form->currency('floor_price', '品牌显示的最低价')
                ->symbol('￥');

            $form->radio('is_show', '状态')
                ->options(ShopBrand::getStateDispayMap())
                ->default(ShopBrand::STATE_SHOW);


            $form->image('new_pic_url', '新增展示图片')
                ->rules('required')
                ->uniqueName();

            $form->number('new_sort_order','新增逻辑下的排序')
                ->default(255);

            $form->radio('is_new', '是否新增')
                ->options(ShopBrand::getTypeStateDispayMap())
                ->default(ShopBrand::NEW_ADD);

        });
    }
}
