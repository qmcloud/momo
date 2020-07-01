<?php

namespace App\Admin\Extensions\Tools;

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;
use Illuminate\Support\Facades\Request;

class CheckGoodsAction extends AbstractTool
{
    protected function script()
    {
        return <<<EOT
        
    var selectedRowsImg = function () {
    var selectedImg = '';
    $('.grid-row-checkbox:checked').each(function(){
        var imgsrc = $(this).parents('tr').find('.img-thumbnail').attr('src');
        var nowid = $(this).data('id')
        selectedImg +='<img src="'+imgsrc+'" class="img-circle" data-id="'+nowid+'" id="img_'+nowid+'">-';
    });

    return selectedImg;
    }
$(function(){
    var submitCheckoutGoods = $("#submitCheckoutGoods");
    var ids = selectedRows()
    var imgs = selectedRowsImg();
    $('.iCheck-helper').click(function () {
        setTimeout(function(){
            ids = selectedRows();
            imgs = selectedRowsImg();
            if(ids.length <= 0){
                submitCheckoutGoods.addClass('disabled');
            }else{
                submitCheckoutGoods.removeClass('disabled');
            }
        },200);
    });
    submitCheckoutGoods.click(function () {
        $('#item_data').val(ids);
        $('.checkedgoodsImg').html(imgs);
    });
})
EOT;
    }

    public function render()
    {
        Admin::script($this->script());

        return view('admin.tools.check_goods');
    }
}