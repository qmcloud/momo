<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;
use Illuminate\Support\Facades\Request;

class GridView extends AbstractTool
{
    public function script()
    {
        $url = Request::fullUrlWithQuery(['view' => '_view_']);

        return <<<EOT

$('input:radio.grid-view').change(function () {

    var url = "$url".replace('_view_', $(this).val());

    $.pjax({container:'#pjax-container', url: url });

});

EOT;
    }

    public function render()
    {
        Admin::script($this->script());

        $options = [
            'card' => 'image',
            'table' => 'list',
        ];

        return view('admin.tools.grid-view', compact('options'));
    }
}
