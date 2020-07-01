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
use Encore\Admin\Widgets\Box;
use App\Models\SpecialItem;

/**
 * 暂未使用
 * Class ModuleDController
 * @package App\Admin\Controllers\Module
 */
class ModuleDController extends Controller
{
    use ModelForm;

    public function index()
    {
        return Admin::content(function (Content $content) {


            $content->header('添加模板');
            $content->description('添加模板C');

            $content->row(function (Row $row) {
                $row->column(6, $this->getView());
                $row->column(6, function (Column $column) {
                    $form = $this->form();
                    $column->append($form);
                });
            });
        });

    }

    public function getView($id = 0){
        $specialItem = SpecialItem::find($id);
    	return view('admin.module.moduleC',compact('specialItem'));
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

            $content->header('管理模板C');
            $content->description('模板C');
            $content->row(function (Row $row) use ($id){
                $row->column(6, $this->getView($id));
                $row->column(6, function (Column $column) use ($id) {
                    $form = $this->form($id)->edit($id);
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
            $form->text('item_title','条目标题')->rules('required|min:2')->help('必填');
            $form->text('item_desc','条目描述')->rules('required|min:2')->help('必填');
            $form->number('sort', '排序')->default(255);
            // 轮播内容
            $form->embeds('item_data', '附加信息', function ($form) {
                $form->image('adv_img_1','1-推广图片')->help('必填 推荐 600*400')->uniqueName();//->rules('required')
                $form->text('adv_title_1','1-标题')->rules('required')->help('必填');
                $form->select('adv_type_1','1-操作类型')->options(SpecialItem::getItemDataTypes())->rules('required')->help('操作类型');
                $form->text('adv_data_1','1-操作数据')->rules('required')->help('对应于操作类型');
                $form->divide();

                $form->image('adv_img_2','2-推广图片')->help('推荐 600*400')->uniqueName();
                $form->text('adv_title_2','2-标题');
                $form->select('adv_type_2','2-操作类型')->options(SpecialItem::getItemDataTypes())->help('操作类型');
                $form->text('adv_data_2','2-操作数据')->help('对应于操作类型');
                $form->divide();

                $form->image('adv_img_3','3-推广图片')->help('推荐 600*400')->uniqueName();
                $form->text('adv_title_3','3-标题');
                $form->select('adv_type_3','3-操作类型')->options(SpecialItem::getItemDataTypes())->help('操作类型');
                $form->text('adv_data_3','3-操作数据')->help('对应于操作类型');
                $form->divide();
                $form->image('adv_img_4','4-推广图片')->help('推荐 600*400')->uniqueName();
                $form->text('adv_title_4','4-标题');
                $form->select('adv_type_4','4-操作类型')->options(SpecialItem::getItemDataTypes())->help('操作类型');
                $form->text('adv_data_4','4-操作数据')->help('对应于操作类型');
            });
            //$form->setAction(admin_base_path('module-adv'));
            $form->saving(function (Form $form) {
                $null_ignore =['adv_img_1','adv_img_2','adv_img_3','adv_img_4'];
                $item_data = $form->model()->item_data;
                $goal = $form->item_data;
                foreach($null_ignore as $v){
                    if(empty($form->$v) && isset($item_data[$v])){
                        $goal[$v] = $item_data[$v];
                    }
                }
                $form->item_data = $goal;
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
