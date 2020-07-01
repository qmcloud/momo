<?php

namespace App\Admin\Controllers\Module;

use App\Models\ShopGoods;
use App\Logic\ShopGoodsLogic;
use Encore\Admin\Controllers\ModelForm;
use App\Models\ShopCategory;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use App\Admin\Extensions\Tools\CheckGoodsBar;
use App\Admin\Extensions\Tools\CheckGoodsAction;
use App\Admin\Extensions\Tools\CheckGoodsRow;

class checkGoods
{
    use ModelForm;
    /**
     * 获取商品列表
     */
    public static function goodsList($checkedIds = [])
    {
        return Admin::grid(ShopGoods::class, function (Grid $grid) use($checkedIds) {
            $grid->model()->orderBy('sort_order', 'asc');
            $grid->model()->whereNotIn('id', $checkedIds);
            $grid->id('序号')->sortable();
            $grid->primary_pic_url('商品主图')->image('', 75, 75);
//            $grid->column('full_info','商品')->display(function () {
//                return $this->goods_name;
//            });
            $grid->goods_name('商品名')->label('info')->limit(50);
            $grid->filter(function (Grid\Filter $filter) {
                $filter->expand();
                $filter->like('goods_name', '商品名');
                $filter->in('class_id', '分类')
                    ->multipleSelect(ShopCategory::getAllClasses(true));
            });

            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
                // append一个操作
                $actions->append(new CheckGoodsRow($actions->getKey()));
            });

            $grid->disableExport();
            $grid->disableCreateButton();
            $grid->tools(function ($tools) {
                $tools->append(new CheckGoodsAction());
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
        });
    }


}
