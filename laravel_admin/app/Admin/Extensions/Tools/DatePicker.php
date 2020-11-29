<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;
use Illuminate\Support\Facades\Request;

class DatePicker extends AbstractTool
{
    protected function script()
    {
        $url = Request::fullUrlWithQuery(['date' => '_date_']);

        return <<<EOT

$('#grid-date-picker').datetimepicker({format:'YYYY-MM-DD'}).on("dp.change", function () {

    var url = "$url".replace('_date_', $(this).val());

    $.pjax({container:'#pjax-container', url: url });

});

EOT;

    }

    public function render()
    {
        Admin::script($this->script());

        $date = request('date', date('Y-m-d'));

        return <<<EOT

<div class="input-group pull-right input-group-sm" style="width: 120px; margin-left:10px;">
  <div class="input-group-addon">
    <i class="fa fa-calendar"></i>
  </div>
  <input type="text" class="form-control" value="$date" id="grid-date-picker"  />
</div>

EOT;
    }
}