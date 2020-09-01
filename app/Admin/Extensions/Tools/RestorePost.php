<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Grid\Tools\BatchAction;

class RestorePost extends BatchAction
{
    public function script()
    {
        return <<<EOT

$('{$this->getElementClass()}').on('click', function() {

    $.ajax({
        method: 'post',
        url: '{$this->resource}/restore',
        data: {
            _token:'{$this->getToken()}',
            ids: selectedRows()
        },
        success: function () {
            $.pjax.reload('#pjax-container');
            toastr.success('操作成功');
        }
    });
});

EOT;

    }
}
