<?php

namespace App\Admin\Controllers\Shop;

use App\Models\ShopCategory;
use App\Models\ShopGoods;
use App\Models\ShopBrand;
use App\Models\ShopAttribute;
use App\Models\ShopSpecification;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Widgets\Table;
use App\Admin\Extensions\Tools\ShopGoodsHandle;

class ShopGoodsController extends Controller
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

            $content->header('商城商品列表');
            $content->description('商城商品管理');

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

            $content->header('商城商品列表');
            $content->description('商城商品管理');

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

            $content->header('新增商品');
            $content->description('商品管理');

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
        return Admin::grid(ShopGoods::class, function (Grid $grid) {
            $grid->model()->where('goods_type', ShopGoods::TYPE_NOMAL);
            $grid->model()->orderBy('sort_order', 'asc');
            $grid->id('序号')->sortable();
            $grid->primary_pic_url('商品主图')->image('', 75, 75);
            $grid->goods_name('商品名')->label('info')->limit(50);
            $grid->goods_sn('商品编号')->limit(50);
            $grid->category_id('商品分类')
                ->select(ShopCategory::getAllClasses(true));
            $grid->brand_id('品牌id')
                ->select(ShopBrand::getAllClasses(true));
            $grid->goods_number('商品库存量');
            $grid->sort_order('商品排序');

            $getListImg = $this;
            // 这里是多个信息一起显示
            $grid->column('其他信息')->expand(function () use ($getListImg) {
                $imgUrl = '<img src="%s" style="max-width:160px;max-height:160px" class="img img-thumbnail">';
                $row_arr1 = [
                    [
                        '商品主图：' . sprintf($imgUrl, config('filesystems.disks.oss.url') . '/' . $this->primary_pic_url),
                    ],
                    [
                        '商品列表图：' . $getListImg->getListImg($this->list_pic_url, $imgUrl),
                    ],
                    [
                        '商品关键词：' . $this->keywords,
                    ],
                    [
                        '商品摘要：' . $this->goods_brief,
                    ],
                    [
                        '专柜价格：￥' . $this->counter_price,
                        '附加价格：￥' . $this->extra_price,
                        '零售价格：￥' . $this->retail_price,
                        '单位价格，单价：￥' . $this->unit_price,
                        '运费：￥' . $this->freight_price,
                    ],
                ];
                $table = new Table(['其他信息'], $row_arr1);
                $tab = new Tab();
                $tab->add('商品基础信息', $table);

                $box = new Box('商品描述', $this->goods_desc);
                $tab->add('商品描述', $box);

                return $tab;
            }, '其他信息');


            $grid->actions(function ($actions) {
                // 添加操作
                $actions->append(new ShopGoodsHandle($actions->row));
            });
            $grid->filter(function ($filter) {
                $filter->like('goods_name', '商品名');
                $filter->in('class_id', '分类')
                    ->multipleSelect(ShopCategory::getAllClasses(true));
                $filter->equal('is_delete', '状态')
                    ->radio(ShopGoods::getDeleteDispayMap());
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
        return Admin::form(ShopGoods::class, function (Form $form) {

            $form->display('id', '序号');

            $form->text('goods_name', '商品名')
                ->rules('required');

            $form->select('category_id', '商品分类')
                ->rules('required')
                ->options(ShopCategory::selectOptions(true));
            $form->select('brand_id', '品牌id')
                ->rules('required')
                ->options(ShopBrand::getAllClasses(true));

            $form->currency('counter_price', '专柜价格')
                ->symbol('￥');

            $form->currency('extra_price', '附加价格')
                ->symbol('￥');

            $form->currency('retail_price', '零售价格')
                ->symbol('￥');

            $form->currency('unit_price', '单位价格，单价')
                ->symbol('￥');
            $form->currency('freight_price', '运费，单价')
                ->symbol('￥');

            $form->textarea('keywords', '商品关键词');
            $form->textarea('goods_brief', '商品摘要');
            $form->editor('goods_desc', '商品描述');
            $form->addSpecification('products', '添加规格', function () {
                return ShopSpecification::all();
            });
            $form->textarea('promotion_desc', '促销描述');
            $form->text('promotion_tag', '促销标签')
                ->value(' ');

            $form->image('primary_pic_url', '商品主图')
                ->rules('required')
                ->uniqueName()->help('建议300*300');

            $form->multipleImage('list_pic_url', '商品列表图')
                ->uniqueName();
            $form->number('goods_number', '库存')->default(10);
            $form->number('sell_volume', '销售量');
            $form->radio('is_on_sale', '上架状态')
                ->options(ShopGoods::getSaleDispayMap())
                ->default(ShopGoods::STATE_ON_SALE);
            $form->radio('is_delete', '删除状态')
                ->options(ShopGoods::getDeleteDispayMap())
                ->default(ShopGoods::STATE_NOT_DELETE);
            $form->radio('is_limited', '是否限购')
                ->options(ShopGoods::getLimitDispayMap())
                ->default(ShopGoods::STATE_SALE_NOT_LIMIT);
            $form->radio('is_hot', '是否推荐')
                ->options(ShopGoods::getRecommendDispayMap())
                ->default(ShopGoods::STATE_SALE_NOT_RECOMMEND);
            $form->radio('is_new', '是否新品')
                ->options(ShopGoods::getNewDispayMap())
                ->default(ShopGoods::STATE_SALE_NEW);
            $form->radio('is_vip_exclusive', '是否是会员专属')
                ->options(ShopGoods::getVipDispayMap())
                ->default(ShopGoods::STATE_NOT_VIP);
            $form->currency('vip_exclusive_price', '会员专享价')
                ->symbol('￥');
            $form->number('sort_order', '排序')
                ->default(255);

            $form->hasMany('goods_attribute', '添加属性', function (Form\NestedForm $form) {
                $form->select('attribute_id', '选择属性')->options(ShopAttribute::pluck('name', 'id'));
                $form->text('value', '属性值');
            });

//            $form->addSpecification('attribute_category', 'wewe');
//            $form->divide();
//            $form->hasMany('products', '添加规格', function (Form\NestedForm $form) {
//                $form->number('goods_number','库存')->default(255)->rules('required|min:1|max:20');
//                $form->currency('retail_price', '单价')
//                    ->symbol('￥');
//            });

            //保存前回调
//            $form->saving(function (Form $form) {
//                dd($form->products);
//            });


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
