<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Callout;
use Encore\Admin\Widgets;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->row(function (Row $row) {

                $row->column(2, new Widgets\InfoBox('今日新增', 'users', 'aqua', '/demo/users', '1024'));
                $row->column(2, new Widgets\InfoBox('APP启动次数', 'mobile', 'green', '/demo/orders', '150%'));
                $row->column(2, new Widgets\InfoBox('活跃用户数', 'file', 'yellow', '/demo/articles', '2786'));
                $row->column(2, new Widgets\InfoBox('平均使用时长', 'align-justify', 'red', '/demo/files', '698726'));
                $row->column(2, new Widgets\InfoBox('总注册数', 'book', 'blue', '/demo/files', '698726'));
                $row->column(2, new Widgets\InfoBox('总收入', 'cny', 'yellow', '/demo/files', '698726'));

                $bar = view('admin.chartjs.bar');
                $row->column(1/3, new Box('Bar chart', $bar));

                $scatter = view('admin.chartjs.scatter');
                $row->column(1/3, new Box('Scatter chart', $scatter));

                $bar = view('admin.chartjs.line');
                $row->column(1/3, new Box('Line chart', $bar));

            })->row(function (Row $row) {

                $bar = view('admin.chartjs.doughnut');
                $row->column(1/3, new Box('Doughnut chart', $bar));

                $scatter = view('admin.chartjs.combo-bar-line');
                $row->column(1/3, new Box('Chart.js Combo Bar Line Chart', $scatter));

                $bar = view('admin.chartjs.line-stacked');
                $row->column(1/3, new Box('Chart.js Line Chart - Stacked Area', $bar));

            });
    }

    protected function info($url, $title)
    {
        $content = "<a href=\"{$url}\" target='_blank'>{$url}</a>";

        return new Callout($content, $title, 'info');
    }
}
