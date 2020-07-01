<?php

namespace App\Admin\Extensions\Tools;

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Admin;
use Illuminate\Support\Facades\Request;

class ShopGoodsHandle
{
    protected $row;
    public function __construct($row)
    {
        $this->row = $row;
    }
    protected function script()
    {
        return <<<EOT

EOT;
    }

    public function render()
    {
        $bargain = $this->row->bargain;
        if(empty($bargain)){
            $button_name = '开启砍价';
            $url = admin_base_path('bargain/create').'?goods_id='.$this->row['id'].'&goods_name='.$this->row['goods_name'];
        }else{
            $button_name = '砍价详情';
            $url = admin_base_path('bargain').'?id='.$bargain['id'];
        }
        Admin::script($this->script());
//        return "<button type='button' class='btn btn-block btn-default grid-check-row' data-id='{$this->id}'>添加</button>";
        return <<<EOF
    <div class="btn-group">
    <button type="button" class="btn btn-info dropdown-toggle"   data-toggle="dropdown" aria-expanded="false">操作</button>
    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only">Toggle Dropdown</span>
    </button>
    <ul class="dropdown-menu" role="menu">
        <li><a href="$url"> $button_name </a></li>
    </ul>
</div>
EOF;
        return view('admin.tools.goods_muti_button');
    }


    public function __toString()
    {
        return $this->render();
    }
}