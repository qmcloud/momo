<?php

namespace App\Admin\Extensions\Tools;

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Admin;
use Illuminate\Support\Facades\Request;

class CheckGoodsRow
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    protected function script()
    {
        return <<<SCRIPT
$(function(){
$('.grid-check-row').on('click', function () {
    var nowid = $(this).data('id');
    var dom = $('#img_'+nowid);
    if(dom.length>0){
        alert('已经添加');
        return;
    }
    var imgsrc = $(this).parents('tr').find('.img-thumbnail').attr('src');
    var selectedImgRow ='<img src="'+imgsrc+'" class="img-circle" data-id="'+nowid+'" id="img_'+nowid+'">-';
    $(this).parents('tr').find('.iCheck-helper').click();
    if($('#item_data').val()){
        var checkedIds = $('#item_data').val() + ','+nowid;
    }else{
        var checkedIds = nowid;
    }
   
    $('#item_data').val(checkedIds);
    $('.checkedgoodsImg').append(selectedImgRow);
});
});
SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());

        return "<button type='button' class='btn btn-block btn-default grid-check-row' data-id='{$this->id}'>添加</button>";

    }

    public function __toString()
    {
        return $this->render();
    }
}