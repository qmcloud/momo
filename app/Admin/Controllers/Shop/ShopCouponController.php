<?php

namespace App\Admin\Controllers\Shop;

use App\Models\ShopCoupon;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Widgets\Table;

class ShopCouponController extends Controller
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

            $content->header('优惠管理');
            $content->description('商品的优惠管理');

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

            $content->header('优惠编辑管理');
            $content->description('优惠券编辑');

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

            $content->header('新增优惠');
            $content->description('新增商品优惠卷页面');

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
        return Admin::grid(ShopCoupon::class, function (Grid $grid) {
            $grid->model()->orderBy('sort_order', 'desc');
            $grid->id('序号')->sortable();
            $grid->name('优惠券名称');
            $grid->icon('展示图片')->image('', 75, 75);

            $grid->status('优惠券状态')
                ->select(ShopCoupon::getStatusDispayMap());

            $grid->column('type')->display(function ($type) {
                return ShopCoupon::getTypeDispayMap()[$type];
            });
            $grid->type_money('减免额度')->display(function ($type_money) {
                $label ='元';
                if(ShopCoupon::TYPE_DISCOUNT){
                    $label = '折';
                }
                return $type_money.' '.$label;
            })->label('info');

            $grid->send_type('优惠券发放类型');
            $grid->min_amount('优惠使用最小金额');
            $grid->max_amount('优惠使用最大金额');
            $grid->min_goods_amount('优惠使用单个商品的最小金额');


             #这里是多个信息一起显示
             $grid->column('其他详细信息')->expand(function () {
                 $timeInfo = [
                     '优惠券发放开始时间：' . $this->send_start_date,
                     '优惠券发放截止时间：' . $this->send_end_date,
                 ];
                 if($this->expire_type == ShopCoupon::ESPIRE_TYPE_DAY){
                     $timeInfo = array_merge($timeInfo,[
                         '使用时间：' . '领取后'.$this->expire_day.'天有效',
                     ]);
                 }else{
                     $timeInfo = array_merge($timeInfo,[
                         '使用开始时间：' . $this->use_start_date,
                         '使用截止时间：' . $this->use_end_date,
                     ]);
                 }
                 $row_arr1 = [
                     $timeInfo,
                     [
                         '已领取个数：' . $this->reward_num,
                         '库存量：' . $this->total_num,
                         '每人限制领取个数：' . $this->limit_num,
                     ],
                     [
                         '简述：' . $this->brief,
                         '说明：' . $this->desc,
                     ],
                 ];
                 $table = new Table(['其他信息'], $row_arr1);
                 return $table;
             }, '其他详细信息');

            $grid->filter(function ($filter) {
                $filter->like('name', '优惠券名称');
                $filter->equal('status', '状态')
                    ->radio(ShopCoupon::getStatusDispayMap());
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
        return Admin::form(ShopCoupon::class, function (Form $form) {

            $form->display('id', '序号');

            $form->text('name', '优惠券名称')
                ->rules('required');

            $form->text('brief', '简述/副标题')
                ->rules('required');

            $form->image('icon', 'icon')
                ->rules('required')
                ->uniqueName()->help('建议120*120');

            $form->textarea('desc', '描述')
                ->rows(10);

            $form->select('type', '优惠券类型 1满减 2折扣')
                ->rules('required')
                ->options(ShopCoupon::getTypeDispayMap());

            $form->select('send_type', '优惠券发放类型')
                ->rules('required')
                ->options(ShopCoupon::$send_types);

            $form->currency('type_money', '减免金额/折扣')
                ->rules('required');

            $form->currency('min_amount', '优惠使用最小金额')
                ->rules('required');

            $form->currency('min_goods_amount', '优惠使用商品的最小金额')
                ->rules('required');

            $form->currency('max_amount', '优惠使用最大金额')
                ->rules('required');
            $form->number('total_num', '库存')->default(100);
            $form->number('limit_num', '每人限领')->default(1);
            $form->radio('status', '上架状态')
                ->options(ShopCoupon::getStatusDispayMap())
                ->default(ShopCoupon::STATUS_ON);
            $form->number('sort_order', '排序')->default(100);
            $form->datetime('send_start_date','优惠券发放开始时间')->format('YYYY-MM-DD HH:mm:ss');
            $form->datetime('send_end_date','优惠券发放截止时间')->format('YYYY-MM-DD HH:mm:ss');

            $form->radio('expire_type', '到期类型')
                ->options(ShopCoupon::getExpireDispayMap())
                ->default(ShopCoupon::ESPIRE_TYPE_DAY)->help('到期类型：1=领取后N天过期，2=指定有效期');

            $form->number('expire_day', '有效天数')->default(60)->help('只有选择了到期类型为领取后N天过期有效');;

            $form->datetime('use_start_date','使用开始时间')->format('YYYY-MM-DD HH:mm:ss')->help('只有选择了到期类型为指定有效期有效');;
            $form->datetime('use_end_date','使用截止时间')->format('YYYY-MM-DD HH:mm:ss')->help('只有选择了到期类型为指定有效期有效');;
            
        
        });
    }

}
