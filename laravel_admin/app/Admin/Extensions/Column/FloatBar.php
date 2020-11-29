<?php

namespace App\Admin\Extensions\Column;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Displayers\AbstractDisplayer;

class FloatBar extends AbstractDisplayer
{
    protected function script()
    {
        return <<<EOT

$('.grid-float-bar').closest('tr').mouseover(function () {
    $(this).find('.grid-float-bar').removeClass('hide');
});

$('.grid-float-bar').closest('tr').mouseout(function () {
    $(this).find('.grid-float-bar').addClass('hide');
});

EOT;

    }

    public function display()
    {
        Admin::script($this->script());

        return <<<EOT
<div style="width:220px;">
    <div class="hide grid-float-bar">
        <a class="btn btn-xs btn-default"><i class="fa fa-thumbs-up"></i> Up</a>
        <a class="btn btn-xs btn-default"><i class="fa fa-thumbs-down"></i> Down</a>
        <a class="btn btn-xs btn-default"><i class="fa fa-heart"></i> Like</a>
        <a class="btn btn-xs btn-default"><i class="fa fa-share"></i> Share</a>
    </div>
</div>
EOT;
    }
}