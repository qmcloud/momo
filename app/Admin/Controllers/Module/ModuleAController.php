<?php

namespace App\Admin\Controllers\Module;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Row;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Tree;
use Illuminate\Support\MessageBag;
use App\Models\SpecialItem;
use App\Models\Carousel;
use Illuminate\Support\Facades\Input;

/**
 * 暂未使用到
 * Class ModuleAController
 * @package App\Admin\Controllers\Module
 */
class ModuleAController extends Controller
{
    use ModelForm;

    public function index()
    {
        return Admin::content(function (Content $content) {


            $content->header('添加模板A');
            $content->description('添加模板A');

            $content->row(function (Row $row) {

                $row->column(6, $this->getView());
                $row->column(6, function (Column $column) {
                    $form = new \Encore\Admin\Widgets\Form();
                    $form->action(admin_base_path('shop-category'));
                    $form->text('name','分类名称')->rules('required|min:2')->help('必填');
                    $form->text('front_name','分类简述')->rules('required|min:2')->help('必填');
                    $form->number('sort_order', '排序')->default(255);
                });
            });
        });

    }

    public function getView($id = 0)
    {
        return view('admin.module.moduleA'); //
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

            $content->header('首页轮播');
            $content->description('首页轮播管理');
            $content->row(function (Row $row) use ($id) {
                $row->column(6, $this->getAdv($id));
                $row->column(6, function (Column $column) use ($id) {
                    $form = $this->form()->edit($id);
                    $column->append($form);
                });
            });
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

            $content->header('header');
            $content->description('description');

            $content->body($this->form());
        });
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(SpecialItem::class, function (Form $form) {
            $form->setTitle('轮播');
            $form->hidden('special_id')->value(Input::get('specialId',0)); // 默认是首页
            $form->hidden('item_type')->value('adv'); // 默认是首页
            $form->text('item_title', '条目标题')->rules('required|min:2')->help('必填');
            $form->text('item_desc', '条目描述')->rules('required|min:2')->help('必填');
            $form->number('sort', '排序')->default(255);
            // 轮播内容
            $form->hasMany('carousels', '添加轮播', function (Form\NestedForm $form) {
                $form->text('carousel_title', '标题')
                    ->rules('required|max:100');
                $form->image('carousel_img', '图片')->uniqueName();
                $form->row(function ($row) {
                    $row->select('carousel_type', '操作类型')->options(SpecialItem::getItemDataTypes())->rules('required')->help('操作类型');
                    $row->text('carousel_type_data', '操作数据')->rules('required')->help('对应于操作类型');
                });

                $form->text('carousel_info', '显示内容')
                    ->rules('required|max:100');
                $form->switch('state', '状态')
                    ->states(Carousel::getStateDisplayConfig())
                    ->default(Carousel::STATE_NORMAL);
            });

            //$form->setAction(admin_base_path('module-adv'));
            $form->saving(function (Form $form) {
                // 跳转页面
//                dd($form->carousels);
            });
            $form->saved(function (Form $form) {
                // 跳转页面
                $success = new MessageBag([
                    'title' => '操作成功',
                    'message' => '操作成功了',
                ]);
                return back()->with(compact('success'));
            });
            $form->tools(function (Form\Tools $tools) {
                // 去掉返回按钮
                $tools->disableBackButton();
                // 去掉跳转列表按钮
                $tools->disableListButton();
            });
        });
    }
}
