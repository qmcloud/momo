<?php

namespace App\Admin\Controllers\Shop;

use App\Models\ShopTopic;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Widgets\Table;

class ShopTopicController extends Controller
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

            $content->header('主题&专题列表');
            $content->description('主题&专题管理');

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

            $content->header('主题&专题列表');
            $content->description('主题&专题管理');

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

            $content->header('新增主题&专题');
            $content->description('主题&专题管理');

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
        return Admin::grid(ShopTopic::class, function (Grid $grid) {
            $grid->model()->orderBy('sort_order', 'asc');
            $grid->id('序号')->sortable();
            $grid->title('主题标题');
            $grid->subtitle('短标题');
            $grid->avatar('主题图片')->image('', 75, 75);
            $grid->item_pic_url('元素图片')->image('', 75, 75);

            $grid->price_info('价格信息')->label('info');
            $grid->read_count('阅读量');
            $grid->scene_pic_url('展示图片')->image('', 75, 75);
            $grid->sort_order('主题排序');

            // 这里是多个信息一起显示
            $grid->column('主题表述')->expand(function ()  {
                $tab = new Tab();
                $box = new Box('主题表述',$this->content);
                $tab->add('主题表述', $box);
                return $tab;
            }, '主题表述');

            $grid->is_show('状态')
                ->select(ShopTopic::getStateDispayMap());

            $grid->filter(function ($filter) {
                $filter->like('title', '主题标题');
                $filter->equal('is_show', '状态')
                    ->radio(ShopTopic::getStateDispayMap());
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
        return Admin::form(ShopTopic::class, function (Form $form) {

            $form->display('id', '序号');

            $form->text('title', '主题标题')
                ->rules('required');

            $form->text('subtitle', '短标题')
                ->rules('required');

            $form->image('avatar', '主题图片')
                ->rules('required')
                ->uniqueName() ;
            $form->image('scene_pic_url', '展示图片')
                ->rules('required')
                ->uniqueName() ;

            $form->multipleImage('item_pic_url', '元素图片')
                ->uniqueName();

            $form->currency('price_info', '价格信息')
                ->symbol('￥');

            $form->editor('content', '主题表述');

            $form->number('read_count', '阅读量');

            $form->number('sort_order','排序')
                ->default(255);

            $form->radio('is_show', '是否展示')
                ->options(ShopTopic::getStateDispayMap())
                ->default(ShopTopic::STATE_SHOW);
        });
    }

}
