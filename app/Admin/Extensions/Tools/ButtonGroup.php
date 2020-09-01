<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Grid\Tools\AbstractTool;

class ButtonGroup extends AbstractTool
{
    public function render()
    {
        return <<<EOT

<div class="btn-group" data-toggle="buttons">
    <label class="btn btn-default btn-sm active">
        <input type="checkbox" checked autocomplete="off"> HOT
    </label>
    <label class="btn btn-default btn-sm">
        <input type="checkbox" autocomplete="off"> Recommend
    </label>
    <label class="btn btn-default btn-sm">
        <input type="checkbox" autocomplete="off"> Latest
    </label>
</div>

EOT;

    }
}
