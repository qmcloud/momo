<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;
use Illuminate\Support\Facades\Request;

class Messagekind extends AbstractTool
{
    public function script()
    {
        $url = Request::fullUrlWithQuery(['kind' => '_kind_']);

        return <<<EOT

$('input:radio.message-kind').change(function () {

    var url = "$url".replace('_kind_', $(this).val());

    $.pjax({container:'#pjax-container', url: url });

});

EOT;
    }

    public function render()
    {
        Admin::script($this->script());

        $options = [
            'inbox'   => 'Inbox',
            'outbox'  => 'Outbox',
        ];

        return view('admin.tools.message-kind', compact('options'));
    }
}
