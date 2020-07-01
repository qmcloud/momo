<?php

namespace App\Admin\Controllers\Shop;

use App\Logic\AddressLogic;
use App\Models\ShopOrder;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Table;

class ShopOrderController extends Controller
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

            $content->header('订单列表');
            $content->description('订单管理');

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
        $content = Admin::content(function (Content $content) use ($id) {

            $content->header('订单信息修改');
            $content->description('订单管理');

            $content->body($this->form($id)->edit($id));
        });
        return $content;
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
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(ShopOrder::class, function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');
            $grid->disableRowSelector();
            $grid->disableCreateButton();
            $grid->actions(function ($actions){
                $actions->disableDelete();
            });
            $grid->id('序号')->sortable();
            $grid->order_sn('订单编号');
            $grid->order_price('订单金额');
            $grid->coupon_price('优惠金额');
            $grid->actual_price('支付金额');
            $grid->consignee('收件人')->limit(10);
            $grid->mobile('收件人手机')->editable();
            $grid->order_status('订单状态')->display(function ($status) {
                switch($status){
                    case ShopOrder::STATUS_WAIT_PAY:
                        $label = 'label-danger';
                        break;
                    case ShopOrder::STATUS_ALREADY_PAID:
                        $label = 'label-success';
                        break;
                    default:
                        $label = 'label-info';
                }
                $status = ShopOrder::getStatusDisplayMap()[$status];
                return "<span class='label {$label}'>{$status}</span>";
            });

            // 这里是多个信息一起显示
            $grid->column('购物信息')->expand(function (){
                $imgUrl = '<img src="%s" style="max-width:160px;max-height:160px" class="img img-thumbnail">';
                $goodsInfo = [];
                foreach($this->orderGoods as $goods){
                    $goodsInfo[] = [sprintf($imgUrl,config('filesystems.disks.oss.url').'/'.$goods->list_pic_url).' '.$goods->goods_name.' '.$goods->retail_price.' * '.$goods->number];
                }
                $row_arr1 = [
                    [
                        '收货人：' . $this->consignee,
                        '收件人手机：' . $this->mobile,
                        '收货地址：' . AddressLogic::getRegionNameById($this->province).' '.AddressLogic::getRegionNameById($this->city).' '.AddressLogic::getRegionNameById($this->district).' '.$this->address,
                    ],
                ];
                $row_arr1 = array_merge($goodsInfo,$row_arr1);
                $table = new Table(['购物信息'], $row_arr1);
                return $table;
            }, '购物信息');

            $grid->pay_time('付款时间');
            $grid->created_at('创建时间');
            $grid->updated_at('更新时间');
            $grid->filter(function ($filter) {
                $filter->like('order_sn', '订单编号');
                $filter->equal('mobile', '收件人手机');
                $filter->equal('order_status', '订单状态')->select(ShopOrder::getStatusDisplayMap());
                $filter->between('pay_time', '付款时间')->datetime();
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form($id='')
    {

        return Admin::form(ShopOrder::class, function (Form $form){
            $form->display('id', '序号');
            $form->display('order_sn', '订单编号');
            $form->display('order_status', '订单状态')->with(function ($value) {
                return ShopOrder::getStatusDisplayMap()[$value];
            });
            $form->display('goods_price', '商品总价');
            $form->display('order_price', '订单金额');
            $form->display('coupon_price', '优惠金额');
            $form->display('actual_price', '支付金额');
            $form->display('consignee', '收件人');
            $form->display('mobile', '收件人手机号');
            $form->display('uid', '下单用户ID');
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '更新时间');

        });
    }
}
