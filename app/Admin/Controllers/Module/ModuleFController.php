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
use App\Logic\ModuleLogic;
use Illuminate\Support\Facades\Input;

class ModuleFController extends Controller
{
    use ModelForm;

    static $checkGoodsList;

    public function index()
    {
        return Admin::content(function (Content $content) {


            $content->header('添加模板F');
            $content->description('添加模板F');

            $content->row(function (Row $row) {
                $row->column(6, function (Column $column) {
                    $column->row($this->form());
                    $column->row($this->getView());
                });
                $row->column(6, checkGoods::goodsList());
            });
        });

    }

    public function getView($id = 0)
    {
        $goodsList = $this->getCheckGoodsList($id);
        return view('admin.module.moduleF',compact('goodsList')); //
    }


    /**
     * Edit interface
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('模板F');
            $content->description('模板F管理');
            $content->row(function (Row $row) use ($id) {
                $row->column(6, function (Column $column) use ($id){
                    $column->row($this->form($id)->edit($id));
                    $column->row($this->getView());
                });
                $row->column(6, checkGoods::goodsList(ModuleLogic::getModuleBCheckedGoodsById($id,true)));
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
    protected function form($id = '')
    {
        return Admin::form(SpecialItem::class, function (Form $form) use ($id){
            $form->setAction($this->getFormUrl($id));
            if(empty($id)){
                $form->setTitle('添加模板F元素');
            }else{
                $form->setTitle('修改模板F元素');
            }
            $form->hidden('special_id')->value(Input::get('specialId',0)); // 默认是首页
            $form->hidden('item_type')->value('moduleF'); // 默认是首页
            $form->text('item_title', '条目标题')->rules('required|min:2')->help('必填');
            $form->hidden('item_data', '商品数据')->attribute(['id' => 'item_data'])->help('必填');
            $form->text('item_desc', '条目描述')->rules('required|min:2')->help('必填');
            $form->number('sort', '排序')->default(255);

            $form->html($this->getGoodsListHtml($id), '已选择');
            $form->saved(function (Form $form) use ($id) {
                // 跳转页面
                admin_toastr('操作成功', 'success');
                if(Input::get('module','')){
                    return redirect(admin_base_path('module').'?id='.Input::get('specialId',0));
                }
                if(empty($id)){
                    return redirect(admin_base_path('module-f').'/'.$form->model()->id.'/edit');
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

    private function getGoodsListHtml($id){
        $goodsImgs = '';
        if($id){
            $goodsList = $this->getCheckGoodsList($id);
            foreach ($goodsList as $goods){
                $imgSrc = config('filesystems.disks.oss.url').'/'.$goods->primary_pic_url;
                $goodsImgs .="<img src='{$imgSrc}' class='img-circle' data-id='{$goods->id}' id='img_{$goods->id}'>-";
            }
        }
        $html  = <<<EOT
            <div class="box">
            <div class="box box-widget">
            <div class="box-header with-border" style="height:38px;">
              <div class="box-tools">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
              <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body">
               <div class="pull-left image checkedgoodsImg">
                    {$goodsImgs}
                </div>
            <!-- /.box-body -->
          </div>
          </div> </div>
EOT;
        return $html;
    }

    private function getCheckGoodsList($id = ''){
        if(!static::$checkGoodsList){
            static::$checkGoodsList = ModuleLogic::getModuleBCheckedGoodsById($id);
        }
        return static::$checkGoodsList;
    }

    protected function  getFormUrl($id){
        $url = '';
        if(!$id){
            $url = admin_base_path('module-f').'?module='.Input::get('module','').'&specialId='.Input::get('specialId',0);
        }else{
            $url = admin_base_path('module-f').'/'.$id.'?module='.Input::get('module','').'&specialId='.Input::get('specialId',0);
        }
        return $url;
    }
}
