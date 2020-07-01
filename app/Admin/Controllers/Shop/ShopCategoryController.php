<?php

namespace App\Admin\Controllers\Shop;

use App\Models\ShopCategory;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Row;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Tree;
use Encore\Admin\Widgets\Box;

class ShopCategoryController extends Controller
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

            $content->header('商城分类');
            $content->description('分类管理');

            $content->row(function (Row $row) {
                $row->column(6, $this->treeView()->render());
                $row->column(6, function (Column $column) {
                    $form = new \Encore\Admin\Widgets\Form();
                    $form->action(admin_base_path('shop-category'));
                    $form->select('parent_id','上级分类')->options(ShopCategory::selectOptions())->help('目前只支持一级分类，但留有多级接口。可根据需要联系开发人员进行功能构造');
                    $form->text('name','分类名称')->rules('required|min:2')->help('必填');
                    $form->text('front_name','分类简述')->rules('required|min:2')->help('必填');
                    $form->number('sort_order', '排序')->default(255);
                    $form->text('keywords','分类关键词')->rules('required')->help('必填');
                    $form->textarea('front_desc','分类描述');
                    $form->image('icon_url','分类图标')->help('必填 推荐 62*62')->rules('required')->uniqueName();
                    $form->image('banner_url','推广图片')->help('必填 推荐 600*400')->uniqueName();
                    $form->image('img_url','展示图片')->help('推荐 400*400')->rules('required')->uniqueName();
                    $form->display('created_at', '创建时间');
                    $form->display('updated_at', '更新时间');
                    $form->saved(function (Form $form) {
                        if($form->model()->id!=0){
                            $info = ShopCategory::find($form->model()->id);
                            if($info->parent_id){
                                $pinfo = ShopCategory::find($info->parent_id);
                                $info->level=$pinfo->level + 1;
                                $info->save();
                            }
                            
                        }
                    });
                    $column->append((new Box(trans('新增商城分类'), $form))->style('success'));
                });
            });
        });
    }

    /**
     * @return \Encore\Admin\Tree
     */
    protected function treeView()
    {
        return ShopCategory::tree(function (Tree $tree) {
            $tree->disableCreate();
            $tree->disableSave();
            $tree->disableRefresh();
            $tree->branch(function ($branch) {
                $src = config('filesystems.disks.oss.url') . '/' . $branch['icon_url'] ;
                $logo = "<img src='$src' style='max-width:30px;max-height:30px' class='img'/>";

                return "{$branch['id']} - {$branch['name']} $logo";
            });
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

            $content->header('header');
            $content->description('description');

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
        return Admin::form(ShopCategory::class, function (Form $form) {
            $form->select('parent_id','上级分类')->options(ShopCategory::selectOptions())->help('目前只支持一级分类，但留有多级接口。可根据需要联系开发人员进行功能构造');
            $form->text('name','分类名称')->rules('required|min:2');
            $form->text('front_name','分类简述')->rules('required|min:2')->help('必填');
            $form->number('sort_order', '排序')->default(255);
            $form->text('keywords','分类关键词')->help('必填')->rules('required');
            $form->textarea('front_desc','分类描述')->help('必填')->rules('required');
            $form->image('icon_url','分类图标')->help('必填 推荐 62*62')->rules('required')->uniqueName();
            $form->image('banner_url','推广图片')->help('必填 推荐 600*400')->rules('required')->uniqueName();
            $form->image('img_url','展示图片')->help('推荐 400*400')->uniqueName();
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '更新时间');
            $form->saved(function (Form $form) {
                if($form->model()->id!=0){
                    $info = ShopCategory::find($form->model()->id);
                    if($info->parent_id){
                        $pinfo = ShopCategory::find($info->parent_id);
                        $info->level=$pinfo->level + 1;
                        $info->save();
                    }
                }
            });
        });
    }
}
