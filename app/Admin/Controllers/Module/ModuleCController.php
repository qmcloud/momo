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
use App\Models\Carousel;
use Illuminate\Support\Facades\Input;

class ModuleCController extends Controller
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
    protected function form($id ='')
    {
        return Admin::form(SpecialItem::class, function (Form $form)  use ($id){
            $form->setAction($this->getFormUrl($id));
            if(empty($id)){
                $form->setTitle('添加模板C');
            }else{
                $form->setTitle('修改模板C');
            }
            $form->hidden('special_id')->value(Input::get('specialId',0)); // 默认是首页
            $form->hidden('item_type')->value('moduleC');
            $form->text('item_title', '条目标题')->rules('required|min:2')->help('必填');
            $form->text('item_desc', '条目描述')->rules('required|min:2')->help('必填');
            $form->number('sort', '排序')->default(255);
            // 轮播内容
            $form->hasMany('carousels', '滑块元素', function (Form\NestedForm $form) {
                $form->text('carousel_title', '标题')
                    ->rules('required|max:100');
                $form->image('carousel_img', '图片')->uniqueName();
                $form->select('carousel_type', '操作类型')->options(SpecialItem::getItemDataTypes())->rules('required')->help('操作类型');
                $form->text('carousel_type_data', '操作数据')->rules('required')->help('对应于操作类型');
                $form->text('carousel_info', '显示内容')
                    ->rules('required|max:100');
                $form->switch('state', '状态')
                    ->states(Carousel::getStateDisplayConfig())
                    ->default(Carousel::STATE_NORMAL);
            });
            $form->saved(function (Form $form)  use ($id){
                // 跳转页面
                admin_toastr('操作成功', 'success');
                if(Input::get('module','')){
                    return redirect(admin_base_path('module').'?id='.Input::get('specialId',0));
                }
                if(empty($id)){
                    return redirect(admin_base_path('module-c').'/'.$form->model()->id.'/edit');
                }else{
                    return back();
                }
            });
            $form->tools(function (Form\Tools $tools) {
                // 去掉返回按钮
                $tools->disableBackButton();
                // 去掉跳转列表按钮
                $tools->disableListButton();
            });
        });
    }

    protected function  getFormUrl($id){
        $url = '';
        if(!$id){
            $url = admin_base_path('module-c').'?module='.Input::get('module','').'&specialId='.Input::get('specialId',0);
        }else{
            $url = admin_base_path('module-c').'/'.$id.'?module='.Input::get('module','').'&specialId='.Input::get('specialId',0);
        }
        return $url;
    }
}
