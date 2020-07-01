<?php

namespace App\Admin\Controllers\Shop;

use App\Models\ActivityBargain;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Widgets\Table;
use Illuminate\Support\Facades\Input;

class BargainController extends Controller
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

            $content->header('砍价列表');
            $content->description('砍价管理');

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

            $content->header('砍价列表');
            $content->description('砍价管理');

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

            $content->header('新增砍价');
            $content->description('砍价管理');
            $goods_id = Input::get('goods_id', 0);
            $goods_name = Input::get('goods_name', '');
            $content->body($this->form($goods_id, $goods_name));
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(ActivityBargain::class, function (Grid $grid) {
            $grid->model()->orderBy('sort', 'asc');
            $grid->id('序号')->sortable();
            $grid->title('砍价活动名称')->label('info')->limit(50);
            $grid->goods_name('活动商品名')->label('info')->limit(100);
            $grid->start_time('砍价开启时间')->limit(50);
            $grid->stop_time('砍价结束时间')->limit(50);
            $grid->min_price('砍价商品最低价');
            $grid->total_num('总库存');
            $grid->sales('销量');
            $grid->look('砍价产品浏览量');
            $grid->share('砍价产品分享量');
            $grid->sort('排序');

            $getListImg = $this;
            // 这里是多个信息一起显示
            $grid->column('其他信息')->expand(function () use ($getListImg) {
                $imgUrl = '<img src="%s" style="max-width:160px;max-height:160px" class="img img-thumbnail">';
                $row_arr1 = [
                    [
                        '砍价活动简介：' . $this->info,
                    ],
                    [
                        '砍价产品轮播图：' . $getListImg->getListImg($this->images, $imgUrl),
                    ],
                    [
                        '用户每次砍价的最大金额：￥' . $this->bargain_max_price,
                        '用户每次砍价的最小金额：￥' . $this->bargain_min_price,
                        '可助力人数：￥' . $this->help_num,
                    ],
                ];

                $table = new Table(['其他信息'], $row_arr1);
                $tab = new Tab();
                $tab->add('砍价活动信息', $table);
                $box = new Box('砍价活动说明', $this->description);
                $tab->add('砍价详情/说明', $box);
                $box = new Box('砍价规则', $this->rule);
                $tab->add('规则', $box);
                return $tab;
            }, '其他信息');

            $grid->disableCreateButton();
            $grid->disableRowSelector();
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableDelete();
            });

            $grid->filter(function ($filter) {
                $filter->like('goods_name', '商品名');
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form($goods_id = 0, $goods_name = '')
    {
        return Admin::form(ActivityBargain::class, function (Form $form) use ($goods_id,$goods_name){
            $goods_ids_attribute = ['readonly' => 'readonly'];
            $goods_name_attribute = ['readonly' => 'readonly'];
            if($goods_id){
                $goods_ids_attribute = ['readonly' => 'readonly', 'value' => $goods_id];
            }
            if($goods_name){
                $goods_name_attribute = ['readonly' => 'readonly', 'value' => $goods_name];
            }
            $form->display('id', '序号');

            $form->text('goods_id', '商品ID')
                ->rules('required')->attribute($goods_ids_attribute);
            $form->text('goods_name', '商品名称')
                ->rules('required')->attribute($goods_name_attribute);
            $form->text('title', '砍价活动名称')
                ->rules('required');
            $form->text('info', '砍价活动简介')
                ->rules('required');
            $form->editor('description', '砍价详情/说明');

            $form->multipleImage('images', '砍价产品轮播图')
                ->uniqueName();
            $form->datetime('start_time', '砍价开启时间')->format('YYYY-MM-DD HH:mm:ss');
            $form->datetime('stop_time', '砍价结束时间')->format('YYYY-MM-DD HH:mm:ss');
            $form->number('total_num', '总库存')->default(100);
            $form->display('sales', '销量');
            $form->number('limit_num', '每次购买的砍价产品数量')->default(1);
            $form->currency('min_price', '活动价[砍价商品最低价]')
                ->symbol('￥');
            $form->currency('bargain_max_price', '用户每次砍价的最大金额')
                ->symbol('￥');
            $form->currency('bargain_min_price', '用户每次砍价的最小金额')
                ->symbol('￥');
            $form->number('help_num', '可助力人数')->default(5);
            $form->number('sort', '排序')
                ->default(0);
            $form->radio('status', '上架状态')
                ->options(ActivityBargain::getStateDisplayMap())
                ->default(ActivityBargain::STATE_ON);
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '更新时间');
        });
    }

    public function getListImg($list_pic_url, $modelUrl)
    {
        if (empty($list_pic_url) || empty($modelUrl)) {
            return '';
        }
        $url = '';
        foreach ($list_pic_url as $v) {
            $url .= sprintf($modelUrl, config('filesystems.disks.oss.url') . '/' . $v);
        }
        return $url;
    }
}

