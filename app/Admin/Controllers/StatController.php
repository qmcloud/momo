<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Admin\Controllers\Stat\DoStat;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;

class StatController extends Controller
{
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('统计');
            $content->description('数据控制台');

            $content->row(function (Row $row) {
                $row->column(6, function (Column $column) {
                    $box = new Box('商品分类统计', DoStat::goodsStatByClass());
                    $box->removable();
                    $box->collapsable();
                    $box->style('info');
                    $box->solid();
                    $column->append($box);
                });
                $row->column(6, function (Column $column) {
                    $box = new Box('专题商品统计', DoStat::goodsStatBySpec());
                    $box->removable();
                    $box->collapsable();
                    $box->style('info');
                    $box->solid();
                    $column->append($box);
                });
            });

        });
    }
}
