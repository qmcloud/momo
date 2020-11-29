<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;
use Illuminate\Support\Facades\Request;

class Trashed extends AbstractTool
{
    protected function script()
    {
        $url  = Request::fullUrlWithQuery(['trashed' => '_trashed_']);

        return <<<EOT

$('.grid-status').click(function () {
    var status = $(this).find('input')[0].checked ? 0 : 1;
    $.pjax({container:'#pjax-container', url: "$url".replace('_trashed_', status) });
});

EOT;

    }

    public function render()
    {
        Admin::script($this->script());

        $trashed = (Request::get('trashed') == 1) ? 'active' : '';
        $checked = (Request::get('trashed') == 1) ? 'checked' : '';

        return <<<EOT

<div class="btn-group" data-toggle="buttons">
    <label class="btn btn-twitter btn-sm grid-status $trashed">
        <input type="checkbox" $checked /> <i class="fa fa-trash"></i> Trashed
    </label>
</div>

EOT;
    }
}